<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\BrandSectionController;
use App\Http\Controllers\Api\DesignController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/reseps', [ResepController::class, 'index']);
Route::get('/reseps/{resep}', [ResepController::class, 'show']);

Route::get('/brand-sections', [BrandSectionController::class, 'index']);

Route::get('/designs', [DesignController::class, 'index']);
Route::get('/designs/{id}', [DesignController::class, 'show']);
Route::get('/designs/{id}/template', [DesignController::class, 'redirectToTemplate']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return response()->json([
            'message' => 'User login saat ini',
            'user' => $request->user(),
        ], 200);
    });

    Route::post('/reseps', [ResepController::class, 'store']);
    Route::put('/reseps/{resep}', [ResepController::class, 'update']);
    Route::delete('/reseps/{resep}', [ResepController::class, 'destroy']);

    Route::post('/brand-sections', [BrandSectionController::class, 'store']);
    Route::put('/brand-sections/{brandSection}', [BrandSectionController::class, 'update']);
    Route::delete('/brand-sections/{brandSection}', [BrandSectionController::class, 'destroy']);
});

Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', function (Request $request) {
        return response()->json([
            'message' => 'Halo Admin',
            'user' => $request->user(),
        ], 200);
    });

    Route::apiResource('/users', UserController::class);

    Route::post('/designs', [DesignController::class, 'store']);
    Route::put('/designs/{id}', [DesignController::class, 'update']);
    Route::delete('/designs/{id}', [DesignController::class, 'destroy']);
    Route::get('/designs/{id}/template', [DesignController::class, 'redirectToTemplate']);
});

Route::prefix('user')->middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::get('/dashboard', function (Request $request) {
        return response()->json([
            'message' => 'Halo User Biasa',
            'user' => $request->user(),
        ], 200);
    });

    Route::get('/profile', function (Request $request) {
        return response()->json($request->user(), 200);
    });
});
