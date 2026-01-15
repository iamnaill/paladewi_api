<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KenaliMerk extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'makna_dibalik_produk',
        'kenapa_dibutuhkan_dan_apa_beda',
        'siapa_yang_kita_tuju',
        'skor',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
