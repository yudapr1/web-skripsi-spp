<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransaksiPembayaran extends Model
{
    use HasFactory;

    protected $table = 'transaksi_pembayaran';

    protected $fillable = [
        'nomor_transaksi',
        'siswa_id',
        'tagihan_tahunan_id',
        'tanggal_transaksi',
        'metode_pembayaran',
        'nominal_pokok',
        'kode_unik',
        'total_transfer',
        'status',
        'catatan_siswa',
        'catatan_bendahara',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_transaksi' => 'datetime',
            'verified_at' => 'datetime',
            'nominal_pokok' => 'decimal:2',
            'total_transfer' => 'decimal:2',
            'kode_unik' => 'integer',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function tagihanTahunan(): BelongsTo
    {
        return $this->belongsTo(TagihanTahunan::class, 'tagihan_tahunan_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_pembayaran_id');
    }

    public function buktiPembayaran(): HasOne
    {
        return $this->hasOne(BuktiPembayaran::class, 'transaksi_pembayaran_id');
    }

    public function kwitansi(): HasOne
    {
        return $this->hasOne(Kwitansi::class, 'transaksi_pembayaran_id');
    }
}
