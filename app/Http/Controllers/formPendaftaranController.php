<?php

namespace App\Http\Controllers;

use App\Models\DaftarToko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // default pending
        $data['status_approval'] = 'pending';
        $data['approved_at'] = null;
        $data['approved_by'] = null;
        $data['alasan_reject'] = null;

        if ($request->hasFile('gambar_produk')) {
            $path = $request->file('gambar_produk')->store('produk', 'public');
            $data['gambar_produk'] = $path;
        }

        $saved = DaftarToko::create($data);

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
        // publik: hanya approved
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

    // ===============================
    // ADMIN
    // ===============================

    // GET semua data (pending / approved / rejected)
    public function adminIndex(Request $request)
    {
        $query = DaftarToko::query();

        if ($request->filled('status')) {
            $query->where('status_approval', $request->status);
        }

        $data = $query->orderBy('id', 'desc')->get();

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

    // APPROVE TOKO
    public function approve($id)
    {
        $toko = DaftarToko::findOrFail($id);

        $toko->update([
            'is_approved'     => 1,              // ⬅️ INI YANG KURANG
            'status_approval' => 'approved',
            'approved_at'     => now(),
            'approved_by'     => Auth::id() ?? 1,
            'alasan_reject'   => null,
        ]);

        return response()->json([
            'message' => 'Toko berhasil di-approve',
            'data' => $toko
        ]);
    }

    // REJECT TOKO
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_reject' => ['required','string']
        ]);

        $toko = DaftarToko::findOrFail($id);

        $toko->update([
            'status_approval' => 'rejected',
            'approved_at' => null,
            'approved_by' => Auth::id() ?? 1,
            'alasan_reject' => $request->alasan_reject,
        ]);

        return response()->json([
            'message' => 'Toko ditolak',
            'data' => $toko
        ]);
    }
}
