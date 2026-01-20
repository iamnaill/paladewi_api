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
        'nama_toko' => ['required','string','max:255'],
        'no_wa' => ['required','string','max:30'],
        'link_ecommerce' => ['nullable','string','max:255'],
        'kategori_produk' => ['required','string','max:255'],
        'bio_toko' => ['required','string'],

        'nama_produk' => ['nullable','string','max:255'],
        'deskripsi_produk' => ['required','string'],
        'harga_produk' => ['required','numeric','min:0'],

        // upload file
        'gambar_produk' => ['nullable','file','mimes:jpg,jpeg,png,webp','max:2048'],
    ]);

    // ambil data text dulu
    $data = $validated;

    // handle upload file
    if ($request->hasFile('gambar_produk')) {
        $path = $request->file('gambar_produk')->store('produk', 'public');
        $data['gambar_produk'] = $path; // simpan "produk/namafile.png"
    }

    $saved = DaftarToko::create($data);

    // optional: balikin full URL biar website gampang
    $saved->gambar_produk = $saved->gambar_produk
        ? asset('storage/' . $saved->gambar_produk)
        : null;

    return response()->json([
        'message' => 'Pendaftaran UMKM berhasil',
        'data' => $saved,
    ], 201);
}

    public function index()
{
    $data = DaftarToko::orderBy('id', 'desc')->get();

    // kalau kamu mau gambar langsung bisa dipakai di web tanpa nambah /storage/
    $data->transform(function ($item) {
        $item->gambar_produk = $item->gambar_produk
            ? asset('storage/' . $item->gambar_produk)
            : null;
        return $item;
    });

    return response()->json([
        'data' => $data
    ], 200);
}

}

