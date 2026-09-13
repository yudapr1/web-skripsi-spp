<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagihanTahunanResource\Pages;
use App\Models\TagihanTahunan;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TarifPembayaran;
use App\Models\TagihanItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class TagihanTahunanResource extends Resource
{
    protected static ?string $model = TagihanTahunan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Tagihan Siswa';

    protected static ?string $pluralModelLabel = 'Tagihan Siswa';

    protected static ?string $modelLabel = 'Tagihan Siswa';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Tagihan')->schema([
                    Forms\Components\TextInput::make('nomor_tagihan')
                        ->label('Nomor Tagihan')
                        ->default(fn () => 'TAG-' . date('Y') . '-' . strtoupper(uniqid()))
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\Select::make('siswa_id')
                        ->label('Pilih Siswa')
                        ->relationship('siswa', 'nama_lengkap')
                        ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama_lengkap} ({$record->nisn}) - {$record->kelas?->nama_kelas}")
                        ->searchable(['nama_lengkap', 'nisn', 'nis'])
                        ->preload()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if (!$state) return;
                            $siswa = Siswa::with('kelas')->find($state);
                            if (!$siswa || !$siswa->kelas) return;
                            
                            $tingkat = $siswa->kelas->tingkat;
                            $tahunAjaran = $get('tahun_ajaran') ?? '2026/2027';
                            
                            // Ambil master tarif untuk tingkat ini
                            $tarifs = TarifPembayaran::where('tingkat', $tingkat)
                                ->where('tahun_ajaran', $tahunAjaran)
                                ->with('posPembayaran')
                                ->get();
                                
                            if ($tarifs->isNotEmpty()) {
                                $items = [];
                                foreach ($tarifs as $t) {
                                    $items[] = [
                                        'pos_pembayaran_id' => $t->pos_pembayaran_id,
                                        'nominal_pos' => $t->nominal,
                                        'nominal_terbayar' => 0,
                                        'sisa_pos' => $t->nominal,
                                        'status' => $t->nominal == 0 ? 'lunas' : 'belum_lunas',
                                    ];
                                }
                                $set('items', $items);
                            }
                        })
                        ->required(),

                    Forms\Components\TextInput::make('tahun_ajaran')
                        ->label('Tahun Ajaran')
                        ->default('2026/2027')
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label('Status Tagihan Keseluruhan')
                        ->options([
                            'belum_lunas' => 'Belum Lunas',
                            'sebagian' => 'Sebagian',
                            'lunas' => 'Lunas',
                        ])
                        ->default('belum_lunas')
                        ->disabled()
                        ->dehydrated()
                        ->helperText('Status dihitung otomatis: hanya akan Lunas jika seluruh pos tagihan di bawah sudah lunas 100%.')
                        ->required(),
                ])->columns(2),

                Forms\Components\Section::make('Rincian Pos Tagihan (Pilih Satu atau Lebih Jenis Tagihan)')->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship('items')
                        ->schema([
                            Forms\Components\Select::make('pos_pembayaran_id')
                                ->label('Jenis / Pos Tagihan')
                                ->relationship('posPembayaran', 'nama_pos')
                                ->searchable()
                                ->preload()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    if (!$state) return;
                                    $siswaId = $get('../../siswa_id');
                                    $tahunAjaran = $get('../../tahun_ajaran') ?? '2026/2027';
                                    
                                    if ($siswaId) {
                                        $siswa = Siswa::with('kelas')->find($siswaId);
                                        if ($siswa && $siswa->kelas) {
                                            $tarif = TarifPembayaran::where('pos_pembayaran_id', $state)
                                                ->where('tingkat', $siswa->kelas->tingkat)
                                                ->where('tahun_ajaran', $tahunAjaran)
                                                ->first();
                                            if ($tarif) {
                                                $set('nominal_pos', $tarif->nominal);
                                                $set('sisa_pos', $tarif->nominal);
                                            }
                                        }
                                    }
                                })
                                ->required()
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('nominal_pos')
                                ->label('Nominal Pos (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $terbayar = (float) ($get('nominal_terbayar') ?? 0);
                                    $sisa = max(0, (float) $state - $terbayar);
                                    $set('sisa_pos', $sisa);
                                    $set('status', $sisa == 0 ? 'lunas' : ($terbayar > 0 ? 'sebagian' : 'belum_lunas'));
                                })
                                ->required()
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('nominal_terbayar')
                                ->label('Terbayar (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0)
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $nominal = (float) ($get('nominal_pos') ?? 0);
                                    $sisa = max(0, $nominal - (float) $state);
                                    $set('sisa_pos', $sisa);
                                    $set('status', $sisa == 0 ? 'lunas' : ((float) $state > 0 ? 'sebagian' : 'belum_lunas'));
                                })
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('sisa_pos')
                                ->label('Sisa (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->readOnly()
                                ->columnSpan(1),

                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'belum_lunas' => 'Belum Lunas',
                                    'sebagian' => 'Sebagian',
                                    'lunas' => 'Lunas',
                                ])
                                ->default('belum_lunas')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $nominalPos = (float) ($get('nominal_pos') ?? 0);
                                    if ($state === 'lunas') {
                                        $set('nominal_terbayar', $nominalPos);
                                        $set('sisa_pos', 0);
                                    } elseif ($state === 'belum_lunas') {
                                        $set('nominal_terbayar', 0);
                                        $set('sisa_pos', $nominalPos);
                                    }
                                })
                                ->columnSpan(1),
                        ])
                        ->columns(6)
                        ->createItemButtonLabel('➕ Tambah Jenis Tagihan Lainnya')
                        ->columnSpanFull()
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_tagihan')
                    ->label('No. Tagihan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('siswa.kelas.nama_kelas')
                    ->label('Kelas')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tahun_ajaran')
                    ->label('Tahun Ajaran'),

                Tables\Columns\TextColumn::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->money('idr', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_terbayar')
                    ->label('Terbayar')
                    ->money('idr', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sisa_tagihan')
                    ->label('Sisa Tagihan')
                    ->money('idr', locale: 'id')
                    ->sortable()
                    ->color('danger'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'belum_lunas',
                        'warning' => 'sebagian',
                        'success' => 'lunas',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tahun_ajaran')
                    ->options([
                        '2025/2026' => '2025/2026',
                        '2026/2027' => '2026/2027',
                        '2027/2028' => '2027/2028',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'belum_lunas' => 'Belum Lunas',
                        'sebagian' => 'Sebagian',
                        'lunas' => 'Lunas',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTagihanTahunan::route('/'),
            'create' => Pages\CreateTagihanTahunan::route('/create'),
            'edit' => Pages\EditTagihanTahunan::route('/{record}/edit'),
        ];
    }
}
