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
    $validated = $request->validate([
        'nama_toko' => ['required', 'string', 'max:255'],
        'no_wa' => ['required', 'string', 'max:30'],
        'link_ecommerce' => ['nullable', 'string', 'max:255'],
        'kategori_produk' => ['required', 'string', 'max:255'],
        'bio_toko' => ['required', 'string'],
    ]);

    $data = DaftarToko::create($validated);

    return response()->json([
        'message' => 'Pendaftaran berhasil',
        'data' => $data,
    ], 201);
}

}
