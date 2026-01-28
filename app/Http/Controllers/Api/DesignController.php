<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Design;
use Illuminate\Http\Request;

class DesignController extends Controller
{
    // LIST DESIGN + SEARCH
    public function index(Request $request)
    {
        $q = $request->query('q'); // ambil query param 'q' dari URL

        $designs = Design::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('category', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'List design',
            'data' => $designs
        ], 200);
    }

    // DETAIL DESIGN
    public function show($id)
    {
        $design = Design::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail design',
            'data' => $design
        ], 200);
    }

    // TAMBAH DESIGN
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'category'      => 'required|string|max:100',
            'template_link' => 'nullable|string',
            'image_path'    => 'nullable|string',
        ]);

        $design = Design::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Design berhasil dibuat',
            'data' => $design
        ], 201);
    }

    // UPDATE DESIGN
    public function update(Request $request, $id)
    {
        $design = Design::findOrFail($id);

        $data = $request->validate([
            'title'         => 'sometimes|required|string|max:255',
            'category'      => 'sometimes|required|string|max:100',
            'template_link' => 'nullable|string',
            'image_path'    => 'nullable|string',
        ]);

        $design->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Design berhasil diupdate',
            'data' => $design
        ], 200);
    }

    // DELETE DESIGN
    public function destroy($id)
    {
        $design = Design::findOrFail($id);
        $design->delete();

        return response()->json([
            'success' => true,
            'message' => 'Design berhasil dihapus'
        ], 200);
    }
}
