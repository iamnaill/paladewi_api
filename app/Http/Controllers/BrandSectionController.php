<?php

namespace App\Http\Controllers;

use App\Models\BrandSection;
use Illuminate\Http\Request;

class BrandSectionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'required|in:brand_bersuara,kenalimerk',
        ]);

        $sections = BrandSection::where('page', $request->page)
            ->orderBy('section')
            ->get();

        return response()->json([
            'page' => $request->page,
            'sections' => $sections,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'page' => 'required|in:brand_bersuara,kenalimerk',
            'section' => 'required|integer|min:1|max:3',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        $data = BrandSection::updateOrCreate(
            ['page' => $request->page, 'section' => $request->section],
            ['judul' => $request->judul, 'isi' => $request->isi]
        );

        return response()->json([
            'message' => 'Section berhasil disimpan',
            'data' => $data,
        ], 201);
    }

    public function update(Request $request, BrandSection $brandSection)
    {
        $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'isi' => 'sometimes|required|string',
        ]);

        $brandSection->update($request->only(['judul', 'isi']));

        return response()->json([
            'message' => 'Section berhasil diupdate',
            'data' => $brandSection,
        ], 200);
    }

    public function destroy(BrandSection $brandSection)
    {
        $brandSection->delete();

        return response()->json([
            'message' => 'Section berhasil dihapus',
        ], 200);
    }
}
