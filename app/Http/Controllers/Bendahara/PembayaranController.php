<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $selectedStudent = null;
        $bills = collect();
        $history = collect();

        if ($request->filled('nisn_or_nis')) {
            $keyword = trim($request->nisn_or_nis);
            $selectedStudent = Student::with(['kelas', 'user'])
                ->where('nisn', $keyword)
                ->orWhere('nis', $keyword)
                ->first();

            if ($selectedStudent) {
                $bills = Bill::with(['tarifPembayaran.posPembayaran'])
                    ->where('student_id', $selectedStudent->id)
                    ->orderBy('tahun', 'desc')
                    ->orderBy('bulan', 'asc')
                    ->get();

                $history = Transaction::with(['bill.tarifPembayaran.posPembayaran', 'verifier'])
                    ->where('student_id', $selectedStudent->id)
                    ->orderBy('tanggal_bayar', 'desc')
                    ->get();
            }
        }

        $allStudents = Student::with('kelas')->where('status', 'aktif')->orderBy('nama_lengkap')->get();

        return view('bendahara.pembayaran.index', compact('selectedStudent', 'bills', 'history', 'allStudents'));
    }

    public function bayarTunai(Request $request)
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'nominal_bayar' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $bill = Bill::findOrFail($request->bill_id);
        $sisa = $bill->nominal_tagihan - $bill->nominal_terbayar;

        if ($request->nominal_bayar > $sisa) {
            return back()->with('error', 'Nominal bayar melebihi sisa tagihan (Maks: Rp ' . number_format($sisa, 0, ',', '.') . ').');
        }

        $transaction = null;

        DB::transaction(function () use ($bill, $request, &$transaction) {
            $kode = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $transaction = Transaction::create([
                'kode_transaksi' => $kode,
                'bill_id' => $bill->id,
                'student_id' => $bill->student_id,
                'user_id' => Auth::id(),
                'nominal_bayar' => $request->nominal_bayar,
                'metode_pembayaran' => 'tunai',
                'status_verifikasi' => 'diverifikasi',
                'tanggal_bayar' => now(),
                'keterangan' => $request->keterangan ?: 'Pembayaran Tunai di Loket Kasir',
            ]);

            $newTerbayar = $bill->nominal_terbayar + $request->nominal_bayar;
            $status = $newTerbayar >= $bill->nominal_tagihan ? 'lunas' : 'sebagian';

            $bill->update([
                'nominal_terbayar' => $newTerbayar,
                'status' => $status,
            ]);
        });

        return back()->with('success', 'Pembayaran berhasil disimpan. Kode Transaksi: ' . $transaction->kode_transaksi)
            ->with('last_trx_id', $transaction->id);
    }

    public function verifikasiIndex()
    {
        $transfers = Transaction::with(['student.kelas', 'bill.tarifPembayaran.posPembayaran'])
            ->where('metode_pembayaran', 'transfer')
            ->orderByRaw("CASE WHEN status_verifikasi = 'pending' THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('bendahara.pembayaran.verifikasi', compact('transfers'));
    }

    public function prosesVerifikasi(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:diverifikasi,ditolak',
            'catatan' => 'nullable|string',
        ]);

        if ($transaction->status_verifikasi !== 'pending') {
            return back()->with('error', 'Transaksi ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($request, $transaction) {
            $bill = $transaction->bill;

            if ($request->status === 'diverifikasi') {
                $newTerbayar = $bill->nominal_terbayar + $transaction->nominal_bayar;
                $status = $newTerbayar >= $bill->nominal_tagihan ? 'lunas' : 'sebagian';

                $bill->update([
                    'nominal_terbayar' => $newTerbayar,
                    'status' => $status,
                ]);
            }

            $transaction->update([
                'status_verifikasi' => $request->status,
                'user_id' => Auth::id(),
                'keterangan' => $request->catatan ?: ($request->status === 'diverifikasi' ? 'Bukti transfer telah diverifikasi' : 'Transfer ditolak'),
            ]);
        });

        return back()->with('success', 'Status transaksi berhasil diubah menjadi: ' . strtoupper($request->status));
    }

    public function cetakKuitansi(Transaction $transaction)
    {
        $transaction->load(['student.kelas', 'bill.tarifPembayaran.posPembayaran', 'verifier']);
        $setting = SchoolSetting::first() ?? new SchoolSetting();

        return view('bendahara.pembayaran.kuitansi', compact('transaction', 'setting'));
    }
}
