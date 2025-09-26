<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaksi_id',
        'metode',
        'jumlah_bayar',
        'tanggal_bayar',
        'bukti',
    ];

    public function transaksi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Transaksi::class, 'transaksi_id', 'id');
    }

}