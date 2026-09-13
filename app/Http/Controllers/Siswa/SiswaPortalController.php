<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\TagihanTahunan;
use App\Models\TagihanItem;
use App\Models\TransaksiPembayaran;
use App\Models\TransaksiDetail;
use App\Models\BuktiPembayaran;
use App\Models\PengaturanSekolah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SiswaPortalController extends Controller
{
    /**
     * Helper untuk mengambil data profil siswa yang login
     */
    private function getSiswa(): Siswa
    {
        $user = Auth::user();
        if (!$user->siswa) {
            abort(403, 'Akun ini belum ditautkan dengan profil master data siswa.');
        }
        return $user->siswa->load('kelas');
    }

    /**
     * 1. Dashboard Siswa: Menampilkan profil, kartu metrik, progress bar, & riwayat singkat
     */
    public function dashboard()
    {
        $siswa = $this->getSiswa();

        // Ambil seluruh tagihan tahunan siswa
        $tagihans = TagihanTahunan::with(['items.posPembayaran'])
            ->where('siswa_id', $siswa->id)
            ->latest()
            ->get();

        $totalTagihan = $tagihans->sum('total_tagihan');
        $totalTerbayar = $tagihans->sum('total_terbayar');
        $totalTunggakan = $tagihans->sum('sisa_tagihan');

        $persentaseLunas = $totalTagihan > 0 ? min(100, round(($totalTerbayar / $totalTagihan) * 100)) : 0;

        // Tagihan yang belum lunas
        $activeTagihan = $tagihans->where('status', '!=', 'lunas')->first();

        // 5 Transaksi terakhir
        $recentTransactions = TransaksiPembayaran::with(['details.posPembayaran', 'kwitansi'])
            ->where('siswa_id', $siswa->id)
            ->latest('tanggal_transaksi')
            ->take(5)
            ->get();

        $setting = PengaturanSekolah::first() ?? new PengaturanSekolah();

        return view('siswa.dashboard', compact(
            'siswa',
            'tagihans',
            'totalTagihan',
            'totalTerbayar',
            'totalTunggakan',
            'persentaseLunas',
            'activeTagihan',
            'recentTransactions',
            'setting'
        ));
    }

    /**
     * 2. Halaman Rincian Tagihan & Form Cicilan (Itemized Allocation)
     */
    public function tagihan()
    {
        $siswa = $this->getSiswa();

        $tagihans = TagihanTahunan::with(['items.posPembayaran'])
            ->where('siswa_id', $siswa->id)
            ->orderBy('tahun_ajaran', 'desc')
            ->get();

        $setting = PengaturanSekolah::first() ?? new PengaturanSekolah();

        return view('siswa.tagihan', compact('siswa', 'tagihans', 'setting'));
    }

    /**
     * 3. Form Checkout Pembayaran Transfer & Generate Kode Unik
     */
    public function bayarForm(Request $request, TagihanTahunan $tagihan)
    {
        $siswa = $this->getSiswa();

        if ($tagihan->siswa_id !== $siswa->id) {
            abort(403, 'Akses tidak sah.');
        }

        if ($tagihan->status === 'lunas') {
            return redirect()->route('siswa.tagihan')->with('error', 'Tagihan untuk tahun ajaran ini telah lunas seluruhnya.');
        }

        $tagihan->load(['items.posPembayaran']);
        $setting = PengaturanSekolah::first() ?? new PengaturanSekolah();

        return view('siswa.bayar', compact('siswa', 'tagihan', 'setting'));
    }

    /**
     * 4. Proses Submit Pembayaran Transfer & Upload Bukti Pembayaran
     */
    public function prosesUploadBayar(Request $request, TagihanTahunan $tagihan)
    {
        $siswa = $this->getSiswa();

        if ($tagihan->siswa_id !== $siswa->id) {
            abort(403, 'Akses tidak sah.');
        }

        $request->validate([
            'alokasi' => ['required', 'array', 'min:1'],
            'alokasi.*.nominal' => ['nullable', 'numeric', 'min:0'],
            'nama_bank_pengirim' => ['nullable', 'string', 'max:100'],
            'nama_pemilik_rekening' => ['nullable', 'string', 'max:150'],
            'nomor_rekening_pengirim' => ['nullable', 'string', 'max:50'],
            'catatan_siswa' => ['nullable', 'string', 'max:500'],
            'bukti_transfer' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'bukti_transfer.required' => 'Foto atau file bukti transfer wajib diunggah.',
            'bukti_transfer.mimes' => 'Format bukti transfer harus berupa JPG, PNG, atau PDF.',
            'bukti_transfer.max' => 'Ukuran file bukti transfer maksimal 2MB.',
        ]);

        // Filter pos tagihan yang memiliki nominal bayar > 0
        $alokasiList = [];
        $totalNominalPokok = 0;

        foreach ($request->alokasi as $itemId => $data) {
            $nominal = (float)($data['nominal'] ?? 0);
            if ($nominal > 0) {
                $item = TagihanItem::where('id', $itemId)
                    ->where('tagihan_tahunan_id', $tagihan->id)
                    ->first();

                if ($item) {
                    if ($nominal > (float)$item->sisa_pos) {
                        return back()->withInput()->withErrors([
                            'alokasi' => "Nominal bayar untuk pos {$item->posPembayaran->nama_pos} melebihi sisa tagihan (Maks: Rp " . number_format($item->sisa_pos, 0, ',', '.') . ").",
                        ]);
                    }

                    $alokasiList[] = [
                        'tagihan_item_id' => $item->id,
                        'pos_pembayaran_id' => $item->pos_pembayaran_id,
                        'nominal_bayar' => $nominal,
                    ];
                    $totalNominalPokok += $nominal;
                }
            }
        }

        if ($totalNominalPokok <= 0) {
            return back()->withInput()->withErrors([
                'alokasi' => 'Pilih minimal satu pos tagihan dan tentukan nominal bayar (lebih dari Rp 0).',
            ]);
        }

        $totalTransfer = $totalNominalPokok;

        // Upload file bukti transfer
        $file = $request->file('bukti_transfer');
        $filePath = $file->store('bukti_transfer', 'public');

        DB::transaction(function () use ($siswa, $tagihan, $totalNominalPokok, $totalTransfer, $request, $filePath, $file, $alokasiList) {
            // 1. Buat Header Transaksi Pembayaran (status: menunggu_verifikasi)
            $nomorTransaksi = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            $transaksi = TransaksiPembayaran::create([
                'nomor_transaksi' => $nomorTransaksi,
                'siswa_id' => $siswa->id,
                'tagihan_tahunan_id' => $tagihan->id,
                'tanggal_transaksi' => now(),
                'metode_pembayaran' => 'transfer',
                'nominal_pokok' => $totalNominalPokok,
                'kode_unik' => 0,
                'total_transfer' => $totalTransfer,
                'status' => 'menunggu_verifikasi',
                'catatan_siswa' => $request->catatan_siswa,
            ]);

            // 2. Buat Detail Transaksi Pembayaran
            foreach ($alokasiList as $alokasi) {
                TransaksiDetail::create([
                    'transaksi_pembayaran_id' => $transaksi->id,
                    'tagihan_item_id' => $alokasi['tagihan_item_id'],
                    'pos_pembayaran_id' => $alokasi['pos_pembayaran_id'],
                    'nominal_bayar' => $alokasi['nominal_bayar'],
                ]);
            }

            // 3. Simpan Bukti Pembayaran
            BuktiPembayaran::create([
                'transaksi_pembayaran_id' => $transaksi->id,
                'nama_bank_pengirim' => $request->nama_bank_pengirim,
                'nama_pemilik_rekening' => $request->nama_pemilik_rekening,
                'nomor_rekening_pengirim' => $request->nomor_rekening_pengirim,
                'file_path' => $filePath,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_at' => now(),
            ]);
        });

        return redirect()->route('siswa.riwayat')
            ->with('success', 'Bukti pembayaran berhasil dikirim! Silakan menunggu verifikasi dari Bendahara Sekolah.');
    }

    /**
     * 5. Riwayat Transaksi Siswa & Download Kwitansi PDF
     */
    public function riwayat()
    {
        $siswa = $this->getSiswa();

        $transactions = TransaksiPembayaran::with(['details.posPembayaran', 'buktiPembayaran', 'kwitansi', 'verifier'])
            ->where('siswa_id', $siswa->id)
            ->orderBy('tanggal_transaksi', 'desc')
            ->paginate(10);

        return view('siswa.riwayat', compact('siswa', 'transactions'));
    }

    /**
     * 6. Cetak Kwitansi Siswa
     */
    public function cetakKuitansi(TransaksiPembayaran $transaction)
    {
        $siswa = $this->getSiswa();

        if ($transaction->siswa_id !== $siswa->id) {
            abort(403, 'Akses tidak sah.');
        }

        if ($transaction->kwitansi) {
            return redirect()->route('kwitansi.pdf', $transaction->kwitansi->id);
        }

        return redirect()->route('siswa.riwayat')->with('error', 'Kwitansi belum diterbitkan untuk transaksi ini.');
    }
}

