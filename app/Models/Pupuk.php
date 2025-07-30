<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pupuk extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jenis_id',
        'stok',
        'satuan_id',
        'deskripsi',
        'harga',
    ];

    public function satuan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Satuan::class, 'satuan_id', 'id');
    }


    public function jenis(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Jenis::class, 'jenis_id', 'id');
    }


    public function pengajuanDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\PengajuanDetail::class);
    }

}
