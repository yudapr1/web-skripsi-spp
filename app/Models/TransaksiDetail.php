<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiDetail extends Model
{
    use HasFactory;

    protected $table = 'transaksi_detail';

    protected $fillable = [
        'transaksi_pembayaran_id',
        'tagihan_item_id',
        'pos_pembayaran_id',
        'nominal_bayar',
    ];

    protected function casts(): array
    {
        return [
            'nominal_bayar' => 'decimal:2',
        ];
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TransaksiPembayaran::class, 'transaksi_pembayaran_id');
    }

    public function tagihanItem(): BelongsTo
    {
        return $this->belongsTo(TagihanItem::class, 'tagihan_item_id');
    }

    public function posPembayaran(): BelongsTo
    {
        return $this->belongsTo(PosPembayaran::class, 'pos_pembayaran_id');
    }
}
