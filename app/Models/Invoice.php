<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaksi_id',
        'nomor_invoice',
        'tanggal_terbit',
        'catatan',
        'status',
        'perusahaan_id',
    ];

    public function perusahaan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Perusahaan::class, 'perusahaan_id', 'id');
    }


    public function transaksi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Transaksi::class, 'transaksi_id', 'id');
    }

}
