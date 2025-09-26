<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajuan_id',
        'nomor_transaksi',
        'tanggal_transaksi',
        'total_harga',
        'perusahaan_id',
    ];

    public function perusahaan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Perusahaan::class, 'perusahaan_id', 'id');
    }


    public function pengajuan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Pengajuan::class, 'pengajuan_id', 'id');
    }


    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Invoice::class);
    }


    public function pembayarans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Pembayaran::class);
    }
}
