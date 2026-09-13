<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelasResource\Pages;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Data Kelas';

    protected static ?string $pluralModelLabel = 'Data Kelas';

    protected static ?string $modelLabel = 'Kelas';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('nama_kelas')
                        ->label('Nama Kelas')
                        ->placeholder('Contoh: X TKJ 1, XI AKL 2')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\Select::make('tingkat')
                        ->label('Tingkat Kelas')
                        ->options([
                            'X' => 'Kelas X (Sepuluh)',
                            'XI' => 'Kelas XI (Sebelas)',
                            'XII' => 'Kelas XII (Dua Belas)',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('jurusan')
                        ->label('Program Keahlian / Jurusan')
                        ->placeholder('Contoh: Teknik Komputer & Jaringan, Akuntansi')
                        ->maxLength(100),

                    Forms\Components\Select::make('status')
                        ->label('Status Kelas')
                        ->options([
                            'aktif' => 'Aktif',
                            'nonaktif' => 'Non-Aktif',
                        ])
                        ->default('aktif')
                        ->required(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kelas')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('tingkat')
                    ->label('Tingkat')
                    ->colors([
                        'primary' => 'X',
                        'warning' => 'XI',
                        'success' => 'XII',
                    ]),

                Tables\Columns\TextColumn::make('jurusan')
                    ->label('Jurusan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('siswa_count')
                    ->label('Jumlah Siswa')
                    ->counts('siswa')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'aktif',
                        'danger' => 'nonaktif',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tingkat')
                    ->options([
                        'X' => 'Kelas X',
                        'XI' => 'Kelas XI',
                        'XII' => 'Kelas XII',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Non-Aktif',
                    ]),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }
}
