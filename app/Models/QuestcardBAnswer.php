<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestcardBAnswer extends Model
{
    use HasFactory;

    protected $table = 'questcard_b_answers';

    protected $fillable = [
        'user_id',
        'q1_bahan_pala',
        'q2_pernah_olah',
        'q2b_olah_apa',
        'q3_diminta_orang',
        'q4_bayar',
        'q5_sanggup_bikin_sering',
        'q6_mau_jual',
        'score',
        'status',
        'result',
    ];

    protected $casts = [
        'q2b_olah_apa' => 'array',
        'result' => 'array',
    ];
}
