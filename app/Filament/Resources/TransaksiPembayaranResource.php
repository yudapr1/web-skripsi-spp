<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiPembayaranResource\Pages;
use App\Models\TransaksiPembayaran;
use App\Models\Siswa;
use App\Models\TagihanTahunan;
use App\Models\TagihanItem;
use App\Models\Kwitansi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

use Filament\Infolists;
use Filament\Infolists\Infolist;

class TransaksiPembayaranResource extends Resource
{
    protected static ?string $model = TransaksiPembayaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Transaksi & Verifikasi';

    protected static ?string $pluralModelLabel = 'Transaksi Pembayaran';

    protected static ?string $modelLabel = 'Transaksi Pembayaran';

    protected static ?int $navigationSort = 1;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Transaksi')->schema([
                    Infolists\Components\TextEntry::make('nomor_transaksi')
                        ->label('Nomor Transaksi')
                        ->weight('bold')
                        ->copyable(),

                    Infolists\Components\TextEntry::make('tanggal_transaksi')
                        ->label('Tanggal Transaksi')
                        ->dateTime('d F Y, H:i')
                        ->color('gray'),

                    Infolists\Components\TextEntry::make('metode_pembayaran')
                        ->label('Metode Pembayaran')
                        ->badge()
                        ->colors([
                            'info' => 'tunai',
                            'purple' => 'transfer',
                        ]),

                    Infolists\Components\TextEntry::make('status')
                        ->label('Status Transaksi')
                        ->badge()
                        ->colors([
                            'warning' => 'menunggu_verifikasi',
                            'success' => 'terverifikasi',
                            'danger' => 'ditolak',
                            'secondary' => 'dibatalkan',
                        ]),

                    Infolists\Components\TextEntry::make('nominal_pokok')
                        ->label('Nominal Pokok Tagihan')
                        ->money('idr', locale: 'id')
                        ->weight('bold'),

                    Infolists\Components\TextEntry::make('total_transfer')
                        ->label('Total Transfer')
                        ->money('idr', locale: 'id')
                        ->color('success')
                        ->weight('bold'),

                    Infolists\Components\TextEntry::make('kwitansi.nomor_kwitansi')
                        ->label('Nomor Kwitansi Resmi')
                        ->placeholder('Belum terbit / belum diverifikasi')
                        ->copyable(),

