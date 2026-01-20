<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarToko extends Model
{
    use HasFactory;

    protected $table = 'daftar_toko';

protected $fillable = [
  'nama_toko',
  'no_wa',
  'link_ecommerce',
  'kategori_produk',
  'bio_toko',
  'nama_produk',
  'deskripsi_produk',
  'harga_produk',
  'gambar_produk',
];


}
