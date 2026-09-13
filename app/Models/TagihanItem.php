<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TagihanItem extends Model
{
    use HasFactory;

    protected $table = 'tagihan_item';

    protected $fillable = [
        'tagihan_tahunan_id',
        'pos_pembayaran_id',
        'nominal_pos',
        'nominal_terbayar',
        'sisa_pos',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'nominal_pos' => 'decimal:2',
            'nominal_terbayar' => 'decimal:2',
            'sisa_pos' => 'decimal:2',
        ];
    }

    public function tagihanTahunan(): BelongsTo
    {
        return $this->belongsTo(TagihanTahunan::class, 'tagihan_tahunan_id');
    }

    public function posPembayaran(): BelongsTo
    {
        return $this->belongsTo(PosPembayaran::class, 'pos_pembayaran_id');
    }

    public function transaksiDetail(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class, 'tagihan_item_id');
    }
}
