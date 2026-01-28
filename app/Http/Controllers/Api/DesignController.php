<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Design;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DesignController extends Controller
{
    // 🔹 LIST + SEARCH
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $limit = max(1, min((int) $request->query('limit', 20), 50));

        $query = Design::query()
            ->select(['id', 'title', 'template_link', 'image_path', 'category']);

        // 🔍 searching (optional)
        if (strlen($q) >= 2) {
            $query->where('title', 'like', '%' . $q . '%');
        }

        return response()->json([
            'message' => 'List designs',
            'data' => $query->latest()->limit($limit)->get()
        ]);
    }

    // 🔹 DETAIL
    public function show($id)
    {
        return response()->json([
            'message' => 'Detail design',
            'data' => Design::findOrFail($id)
        ]);
    }

    // 🔹 CREATE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'template_link' => 'nullable|url',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = $request->file('image')->store('designs', 'public');

        $design = Design::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'template_link' => $validated['template_link'] ?? null,
            'image_path' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Design berhasil ditambahkan',
            'data' => $design
        ], 201);
    }

    // 🔹 UPDATE
    public function update(Request $request, $id)
    {
        $design = Design::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|max:100',
            'template_link' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($design->image_path) {
                Storage::disk('public')->delete($design->image_path);
            }

            $design->image_path = $request->file('image')->store('designs', 'public');
        }

        $design->update($validated);

        return response()->json([
            'message' => 'Design berhasil diupdate',
            'data' => $design
        ]);
    }

    // 🔹 DELETE
    public function destroy($id)
    {
        $design = Design::findOrFail($id);

        if ($design->image_path) {
            Storage::disk('public')->delete($design->image_path);
        }

        $design->delete();

        return response()->json([
            'message' => 'Design berhasil dihapus'
        ]);
    }
}
