<?php

namespace App\Filament\Widgets;

use App\Models\Siswa;
use App\Models\TransaksiPembayaran;
use App\Models\TagihanTahunan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalSiswa = Siswa::where('status_siswa', 'aktif')->count();

        $transaksiPending = TransaksiPembayaran::where('status', 'menunggu_verifikasi')->count();

        $pemasukanBulanIni = TransaksiPembayaran::where('status', 'terverifikasi')
            ->whereMonth('tanggal_transaksi', now()->month)
            ->whereYear('tanggal_transaksi', now()->year)
            ->sum('nominal_pokok');

        $totalTunggakan = TagihanTahunan::sum('sisa_tagihan');

        return [
            Stat::make('Total Siswa Aktif', $totalSiswa . ' Siswa')
                ->description('Terdaftar dalam sistem')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Perlu Verifikasi', $transaksiPending . ' Transaksi')
                ->description('Menunggu persetujuan bukti')
                ->descriptionIcon('heroicon-m-clock')
                ->color($transaksiPending > 0 ? 'warning' : 'success'),

            Stat::make('Pemasukan Bulan Ini', 'Rp ' . number_format($pemasukanBulanIni, 0, ',', '.'))
                ->description(now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Sisa Tagihan Siswa', 'Rp ' . number_format($totalTunggakan, 0, ',', '.'))
                ->description('Akumulasi tanggungan belum lunas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),
        ];
    }
}
