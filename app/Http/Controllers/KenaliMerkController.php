<?php

namespace App\Http\Controllers;

use App\Models\KenaliMerk;
use Illuminate\Http\Request;

class KenaliMerkController extends Controller
{
    public function index(Request $request)
    {
        $data = KenaliMerk::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($data, 200);
    }

    public function show(Request $request, KenaliMerk $kenaliMerk)
    {
        if ($kenaliMerk->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($kenaliMerk, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'makna_dibalik_produk' => 'nullable|string',
            'kenapa_dibutuhkan_dan_apa_beda' => 'nullable|string',
            'siapa_yang_kita_tuju' => 'nullable|string',
        ]);

        $item = KenaliMerk::create([
            'user_id' => $request->user()->id,
            'makna_dibalik_produk' => $request->makna_dibalik_produk,
            'kenapa_dibutuhkan_dan_apa_beda' => $request->kenapa_dibutuhkan_dan_apa_beda,
            'siapa_yang_kita_tuju' => $request->siapa_yang_kita_tuju,
        ]);

        return response()->json([
            'message' => 'KenaliMerk berhasil disimpan',
            'data' => $item,
        ], 201);
    }

    public function update(Request $request, KenaliMerk $kenaliMerk)
    {
        if ($kenaliMerk->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'makna_dibalik_produk' => 'nullable|string',
            'kenapa_dibutuhkan_dan_apa_beda' => 'nullable|string',
            'siapa_yang_kita_tuju' => 'nullable|string',
        ]);

        $data = [
            'makna_dibalik_produk' => $request->makna_dibalik_produk,
            'kenapa_dibutuhkan_dan_apa_beda' => $request->kenapa_dibutuhkan_dan_apa_beda,
            'siapa_yang_kita_tuju' => $request->siapa_yang_kita_tuju,
        ];

        $data = array_filter($data, fn ($v) => $v !== null);

        $kenaliMerk->update($data);

        return response()->json([
            'message' => 'KenaliMerk berhasil diupdate',
            'data' => $kenaliMerk,
        ], 200);
    }

    public function destroy(Request $request, KenaliMerk $kenaliMerk)
    {
        if ($kenaliMerk->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $kenaliMerk->delete();

        return response()->json(['message' => 'KenaliMerk berhasil dihapus'], 200);
    }
}
