<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/**
 * =========================
 * PUBLIC ROUTES (TANPA LOGIN)
 * =========================
 * URL:
 * POST /api/register
 * POST /api/login
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


/**
 * =========================
 * PROTECTED ROUTES (HARUS LOGIN)
 * =========================
 * Middleware auth akan cek session login.
 */
Route::middleware(['auth'])->group(function () {

    // POST /api/logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // GET /api/me
    Route::get('/me', function () {
        return response()->json([
            'message' => 'User login saat ini',
            'user' => Auth::user()
        ], 200);
    });

    /**
     * =========================
     * ROUTES UNTUK USER BIASA
     * =========================
     */
    Route::middleware(['role:user'])->prefix('user')->group(function () {

        // GET /api/user/dashboard
        Route::get('/dashboard', function () {
            return response()->json([
                'message' => 'Halo User Biasa',
                'user' => Auth::user()
            ], 200);
        });

        // GET /api/user/profile
        Route::get('/profile', function () {
            return response()->json(Auth::user(), 200);
        });
    });

    /**
     * =========================
     * ROUTES UNTUK ADMIN
     * =========================
     */
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {

        // GET /api/admin/dashboard
        Route::get('/dashboard', function () {
            return response()->json([
                'message' => 'Halo Admin',
                'user' => Auth::user()
            ], 200);
        });

        // CRUD user untuk admin
        // GET /api/admin/users
        // POST /api/admin/users
        // PUT /api/admin/users/{id}
        // DELETE /api/admin/users/{id}
        Route::apiResource('/users', UserController::class);
    });

});
