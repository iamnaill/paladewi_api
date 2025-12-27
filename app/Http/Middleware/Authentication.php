<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // 1) Pastikan user terautentikasi
        $this->authenticate($request, $guards);

        // 2) Pastikan request API harus JSON (opsional tapi lebih aman)
        if ($request->is('api/*') && !$request->expectsJson()) {
            return response()->json([
                'message' => 'Request harus menggunakan format JSON.'
            ], 406); // Not Acceptable
        }

        // 3) Cegah session hijack sederhana
        // Simpan IP & User-Agent saat login, cek konsistensi
        if ($request->session()->has('auth_ip') && $request->session()->get('auth_ip') !== $request->ip()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Session tidak valid (IP berubah). Silakan login ulang.'
            ], 401);
        }

        if ($request->session()->has('auth_ua') && $request->session()->get('auth_ua') !== $request->userAgent()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Session tidak valid (User Agent berubah). Silakan login ulang.'
            ], 401);
        }

        // 4) Pastikan user masih ada di database (kadang user dihapus tapi session masih ada)
        if (!Auth::user()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'User tidak valid. Silakan login ulang.'
            ], 401);
        }

        return $next($request);
    }

    /**
     * Redirect jika tidak autentikasi
     */
    protected function redirectTo(Request $request): ?string
    {
        // Kalau request API / JSON, jangan redirect — balikin 401 JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        return route('login');
    }
}