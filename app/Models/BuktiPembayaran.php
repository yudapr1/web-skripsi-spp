<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuktiPembayaran extends Model
{
    use HasFactory;

    protected $table = 'bukti_pembayaran';

    protected $fillable = [
        'transaksi_pembayaran_id',
        'nama_bank_pengirim',
        'nama_pemilik_rekening',
        'nomor_rekening_pengirim',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'file_size' => 'integer',
        ];
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TransaksiPembayaran::class, 'transaksi_pembayaran_id');
    }
}
