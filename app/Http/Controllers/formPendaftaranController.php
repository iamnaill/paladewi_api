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

            'gambar_produk' => ['nullable','file','mimes:jpg,jpeg,png,webp','max:2048'],
        ]);

        $data = $validated;

        // ✅ default: masuk sebagai pending (belum di-approve)
        $data['status_approval'] = 'pending';
        $data['approved_at'] = null;
        $data['approved_by'] = null;
        $data['alasan_reject'] = null;

        // upload file
        if ($request->hasFile('gambar_produk')) {
            $path = $request->file('gambar_produk')->store('produk', 'public');
            $data['gambar_produk'] = $path; // contoh: "produk/namafile.png"
        }

        $saved = DaftarToko::create($data);

        // balikin full URL biar web gampang
        $saved->gambar_produk = $saved->gambar_produk
            ? asset('storage/' . $saved->gambar_produk)
            : null;

        return response()->json([
            'message' => 'Pendaftaran UMKM berhasil (status: pending)',
            'data' => $saved,
        ], 201);
    }

    public function index()
    {
        // ✅ hanya tampilkan yang sudah approved
        $data = DaftarToko::approved()
            ->orderBy('id', 'desc')
            ->get();

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
