<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // Pastikan sudah login
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Kalau role user tidak termasuk role yang diizinkan
        if (!in_array($user->role, $roles)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}