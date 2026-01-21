<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use Illuminate\Http\Request;

class ResepController extends Controller
{
    public function index(Request $request)
    {
        $query = Resep::query();

        // SEARCH keyword (nama/deskripsi/bahan/steps/kategori/link_ytb)
        if ($request->filled('search')) {
            $s = trim((string) $request->search);

            $query->where(function ($q) use ($s) {
                $q->where('nama_resep', 'like', "%{$s}%")
                  ->orWhere('deskripsi', 'like', "%{$s}%")
                  ->orWhere('alat_dan_bahan', 'like', "%{$s}%")
                  ->orWhere('steps', 'like', "%{$s}%")
                  ->orWhere('kategori_produk', 'like', "%{$s}%")
                  ->orWhere('kategori_pala', 'like', "%{$s}%")
                  ->orWhere('link_ytb', 'like', "%{$s}%");
            });
        }

        return response()->json([
            'message' => 'List resep',
            'data' => $query->latest()->get()
        ], 200);
    }

    public function show(Resep $resep)
    {
        return response()->json($resep, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_produk' => 'required|in:makanan,minuman,kecantikan,wewangian',
            'kategori_pala'   => 'nullable|string|max:100',
            'nama_resep'      => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'alat_dan_bahan'  => 'required|string',
            'steps'           => 'required|string',

            // sesuai kolom DB
            'link_ytb'        => 'nullable|url|max:255',

            'foto_produk'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'foto_rekomendasi_kemasan'   => 'nullable|array|max:3',
            'foto_rekomendasi_kemasan.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only([
            'kategori_produk',
            'kategori_pala',
            'nama_resep',
            'deskripsi',
            'alat_dan_bahan',
            'steps',
            'link_ytb',
        ]);

        if ($request->hasFile('foto_produk')) {
            $data['foto_produk'] = $request->file('foto_produk')->store('reseps', 'public');
        }

        $kemasanPaths = [];
        if ($request->hasFile('foto_rekomendasi_kemasan')) {
            foreach ($request->file('foto_rekomendasi_kemasan') as $file) {
                $kemasanPaths[] = $file->store('kemasan', 'public');
            }
        }
        $data['foto_rekomendasi_kemasan'] = $kemasanPaths;

        $resep = Resep::create($data);

        return response()->json([
            'message' => 'Resep berhasil ditambahkan',
            'data' => $resep
        ], 201);
    }

    public function update(Request $request, Resep $resep)
    {
        $request->validate([
            'kategori_produk' => 'sometimes|required|in:makanan,minuman,kecantikan,wewangian',
            'kategori_pala'   => 'nullable|string|max:100',
            'nama_resep'      => 'sometimes|required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'alat_dan_bahan'  => 'sometimes|required|string',
            'steps'           => 'sometimes|required|string',

            // sesuai kolom DB
            'link_ytb'        => 'nullable|url|max:255',

            'foto_produk'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'foto_rekomendasi_kemasan'   => 'nullable|array|max:3',
            'foto_rekomendasi_kemasan.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only([
            'kategori_produk',
            'kategori_pala',
            'nama_resep',
            'deskripsi',
            'alat_dan_bahan',
            'steps',
            'link_ytb',
        ]);

        if ($request->hasFile('foto_produk')) {
            $data['foto_produk'] = $request->file('foto_produk')->store('reseps', 'public');
        } else {
            $data['foto_produk'] = $resep->foto_produk;
        }

        if ($request->hasFile('foto_rekomendasi_kemasan')) {
            $kemasanPaths = [];
            foreach ($request->file('foto_rekomendasi_kemasan') as $file) {
                $kemasanPaths[] = $file->store('kemasan', 'public');
            }
            $data['foto_rekomendasi_kemasan'] = $kemasanPaths;
        } else {
            $data['foto_rekomendasi_kemasan'] = $resep->foto_rekomendasi_kemasan ?? [];
        }

        $resep->update($data);

        return response()->json([
            'message' => 'Resep berhasil diupdate',
            'data' => $resep
        ], 200);
    }

    public function destroy(Resep $resep)
    {
        $resep->delete();

        return response()->json([
            'message' => 'Resep berhasil dihapus'
        ], 200);
    }
}
