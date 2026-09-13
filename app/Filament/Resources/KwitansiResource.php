<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KwitansiResource\Pages;
use App\Models\Kwitansi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KwitansiResource extends Resource
{
    protected static ?string $model = Kwitansi::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Daftar Kwitansi';

    protected static ?string $pluralModelLabel = 'Kwitansi Pembayaran';

    protected static ?string $modelLabel = 'Kwitansi';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_kwitansi')
                    ->label('Nomor Kwitansi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('tanggal_terbit')
                    ->label('Tanggal Terbit')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('transaksi.siswa.nama_lengkap')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('transaksi.siswa.kelas.nama_kelas')
                    ->label('Kelas'),

                Tables\Columns\TextColumn::make('transaksi.nominal_pokok')
                    ->label('Nominal Pembayaran')
                    ->money('idr', locale: 'id')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('transaksi.metode_pembayaran')
                    ->label('Metode')
                    ->colors([
                        'info' => 'tunai',
                        'purple' => 'transfer',
                    ]),

                Tables\Columns\TextColumn::make('penerbit.name')
                    ->label('Diterbitkan Oleh'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('cetak')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->url(fn (Kwitansi $record): string => route('kwitansi.pdf', $record->id))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKwitansi::route('/'),
        ];
    }
}
