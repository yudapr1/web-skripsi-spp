<?php

namespace App\Filament\Resources\TagihanTahunanResource\Pages;

use App\Filament\Resources\TagihanTahunanResource;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TarifPembayaran;
use App\Models\TagihanTahunan;
use App\Models\TagihanItem;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListTagihanTahunan extends ListRecords
{
    protected static string $resource = TagihanTahunanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Buat Tagihan Manual'),

            Actions\Action::make('generateMassal')
                ->label('⚡ Generate Tagihan Massal')
                ->color('success')
                ->icon('heroicon-o-bolt')
                ->form([
                    Forms\Components\Select::make('tingkat')
                        ->label('Pilih Tingkat Kelas')
                        ->options([
                            'X' => 'Kelas X (Sepuluh)',
                            'XI' => 'Kelas XI (Sebelas)',
                            'XII' => 'Kelas XII (Dua Belas)',
                        ])
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('pos_pembayaran_ids', []))
                        ->required(),

                    Forms\Components\TextInput::make('tahun_ajaran')
                        ->label('Tahun Ajaran')
                        ->default('2026/2027')
                        ->reactive()
                        ->required(),

                    Forms\Components\CheckboxList::make('pos_pembayaran_ids')
                        ->label('Pilih Jenis / Pos Tagihan yang Ingin Dibuat')
                        ->options(function (callable $get) {
                            $tingkat = $get('tingkat');
                            $tahunAjaran = $get('tahun_ajaran') ?? '2026/2027';

                            if (!$tingkat) {
                                return \App\Models\PosPembayaran::where('is_active', true)->pluck('nama_pos', 'id');
                            }

                            return TarifPembayaran::where('tingkat', $tingkat)
                                ->where('tahun_ajaran', $tahunAjaran)
                                ->with('posPembayaran')
                                ->get()
                                ->mapWithKeys(function ($t) {
                                    $nominal = number_format($t->nominal, 0, ',', '.');
                                    return [$t->pos_pembayaran_id => "{$t->posPembayaran->nama_pos} (Tarif: Rp {$nominal})"];
                                });
                        })
                        ->default(function (callable $get) {
                            $tingkat = $get('tingkat');
                            $tahunAjaran = $get('tahun_ajaran') ?? '2026/2027';
                            if (!$tingkat) return [];
                            return TarifPembayaran::where('tingkat', $tingkat)
                                ->where('tahun_ajaran', $tahunAjaran)
                                ->pluck('pos_pembayaran_id')
                                ->toArray();
                        })
                        ->bulkToggleable()
                        ->columns(2)
                        ->helperText('Pilih satu atau lebih jenis tagihan yang ingin diterapkan ke siswa. Jika siswa sudah memiliki tagihan, pos baru akan ditambahkan otomatis.')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $tingkat = $data['tingkat'];
                    $tahunAjaran = $data['tahun_ajaran'];
                    $selectedPosIds = $data['pos_pembayaran_ids'] ?? [];

                    if (empty($selectedPosIds)) {
                        Notification::make()
                            ->title('Gagal Generate')
                            ->body("Pilih minimal satu jenis pos tagihan!")
                            ->danger()
                            ->send();
                        return;
                    }

                    // 1. Ambil tarif untuk tingkat, tahun ajaran, dan pos yang dipilih
                    $tarifs = TarifPembayaran::where('tingkat', $tingkat)
                        ->where('tahun_ajaran', $tahunAjaran)
                        ->whereIn('pos_pembayaran_id', $selectedPosIds)
                        ->get();

                    if ($tarifs->isEmpty()) {
                        Notification::make()
                            ->title('Gagal Generate')
                            ->body("Belum ada master tarif untuk pos yang dipilih pada Tingkat {$tingkat} Tahun Ajaran {$tahunAjaran}!")
                            ->danger()
                            ->send();
                        return;
                    }

                    // 2. Ambil semua siswa aktif di kelas dengan tingkat tersebut
                    $siswas = Siswa::whereHas('kelas', function ($q) use ($tingkat) {
                        $q->where('tingkat', $tingkat);
                    })->where('status_siswa', 'aktif')->get();

                    if ($siswas->isEmpty()) {
                        Notification::make()
                            ->title('Tidak Ada Siswa')
                            ->body("Tidak ditemukan siswa aktif di Tingkat {$tingkat}!")
                            ->warning()
                            ->send();
                        return;
                    }

                    $countGenerated = 0;
                    $countUpdated = 0;

                    DB::transaction(function () use ($siswas, $tarifs, $tahunAjaran, $tingkat, &$countGenerated, &$countUpdated) {
                        foreach ($siswas as $siswa) {
                            // Cek jika sudah punya tagihan di tahun ajaran ini
                            $tagihan = TagihanTahunan::where('siswa_id', $siswa->id)
                                ->where('tahun_ajaran', $tahunAjaran)
                                ->first();

                            $isNew = false;
                            if (!$tagihan) {
                                $nomorTagihan = 'TAG-' . substr($tahunAjaran, 0, 4) . '-' . $tingkat . '-' . str_pad($siswa->id, 4, '0', STR_PAD_LEFT);
                                $tagihan = TagihanTahunan::create([
                                    'nomor_tagihan' => $nomorTagihan,
                                    'siswa_id' => $siswa->id,
                                    'tahun_ajaran' => $tahunAjaran,
                                    'total_tagihan' => 0.00,
                                    'total_terbayar' => 0.00,
                                    'sisa_tagihan' => 0.00,
                                    'status' => 'belum_lunas',
                                ]);
                                $isNew = true;
                                $countGenerated++;
                            } else {
                                $countUpdated++;
                            }

                            // Tambahkan atau update tagihan_item sesuai pos yang dipilih
                            foreach ($tarifs as $tarif) {
                                $item = TagihanItem::where('tagihan_tahunan_id', $tagihan->id)
                                    ->where('pos_pembayaran_id', $tarif->pos_pembayaran_id)
                                    ->first();

                                if (!$item) {
                                    TagihanItem::create([
                                        'tagihan_tahunan_id' => $tagihan->id,
                                        'pos_pembayaran_id' => $tarif->pos_pembayaran_id,
                                        'nominal_pos' => $tarif->nominal,
                                        'nominal_terbayar' => 0.00,
                                        'sisa_pos' => $tarif->nominal,
                                        'status' => $tarif->nominal == 0 ? 'lunas' : 'belum_lunas',
                                    ]);
                                }
                            }

                            // Rekalkulasi total header tagihan tahunan
                            $totalTagihan = $tagihan->items()->sum('nominal_pos');
                            $totalTerbayar = $tagihan->items()->sum('nominal_terbayar');
                            $sisaTagihan = max(0, $totalTagihan - $totalTerbayar);
                            $status = $sisaTagihan == 0 ? 'lunas' : ($totalTerbayar > 0 ? 'sebagian' : 'belum_lunas');

                            $tagihan->update([
                                'total_tagihan' => $totalTagihan,
                                'total_terbayar' => $totalTerbayar,
                                'sisa_tagihan' => $sisaTagihan,
                                'status' => $status,
                            ]);
                        }
                    });

                    Notification::make()
                        ->title('Generate Berhasil')
                        ->body("Berhasil memproses tagihan ({$countGenerated} baru dibuat, {$countUpdated} diperbarui dengan pos terpilih) untuk siswa Tingkat {$tingkat}!")
                        ->success()
                        ->send();
                }),
        ];
    }
}
