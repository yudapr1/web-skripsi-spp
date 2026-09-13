<?php

namespace App\Filament\Resources\TransaksiPembayaranResource\Pages;

use App\Filament\Resources\TransaksiPembayaranResource;
use App\Exports\LaporanTransaksiExport;
use App\Models\Kelas;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListTransaksiPembayaran extends ListRecords
{
    protected static string $resource = TransaksiPembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('💵 Kasir Loket (Bayar Tunai)')
                ->icon('heroicon-o-banknotes'),

            Actions\Action::make('exportExcel')
                ->label('📊 Export Rekap Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->default(now()->startOfMonth()),

                    Forms\Components\DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->default(now()),

                    Forms\Components\Select::make('status')
                        ->label('Status Transaksi')
                        ->options([
                            'terverifikasi' => 'Terverifikasi (Valid)',
                            'menunggu_verifikasi' => 'Menunggu Verifikasi',
                            'ditolak' => 'Ditolak',
                        ])
                        ->placeholder('Semua Status'),

                    Forms\Components\Select::make('metode_pembayaran')
                        ->label('Metode Pembayaran')
                        ->options([
                            'tunai' => 'Tunai (Loket)',
                            'transfer' => 'Transfer Bank',
                        ])
                        ->placeholder('Semua Metode'),

                    Forms\Components\Select::make('kelas_id')
                        ->label('Filter Kelas Siswa')
                        ->options(fn () => Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id'))
                        ->placeholder('Semua Kelas')
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    $startDate = $data['start_date'] ?? null;
                    $endDate = $data['end_date'] ?? null;
                    $status = $data['status'] ?? null;
                    $metode = $data['metode_pembayaran'] ?? null;
                    $kelasId = $data['kelas_id'] ?? null;

                    $fileName = 'Laporan_Transaksi_' . date('Ymd_His') . '.xlsx';

                    return Excel::download(
                        new LaporanTransaksiExport($startDate, $endDate, $status, $metode, $kelasId),
                        $fileName
                    );
                }),
        ];
    }
}

