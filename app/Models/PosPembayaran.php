<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosPembayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pos_pembayaran';

    protected $fillable = [
        'kode_pos',
        'nama_pos',
        'keterangan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function tarif(): HasMany
    {
        return $this->hasMany(TarifPembayaran::class, 'pos_pembayaran_id');
    }

    public function tagihanItems(): HasMany
    {
        return $this->hasMany(TagihanItem::class, 'pos_pembayaran_id');
    }
}
