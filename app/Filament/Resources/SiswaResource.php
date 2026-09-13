<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Models\Siswa;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Data Siswa';

    protected static ?string $pluralModelLabel = 'Data Siswa';

    protected static ?string $modelLabel = 'Siswa';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akademik & Identitas')->schema([
                    Forms\Components\TextInput::make('nisn')
                        ->label('NISN')
                        ->placeholder('10 Digit NISN')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20),

                    Forms\Components\TextInput::make('nis')
                        ->label('NIS')
                        ->placeholder('Nomor Induk Siswa')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20),

                    Forms\Components\TextInput::make('nama_lengkap')
                        ->label('Nama Lengkap Siswa')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('kelas_id')
                        ->label('Kelas')
                        ->relationship('kelas', 'nama_kelas')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->options([
                            'L' => 'Laki-Laki',
                            'P' => 'Perempuan',
                        ])
                        ->required(),

                    Forms\Components\Select::make('status_siswa')
                        ->label('Status Siswa')
                        ->options([
                            'aktif' => 'Aktif',
                            'lulus' => 'Lulus',
                            'pindah' => 'Pindah',
                            'keluar' => 'Keluar',
                        ])
                        ->default('aktif')
                        ->required(),
                ])->columns(2),

                Forms\Components\Section::make('Biodata & Kontak')->schema([
                    Forms\Components\TextInput::make('tempat_lahir')
                        ->label('Tempat Lahir')
                        ->maxLength(100),

                    Forms\Components\DatePicker::make('tanggal_lahir')
                        ->label('Tanggal Lahir (Untuk Verifikasi/Klaim Akun Siswa)')
                        ->displayFormat('d/m/Y')
                        ->required(),

                    Forms\Components\TextInput::make('nomor_telepon')
                        ->label('Nomor WhatsApp / HP Siswa')
                        ->tel()
                        ->maxLength(20),

                    Forms\Components\TextInput::make('nomor_telepon_wali')
                        ->label('Nomor WhatsApp / HP Orang Tua / Wali')
                        ->tel()
                        ->maxLength(20),

                    Forms\Components\Textarea::make('alamat')
                        ->label('Alamat Lengkap')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status_siswa')
                    ->label('Status')
                    ->colors([
                        'success' => 'aktif',
                        'warning' => 'lulus',
                        'danger' => fn ($state) => in_array($state, ['pindah', 'keluar']),
                    ]),

                Tables\Columns\IconColumn::make('user_id')
                    ->label('Akun Login')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record) => $record->user_id ? 'Sudah Aktif / Terklaim' : 'Belum Registrasi Mandiri'),

                Tables\Columns\TextColumn::make('nomor_telepon')
                    ->label('No. Telepon')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kelas_id')
                    ->label('Filter Kelas')
                    ->relationship('kelas', 'nama_kelas'),

                Tables\Filters\SelectFilter::make('status_siswa')
                    ->options([
                        'aktif' => 'Aktif',
                        'lulus' => 'Lulus',
                        'pindah' => 'Pindah',
                        'keluar' => 'Keluar',
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiswa::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}
