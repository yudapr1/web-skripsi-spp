<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarifPembayaran extends Model
{
    use HasFactory;

    protected $table = 'tarif_pembayaran';

    protected $fillable = [
        'pos_pembayaran_id',
        'tahun_ajaran',
        'tingkat',
        'nominal',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
        ];
    }

    public function posPembayaran(): BelongsTo
    {
        return $this->belongsTo(PosPembayaran::class, 'pos_pembayaran_id');
    }
}
