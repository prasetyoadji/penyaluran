<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pembeli_id',
        'disetujui_oleh',
        'tanggal_persetujuan',
        'status',
        'alasan_pengajuan',
        'alasan_penolakan',
    ];

    public function pembeli(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'pembeli_id', 'id');
    }

    public function disetujuiOleh(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'disetujui_oleh', 'id');
    }


    public function pengajuanDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\PengajuanDetail::class);
    }

}
