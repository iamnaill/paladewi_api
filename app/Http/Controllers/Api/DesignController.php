<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Design;
use Illuminate\Http\Request;

class DesignController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $category = $request->query('category');
        $limit = (int) $request->query('limit', 20);
        $limit = max(1, min($limit, 50));

        $query = Design::query()
            ->select(['id', 'title', 'template_link', 'image_path', 'category']);

        if (!empty($category)) {
            $query->where('category', $category);
        }

        // ✅ search mengandung kata (bukan harus diawali)
        if (strlen($q) >= 2) {
            $query->where('title', 'like', '%' . $q . '%');
        }

        $designs = $query->orderByDesc('id')
            ->limit($limit)
            ->get();

        return response()->json([
            'message' => 'List designs',
            'data' => $designs
        ], 200);
    }
}
