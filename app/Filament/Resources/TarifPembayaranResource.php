<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TarifPembayaranResource\Pages;
use App\Models\TarifPembayaran;
use App\Models\PosPembayaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TarifPembayaranResource extends Resource
{
    protected static ?string $model = TarifPembayaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Tarif per Tingkat';

    protected static ?string $pluralModelLabel = 'Tarif Pembayaran';

    protected static ?string $modelLabel = 'Tarif Pembayaran';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('pos_pembayaran_id')
                        ->label('Pos Tagihan')
                        ->relationship('posPembayaran', 'nama_pos')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\TextInput::make('tahun_ajaran')
                        ->label('Tahun Ajaran')
                        ->placeholder('Contoh: 2026/2027')
                        ->default('2026/2027')
                        ->required()
                        ->maxLength(20),

                    Forms\Components\Select::make('tingkat')
                        ->label('Tingkat Kelas')
                        ->options([
                            'X' => 'Tingkat X (Sepuluh)',
                            'XI' => 'Tingkat XI (Sebelas)',
                            'XII' => 'Tingkat XII (Dua Belas)',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('nominal')
                        ->label('Nominal Tarif (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('tingkat')
                    ->label('Tingkat')
                    ->colors([
                        'primary' => 'X',
                        'warning' => 'XI',
                        'success' => 'XII',
                    ]),

                Tables\Columns\TextColumn::make('posPembayaran.nama_pos')
                    ->label('Pos Pembayaran')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('nominal')
                    ->label('Nominal Tarif')
                    ->money('idr', locale: 'id')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tahun_ajaran')
                    ->options([
                        '2025/2026' => '2025/2026',
                        '2026/2027' => '2026/2027',
                        '2027/2028' => '2027/2028',
                    ]),
                Tables\Filters\SelectFilter::make('tingkat')
                    ->options([
                        'X' => 'Tingkat X',
                        'XI' => 'Tingkat XI',
                        'XII' => 'Tingkat XII',
                    ]),
                Tables\Filters\SelectFilter::make('pos_pembayaran_id')
                    ->label('Filter Pos')
                    ->relationship('posPembayaran', 'nama_pos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTarifPembayaran::route('/'),
            'create' => Pages\CreateTarifPembayaran::route('/create'),
            'edit' => Pages\EditTarifPembayaran::route('/{record}/edit'),
        ];
    }
}
