<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DesignController extends Controller
{
    /**
     * READ ALL
     * GET /api/designs?search=&category=
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');

        $query = Design::query();

        // filter by kategori
        if ($category) {
            $query->where('category', $category);
        }

        // search all in (title, template_link, category)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('template_link', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $designs = $query->latest()->get();

        return response()->json([
            'data' => $designs
        ]);
    }

    /**
     * CREATE
     * POST /api/designs
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'template_link' => 'required|url',
            'category' => 'required|string|max:100',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        // simpan gambar
        $imagePath = $request->file('image')->store('designs', 'public');

        $design = Design::create([
            'title' => $validated['title'],
            'template_link' => $validated['template_link'],
            'category' => $validated['category'],
            'image_path' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Design berhasil ditambahkan',
            'data' => $design
        ], 201);
    }

    /**
     * READ DETAIL
     * GET /api/designs/{id}
     */
    public function show(string $id)
    {
        $design = Design::find($id);

        if (!$design) {
            return response()->json([
                'message' => 'Design tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'data' => $design
        ]);
    }

    /**
     * UPDATE
     * PUT /api/designs/{id}
     */
    public function update(Request $request, string $id)
    {
        $design = Design::find($id);

        if (!$design) {
            return response()->json([
                'message' => 'Design tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'template_link' => 'sometimes|required|url',
            'category' => 'sometimes|required|string|max:100',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        // update gambar jika ada
        if ($request->hasFile('image')) {
            if ($design->image_path && Storage::disk('public')->exists($design->image_path)) {
                Storage::disk('public')->delete($design->image_path);
            }

            $design->image_path = $request->file('image')->store('designs', 'public');
        }

        if (isset($validated['title'])) {
            $design->title = $validated['title'];
        }
        if (isset($validated['template_link'])) {
            $design->template_link = $validated['template_link'];
        }
        if (isset($validated['category'])) {
            $design->category = $validated['category'];
        }

        $design->save();

        return response()->json([
            'message' => 'Design berhasil diperbarui',
            'data' => $design
        ]);
    }

    /**
     * DELETE
     * DELETE /api/designs/{id}
     */
    public function destroy(string $id)
    {
        $design = Design::find($id);

        if (!$design) {
            return response()->json([
                'message' => 'Design tidak ditemukan'
            ], 404);
        }

        // hapus gambar
        if ($design->image_path && Storage::disk('public')->exists($design->image_path)) {
            Storage::disk('public')->delete($design->image_path);
        }

        $design->delete();

        return response()->json([
            'message' => 'Design berhasil dihapus'
        ]);
    }
    public function redirectToTemplate(string $id)
{
    $design = Design::find($id);

    if (!$design) {
        return response()->json(['message' => 'Design tidak ditemukan'], 404);
    }

    if (!$design->template_link) {
        return response()->json(['message' => 'Template link belum tersedia'], 400);
    }

    // redirect ke link canva (domain luar)
    return redirect()->away($design->template_link);
}
}
