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
        'harga_jual',
        'pembeli_utama',
        'cara_jual',
        'produk_paling_laku',

        // Q
        'q7_pujian',
        'q8_kesulitan',
        'q8_detail',
        'q9_waktu_ramai',
        'q9_hari_tentu',
        'q10_tujuan_beli',
        'q10_lainnya',
        'q11_ancaman',

        // SWOT kolom terpisah
        'swot_s',
        'swot_w',
        'swot_o',
        'swot_t',

        // saran 3 bulan (biar sama controller)
        'saran_3_bulan',

    ];

    protected $casts = [
        'pembeli_utama' => 'array',
        'cara_jual' => 'array',

        'q7_pujian' => 'array',
        'q8_kesulitan' => 'array',
        'q8_detail' => 'array',
        'q9_waktu_ramai' => 'array',
        'q10_tujuan_beli' => 'array',
        'q11_ancaman' => 'array',

        'swot_s' => 'array',
        'swot_w' => 'array',
        'swot_o' => 'array',
        'swot_t' => 'array',

        'saran_3_bulan' => 'array',

    ];
}