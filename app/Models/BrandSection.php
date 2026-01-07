<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'page',
        'section',
        'judul',
        'isi',
    ];
}