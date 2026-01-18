<?php

namespace App\Http\Controllers;

use App\Models\DaftarToko;
use Illuminate\Http\Request;

class FormPendaftaranController extends Controller
{
    public function create()
    {
        return view('pendaftaran.create');
    }

    public function store(Request $request)
    {
        // VALIDASI SESUAI MODEL
        $validated = $request->validate([
  'nama_toko' => ['required','string','max:255'],
  'no_wa' => ['required','string','max:30'],
  'link_ecommerce' => ['nullable','string','max:255'],
  'kategori_produk' => ['required','string','max:255'],
  'bio_toko' => ['required','string'],
  'deskripsi_produk' => ['required','string'],
  'harga_produk' => ['required','numeric','min:0'],
]);

        // SIMPAN KE TABEL daftar_toko
        $data = DaftarToko::create($validated);

        return response()->json([
            'message' => 'Pendaftaran UMKM berhasil',
            'data' => $data,
        ], 201);
    }
}
