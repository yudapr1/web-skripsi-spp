<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TagihanTahunan extends Model
{
    use HasFactory;

    protected $table = 'tagihan_tahunan';

    protected $fillable = [
        'nomor_tagihan',
        'siswa_id',
        'tahun_ajaran',
        'total_tagihan',
        'total_terbayar',
        'sisa_tagihan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_tagihan' => 'decimal:2',
            'total_terbayar' => 'decimal:2',
            'sisa_tagihan' => 'decimal:2',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TagihanItem::class, 'tagihan_tahunan_id');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiPembayaran::class, 'tagihan_tahunan_id');
    }
}