                    Infolists\Components\TextEntry::make('verifier.name')
                        ->label('Diverifikasi Oleh')
                        ->placeholder('-'),
                ])->columns(4),

                Infolists\Components\Section::make('Identitas Siswa')->schema([
                    Infolists\Components\TextEntry::make('siswa.nama_lengkap')
                        ->label('Nama Lengkap Siswa')
                        ->weight('bold'),

                    Infolists\Components\TextEntry::make('siswa.nisn')
                        ->label('NISN'),

                    Infolists\Components\TextEntry::make('siswa.nis')
                        ->label('NIS'),

                    Infolists\Components\TextEntry::make('siswa.kelas.nama_kelas')
                        ->label('Kelas & Jurusan')
                        ->formatStateUsing(fn ($record) => ($record->siswa?->kelas?->nama_kelas ?? '-') . ' (' . ($record->siswa?->kelas?->jurusan ?? '-') . ')'),

                    Infolists\Components\TextEntry::make('tagihanTahunan.nomor_tagihan')
                        ->label('Nomor Tagihan Tahunan'),

                    Infolists\Components\TextEntry::make('tagihanTahunan.tahun_ajaran')
                        ->label('Tahun Ajaran'),
                ])->columns(3),

                Infolists\Components\Section::make('Rincian Alokasi Pembayaran per Pos Tagihan')->schema([
                    Infolists\Components\RepeatableEntry::make('details')
                        ->label('')
                        ->schema([
                            Infolists\Components\TextEntry::make('posPembayaran.nama_pos')
                                ->label('Pos Pembayaran')
                                ->weight('bold'),

                            Infolists\Components\TextEntry::make('nominal_bayar')
                                ->label('Nominal yang Dibayarkan')
                                ->money('idr', locale: 'id')
                                ->weight('bold')
                                ->color('primary'),

                            Infolists\Components\TextEntry::make('tagihanItem.sisa_pos')
                                ->label('Sisa Tagihan Pos Saat Ini')
                                ->money('idr', locale: 'id')
                                ->color('danger'),
                        ])
                        ->columns(3)
                        ->columnSpanFull(),
                ]),

                Infolists\Components\Section::make('Bukti Pembayaran & Catatan')->schema([
                    Infolists\Components\ImageEntry::make('buktiPembayaran.file_path')
                        ->label('Foto Bukti Transfer Bank')
                        ->disk('public')
                        ->visibility('public')
                        ->height(300)
                        ->placeholder('Tidak ada foto bukti (pembayaran tunai di loket).')
                        ->columnSpanFull(),

                    Infolists\Components\TextEntry::make('buktiPembayaran.nama_bank_pengirim')
                        ->label('Bank Pengirim')
                        ->placeholder('-'),

                    Infolists\Components\TextEntry::make('buktiPembayaran.nama_pemilik_rekening')
                        ->label('Nama Pemilik Rekening')
                        ->placeholder('-'),

                    Infolists\Components\TextEntry::make('buktiPembayaran.nomor_rekening_pengirim')
                        ->label('No. Rekening Pengirim')
                        ->placeholder('-'),

                    Infolists\Components\TextEntry::make('catatan_bendahara')
                        ->label('Catatan Bendahara')
                        ->placeholder('Tidak ada catatan khusus.')
                        ->columnSpanFull(),
                ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_transaksi')
                    ->label('No. Transaksi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('siswa.kelas.nama_kelas')
                    ->label('Kelas'),

                Tables\Columns\BadgeColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->colors([
                        'info' => 'tunai',
                        'purple' => 'transfer',
                    ]),

                Tables\Columns\TextColumn::make('nominal_pokok')
                    ->label('Nominal Pokok')
                    ->money('idr', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_transfer')
                    ->label('Total Pembayaran')
                    ->money('idr', locale: 'id')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'menunggu_verifikasi',
                        'success' => 'terverifikasi',
                        'danger' => 'ditolak',
                        'secondary' => 'dibatalkan',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'terverifikasi' => 'Terverifikasi (Valid)',
                        'ditolak' => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('metode_pembayaran')
                    ->options([
                        'tunai' => 'Tunai (Loket)',
                        'transfer' => 'Transfer Bank',
                    ]),
            ])
            ->actions([
                // Action Lihat Bukti & Verifikasi
                Tables\Actions\Action::make('verifikasi')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'menunggu_verifikasi')
                    ->form([
                        Forms\Components\ViewField::make('bukti_preview')
                            ->view('filament.components.bukti-preview')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('keputusan')
                            ->label('Keputusan Verifikasi')
                            ->options([
                                'terverifikasi' => 'Setujui Pembayaran (Valid)',
                                'ditolak' => 'Tolak Pembayaran (Tidak Sesuai)',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('catatan_bendahara')
                            ->label('Catatan / Alasan')
                            ->placeholder('Masukkan alasan jika ditolak, atau catatan tambahan'),
                    ])
                    ->action(function (TransaksiPembayaran $record, array $data): void {
                        $keputusan = $data['keputusan'];
                        $catatan = $data['catatan_bendahara'] ?? null;

                        DB::transaction(function () use ($record, $keputusan, $catatan) {
                            if ($keputusan === 'terverifikasi') {
                                // 1. Update status transaksi
                                $record->update([
                                    'status' => 'terverifikasi',
                                    'catatan_bendahara' => $catatan,
                                    'verified_by' => Auth::id(),
                                    'verified_at' => now(),
                                ]);

                                // 2. Potong nominal tagihan item
                                foreach ($record->details as $detail) {
                                    $tagihanItem = $detail->tagihanItem;
                                    $tagihanItem->nominal_terbayar += $detail->nominal_bayar;
                                    $tagihanItem->sisa_pos = max(0, $tagihanItem->nominal_pos - $tagihanItem->nominal_terbayar);
                                    $tagihanItem->status = $tagihanItem->sisa_pos == 0 ? 'lunas' : 'sebagian';
                                    $tagihanItem->save();
                                }

                                // 3. Update total header tagihan tahunan
                                $tagihan = $record->tagihanTahunan;
                                $tagihan->total_terbayar = $tagihan->items()->sum('nominal_terbayar');
                                $tagihan->sisa_tagihan = max(0, $tagihan->total_tagihan - $tagihan->total_terbayar);
                                $tagihan->status = $tagihan->sisa_tagihan == 0 ? 'lunas' : ($tagihan->total_terbayar > 0 ? 'sebagian' : 'belum_lunas');
                                $tagihan->save();

                                // 4. Terbitkan Kwitansi Otomatis
                                Kwitansi::create([
                                    'transaksi_pembayaran_id' => $record->id,
                                    'nomor_kwitansi' => 'KWT/' . date('Y/m/') . str_pad($record->id, 4, '0', STR_PAD_LEFT),
                                    'tanggal_terbit' => now(),
                                    'diterbitkan_oleh' => Auth::id() ?? 1,
                                ]);
                            } else {
                                $record->update([
                                    'status' => 'ditolak',
                                    'catatan_bendahara' => $catatan,
                                    'verified_by' => Auth::id(),
                                    'verified_at' => now(),
                                ]);
                            }
                        });

                        Notification::make()
                            ->title('Proses Selesai')
                            ->body($keputusan === 'terverifikasi' ? 'Pembayaran berhasil diverifikasi dan kwitansi diterbitkan!' : 'Pembayaran telah ditolak.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('cetak_kwitansi')
                    ->label('Cetak Kwitansi')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->visible(fn ($record) => $record->status === 'terverifikasi' && $record->kwitansi !== null)
                    ->url(fn ($record) => route('kwitansi.pdf', $record->kwitansi->id))
                    ->openUrlInNewTab(),

                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksiPembayaran::route('/'),
            'create' => Pages\CreateKasirPembayaranTunai::route('/create'),
        ];
    }
}
