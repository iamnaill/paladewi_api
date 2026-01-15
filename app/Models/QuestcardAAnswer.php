<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestcardAAnswer extends Model
{
    use HasFactory;

    protected $table = 'questcard_a_answers';

    protected $fillable = [
        'user_id',
        'nama_produk',
        'jenis_produk',
        'jenis_produk_lainnya',
        'harga_jual',
        'pembeli_utama',
        'cara_jual',
        'produk_paling_laku',
        'q7_pujian',
        'q8_kesulitan',
        'q8_detail',
        'q9_waktu_ramai',
        'q9_hari_tentu',
        'q10_tujuan_beli',
        'q10_lainnya',
        'q11_ancaman',
        'result',
    ];

    protected $casts = [
        'q7_pujian' => 'array',
        'q8_kesulitan' => 'array',
        'q8_detail' => 'array',
        'q9_waktu_ramai' => 'array',
        'q10_tujuan_beli' => 'array',
        'q11_ancaman' => 'array',
        'result' => 'array',
    ];
}
