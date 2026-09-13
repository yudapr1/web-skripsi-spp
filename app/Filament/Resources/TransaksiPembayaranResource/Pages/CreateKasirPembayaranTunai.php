<?php

namespace App\Filament\Resources\TransaksiPembayaranResource\Pages;

use App\Filament\Resources\TransaksiPembayaranResource;
use App\Models\Siswa;
use App\Models\TagihanTahunan;
use App\Models\TagihanItem;
use App\Models\TransaksiPembayaran;
use App\Models\TransaksiDetail;
use App\Models\Kwitansi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class CreateKasirPembayaranTunai extends CreateRecord
{
    protected static string $resource = TransaksiPembayaranResource::class;

    protected static ?string $title = 'Loket Kasir — Pembayaran Tunai';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pilih Siswa & Tagihan')->schema([
                    Forms\Components\Select::make('siswa_id')
                        ->label('Cari Siswa (Nama / NISN / Kelas)')
                        ->options(function () {
                            return Siswa::with('kelas')
                                ->where('status_siswa', 'aktif')
                                ->get()
                                ->mapWithKeys(function ($s) {
                                    $kelas = $s->kelas?->nama_kelas ?? '-';
                                    return [$s->id => "{$s->nama_lengkap} ({$s->nisn}) — {$kelas}"];
                                });
                        })
                        ->searchable(['nama_lengkap', 'nisn', 'nis'])
                        ->preload()
                        ->live()
                        ->afterStateUpdated(function (Forms\Set $set, $state) {
                            $set('tagihan_tahunan_id', null);
                            $set('alokasi_pos', []);
                            
                            // Auto pilih jika siswa hanya punya 1 tagihan aktif
                            if ($state) {
                                $tagihan = TagihanTahunan::where('siswa_id', $state)
                                    ->where('status', '!=', 'lunas')
                                    ->first();
                                if ($tagihan) {
                                    $set('tagihan_tahunan_id', $tagihan->id);
                                }
                            }
                        })
                        ->required(),

                    Forms\Components\Select::make('tagihan_tahunan_id')
                        ->label('Pilih Tahun Tagihan')
                        ->options(function (Forms\Get $get) {
                            $siswaId = $get('siswa_id');
                            if (!$siswaId) return [];

                            return TagihanTahunan::where('siswa_id', $siswaId)
                                ->where('status', '!=', 'lunas')
                                ->get()
                                ->mapWithKeys(function ($t) {
                                    $sisaFormatted = number_format($t->sisa_tagihan, 0, ',', '.');
                                    return [$t->id => "Tahun Ajaran {$t->tahun_ajaran} (Sisa Tanggungan: Rp {$sisaFormatted})"];
                                });
                        })
                        ->live()
                        ->afterStateUpdated(fn (Forms\Set $set) => $set('alokasi_pos', []))
                        ->helperText(function (Forms\Get $get) {
                            $siswaId = $get('siswa_id');
                            if ($siswaId) {
                                $count = TagihanTahunan::where('siswa_id', $siswaId)->where('status', '!=', 'lunas')->count();
                                if ($count === 0) {
                                    return '⚠️ Siswa ini belum memiliki tagihan aktif / semua tagihan sudah lunas.';
                                }
                            }
                            return null;
                        })
                        ->required(),
                ])->columns(2),

                Forms\Components\Section::make('Alokasi Pembayaran Pos Tagihan')->schema([
                    Forms\Components\CheckboxList::make('selected_pos_ids')
                        ->label('Pilih Satu atau Lebih Pos Tagihan yang Dibayarkan Siswa:')
                        ->options(function (Forms\Get $get) {
                            $tagihanId = $get('tagihan_tahunan_id');
                            if (!$tagihanId) return [];

                            return TagihanItem::where('tagihan_tahunan_id', $tagihanId)
                                ->where('status', '!=', 'lunas')
                                ->with('posPembayaran')
                                ->get()
                                ->mapWithKeys(function ($item) {
                                    $sisa = number_format($item->sisa_pos, 0, ',', '.');
                                    return [$item->id => "{$item->posPembayaran->nama_pos} — (Sisa: Rp {$sisa})"];
                                });
                        })
                        ->live()
                        ->bulkToggleable()
                        ->columns(2)
                        ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                            $selectedIds = $state ?? [];
                            $currentAlokasi = $get('alokasi_pos') ?? [];
                            $currentMap = collect($currentAlokasi)->keyBy('tagihan_item_id')->toArray();
                            
                            $newAlokasi = [];
                            foreach ($selectedIds as $itemId) {
                                if (isset($currentMap[$itemId])) {
                                    $newAlokasi[] = $currentMap[$itemId];
                                } else {
                                    $item = TagihanItem::find($itemId);
                                    $newAlokasi[] = [
                                        'tagihan_item_id' => $itemId,
                                        'nominal_bayar' => $item ? $item->sisa_pos : 0,
                                    ];
                                }
                            }
                            $set('alokasi_pos', $newAlokasi);
                        })
                        ->helperText('Centang pos yang ingin dibayar. Rincian nominal akan otomatis muncul di bawah.')
                        ->columnSpanFull(),

                    Forms\Components\Repeater::make('alokasi_pos')
                        ->label('Rincian Nominal Pembayaran per Pos (Bisa disesuaikan jika mencicil sebagian):')
                        ->schema([
                            Forms\Components\Select::make('tagihan_item_id')
                                ->label('Pos Tagihan')
                                ->options(function (Forms\Get $get) {
                                    $tagihanId = $get('../../tagihan_tahunan_id');
                                    if (!$tagihanId) return [];

                                    return TagihanItem::where('tagihan_tahunan_id', $tagihanId)
                                        ->where('status', '!=', 'lunas')
                                        ->with('posPembayaran')
                                        ->get()
                                        ->mapWithKeys(function ($item) {
                                            $sisa = number_format($item->sisa_pos, 0, ',', '.');
                                            return [$item->id => "{$item->posPembayaran->nama_pos} (Sisa: Rp {$sisa})"];
                                        });
                                })
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('nominal_bayar')
                                ->label('Nominal Dibayar (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->columnSpan(2),
                        ])
                        ->columns(5)
                        ->addable(false)
                        ->deletable(true)
                        ->columnSpanFull()
                        ->required(),

                    Forms\Components\Textarea::make('catatan_bendahara')
                        ->label('Catatan Loket Kasir (Opsional)')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
            ]);
    }

    protected function handleRecordCreation(array $data): TransaksiPembayaran
    {
        $siswaId = $data['siswa_id'];
        $tagihanId = $data['tagihan_tahunan_id'];
        $alokasiList = $data['alokasi_pos'] ?? [];
        $catatan = $data['catatan_bendahara'] ?? null;

        $totalBayar = 0;
        foreach ($alokasiList as $item) {
            $totalBayar += (float) $item['nominal_bayar'];
        }

        return DB::transaction(function () use ($siswaId, $tagihanId, $alokasiList, $totalBayar, $catatan) {
            // 1. Create Transaksi Record
            $nomorTransaksi = 'TRX-' . date('Ymd') . '-' . strtoupper(uniqid());
            $transaksi = TransaksiPembayaran::create([
                'nomor_transaksi' => $nomorTransaksi,
                'siswa_id' => $siswaId,
                'tagihan_tahunan_id' => $tagihanId,
                'tanggal_transaksi' => now(),
                'metode_pembayaran' => 'tunai',
                'nominal_pokok' => $totalBayar,
                'kode_unik' => 0,
                'total_transfer' => $totalBayar,
                'status' => 'terverifikasi', // Tunai langsung terverifikasi
                'catatan_bendahara' => $catatan,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);

            // 2. Simpan Alokasi Transaksi Detail & Update Tagihan Item
            foreach ($alokasiList as $alokasi) {
                $tagihanItem = TagihanItem::findOrFail($alokasi['tagihan_item_id']);
                $nominal = (float) $alokasi['nominal_bayar'];

                TransaksiDetail::create([
                    'transaksi_pembayaran_id' => $transaksi->id,
                    'tagihan_item_id' => $tagihanItem->id,
                    'pos_pembayaran_id' => $tagihanItem->pos_pembayaran_id,
                    'nominal_bayar' => $nominal,
                ]);

                $tagihanItem->nominal_terbayar += $nominal;
                $tagihanItem->sisa_pos = max(0, $tagihanItem->nominal_pos - $tagihanItem->nominal_terbayar);
                $tagihanItem->status = $tagihanItem->sisa_pos == 0 ? 'lunas' : 'sebagian';
                $tagihanItem->save();
            }

            // 3. Update Tagihan Header
            $tagihan = TagihanTahunan::findOrFail($tagihanId);
            $tagihan->total_terbayar = $tagihan->items()->sum('nominal_terbayar');
            $tagihan->sisa_tagihan = max(0, $tagihan->total_tagihan - $tagihan->total_terbayar);
            $tagihan->status = $tagihan->sisa_tagihan == 0 ? 'lunas' : ($tagihan->total_terbayar > 0 ? 'sebagian' : 'belum_lunas');
            $tagihan->save();

            // 4. Terbitkan Kwitansi Otomatis
            Kwitansi::create([
                'transaksi_pembayaran_id' => $transaksi->id,
                'nomor_kwitansi' => 'KWT/' . date('Y/m/') . str_pad($transaksi->id, 4, '0', STR_PAD_LEFT),
                'tanggal_terbit' => now(),
                'diterbitkan_oleh' => Auth::id() ?? 1,
            ]);

            return $transaksi;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
