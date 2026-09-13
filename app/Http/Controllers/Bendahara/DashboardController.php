<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\PosPembayaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // 1. Kas hari ini
        $penerimaanHariIni = Transaction::whereDate('tanggal_bayar', $today)
            ->where('status_verifikasi', 'diverifikasi')
            ->sum('nominal_bayar');

        // 2. Kas bulan ini
        $penerimaanBulanIni = Transaction::whereMonth('tanggal_bayar', $thisMonth)
            ->whereYear('tanggal_bayar', $thisYear)
            ->where('status_verifikasi', 'diverifikasi')
            ->sum('nominal_bayar');

        // 3. Total Tunggakan Keseluruhan
        $totalTunggakan = Bill::where('status', '!=', 'lunas')
            ->sum(DB::raw('nominal_tagihan - nominal_terbayar'));

        // 4. Jumlah Siswa Aktif
        $totalSiswa = Student::where('status', 'aktif')->count();

        // 5. Pembayaran Transfer Menunggu Konfirmasi (Pending)
        $pendingTransfers = Transaction::with(['student.kelas', 'bill.tarifPembayaran.posPembayaran'])
            ->where('metode_pembayaran', 'transfer')
            ->where('status_verifikasi', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // 6. Transaksi Terbaru
        $recentTransactions = Transaction::with(['student.kelas', 'bill.tarifPembayaran.posPembayaran', 'verifier'])
            ->latest('tanggal_bayar')
            ->take(8)
            ->get();

        // 7. Data Grafik Pendapatan 6 Bulan Terakhir
        $chartMonths = [];
        $chartIncome = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->translatedFormat('M Y');
            $sum = Transaction::whereMonth('tanggal_bayar', $date->month)
                ->whereYear('tanggal_bayar', $date->year)
                ->where('status_verifikasi', 'diverifikasi')
                ->sum('nominal_bayar');

            $chartMonths[] = $monthName;
            $chartIncome[] = (float) $sum;
        }

        return view('bendahara.dashboard', compact(
            'penerimaanHariIni',
            'penerimaanBulanIni',
            'totalTunggakan',
            'totalSiswa',
            'pendingTransfers',
            'recentTransactions',
            'chartMonths',
            'chartIncome'
        ));
    }
}
