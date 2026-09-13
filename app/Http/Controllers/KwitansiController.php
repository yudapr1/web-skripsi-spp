<?php

namespace App\Http\Controllers;

use App\Models\Kwitansi;
use App\Models\PengaturanSekolah;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KwitansiController extends Controller
{
    /**
     * Cetak Kwitansi Resmi Pembayaran Siswa berformat PDF ber-kop sekolah
     */
    public function cetakPdf(Kwitansi $kwitansi)
    {
        $kwitansi->load([
            'transaksi.siswa.kelas',
            'transaksi.details.posPembayaran',
            'transaksi.tagihanTahunan.items.posPembayaran',
            'penerbit'
        ]);

        $transaksi = $kwitansi->transaksi;
        $siswa = $transaksi->siswa;

        // Keamanan: Jika user login adalah siswa, pastikan kwitansi miliknya sendiri
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->role === 'siswa') {
            if (!$currentUser->siswa || $currentUser->siswa->id !== $siswa->id) {
                abort(403, 'Akses kwitansi tidak sah.');
            }
        }

        $setting = PengaturanSekolah::first() ?? new PengaturanSekolah();

        // Hitung akumulasi sisa tagihan siswa di tahun ajaran bersangkutan
        $sisaTagihanSaatIni = $transaksi->tagihanTahunan?->sisa_tagihan ?? 0;

        $terbilang = $this->terbilang($transaksi->nominal_pokok) . ' Rupiah';

        $data = [
            'kwitansi' => $kwitansi,
            'transaksi' => $transaksi,
            'siswa' => $siswa,
            'setting' => $setting,
            'sisaTagihan' => $sisaTagihanSaatIni,
            'terbilang' => $terbilang,
        ];

        $pdf = Pdf::loadView('pdf.kwitansi', $data);
        $pdf->setPaper('a5', 'landscape');

        $fileName = 'Kwitansi_' . str_replace('/', '_', $kwitansi->nomor_kwitansi) . '.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Konversi nominal angka ke terbilang Bahasa Indonesia
     */
    private function terbilang($angka): string
    {
        $angka = abs((float)$angka);
        $baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $terbilang = '';

        if ($angka < 12) {
            $terbilang = ' ' . $baca[(int)$angka];
        } elseif ($angka < 20) {
            $terbilang = $this->terbilang($angka - 10) . ' Belas';
        } elseif ($angka < 100) {
            $terbilang = $this->terbilang($angka / 10) . ' Puluh' . $this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = ' Seratus' . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = $this->terbilang($angka / 100) . ' Ratus' . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = ' Seribu' . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = $this->terbilang($angka / 1000) . ' Ribu' . $this->terbilang($angka % 100);
        } elseif ($angka < 1000000000) {
            $terbilang = $this->terbilang($angka / 1000000) . ' Juta' . $this->terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $terbilang = $this->terbilang($angka / 1000000000) . ' Miliar' . $this->terbilang(fmod($angka, 1000000000));
        }

        return trim($terbilang);
    }

    /**
     * Unduh Berkas / Foto Bukti Pembayaran Siswa
     */
    public function downloadBukti(\App\Models\BuktiPembayaran $bukti)
    {
        $bukti->load(['transaksi.siswa']);
        $transaksi = $bukti->transaksi;
        $currentUser = auth()->user();

        // Keamanan: Siswa hanya dapat mengunduh bukti miliknya sendiri
        if ($currentUser && $currentUser->role === 'siswa') {
            if (!$currentUser->siswa || $currentUser->siswa->id !== $transaksi?->siswa_id) {
                abort(403, 'Akses unduh bukti transfer tidak sah.');
            }
        }

        if (!$bukti->file_path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($bukti->file_path)) {
            abort(404, 'File bukti transfer tidak ditemukan.');
        }

        $extension = pathinfo($bukti->file_path, PATHINFO_EXTENSION);
        $namaSiswa = \Illuminate\Support\Str::slug($transaksi?->siswa?->nama_lengkap ?? 'siswa');
        $downloadName = 'BuktiTransfer_' . ($transaksi?->nomor_transaksi ?? $bukti->id) . '_' . $namaSiswa . '.' . $extension;

        return \Illuminate\Support\Facades\Storage::disk('public')->download($bukti->file_path, $downloadName);
    }
}
