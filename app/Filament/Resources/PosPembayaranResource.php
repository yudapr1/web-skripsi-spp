<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PosPembayaranResource\Pages;
use App\Models\PosPembayaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PosPembayaranResource extends Resource
{
    protected static ?string $model = PosPembayaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Pos Pembayaran';

    protected static ?string $pluralModelLabel = 'Pos Pembayaran';

    protected static ?string $modelLabel = 'Pos Pembayaran';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('kode_pos')
                        ->label('Kode Pos Tagihan')
                        ->placeholder('Contoh: SPP, SERAGAM, INFAQ')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50),

                    Forms\Components\TextInput::make('nama_pos')
                        ->label('Nama Pos Pembayaran')
                        ->placeholder('Contoh: Uang SPP Bulanan, Seragam & Atribut')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Textarea::make('keterangan')
                        ->label('Deskripsi / Keterangan')
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Status Aktif')
                        ->default(true),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_pos')
                    ->label('Kode Pos')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('nama_pos')
                    ->label('Nama Pos Pembayaran')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
            'index' => Pages\ListPosPembayaran::route('/'),
            'create' => Pages\CreatePosPembayaran::route('/create'),
            'edit' => Pages\EditPosPembayaran::route('/{record}/edit'),
        ];
    }
}
