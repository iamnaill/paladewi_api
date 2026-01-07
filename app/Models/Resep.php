<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_produk',
        'kategori_pala',
        'nama_resep',
        'deskripsi',
        'alat_dan_bahan',
        'steps',
        'foto_produk',
        'foto_rekomendasi_kemasan',
    ];

    protected $casts = [
        'foto_rekomendasi_kemasan' => 'array',
    ];
}
