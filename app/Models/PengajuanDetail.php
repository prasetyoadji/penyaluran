<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajuan_id',
        'pupuk_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
        'total',
    ];

    public function pengajuan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Pengajuan::class, 'pengajuan_id', 'id');
    }


    public function pupuk(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Pupuk::class, 'pupuk_id', 'id');
    }

}