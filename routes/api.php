<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\Api\DesignController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\FormPendaftaranController;
use App\Http\Controllers\TanyaDewiController;

/*
|--------------------------------------------------------------------------
| AUTH (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/email/verify-otp', [AuthController::class, 'verifyEmailOtp']);
Route::post('/email/resend-otp', [AuthController::class, 'resendEmailOtp']);

/*
|--------------------------------------------------------------------------
| PUBLIC API (READ ONLY)
|--------------------------------------------------------------------------
*/
Route::get('/reseps', [ResepController::class, 'index']);
Route::get('/reseps/{resep}', [ResepController::class, 'show']);

Route::get('/toko', [FormPendaftaranController::class, 'index']);

// DESIGN PUBLIC (READ ONLY)
Route::get('/designs', [DesignController::class, 'index']); // list all + search via ?q=
Route::get('/designs/search', [DesignController::class, 'index']); // optional explicit search route
Route::get('/designs/{id}', [DesignController::class, 'show']);
Route::get('/designs/{id}/template', [DesignController::class, 'redirectToTemplate']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER (SANCTUM)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', fn (Request $request) => response()->json([
        'user' => $request->user(),
        'verified' => $request->user()->hasVerifiedEmail(),
    ]));

    // RESEP CRUD
    Route::post('/reseps', [ResepController::class, 'store']);
    Route::put('/reseps/{resep}', [ResepController::class, 'update']);
    Route::delete('/reseps/{resep}', [ResepController::class, 'destroy']);

    // DESIGN CRUD (USER LOGIN)
    Route::post('/designs', [DesignController::class, 'store']);
    Route::put('/designs/{id}', [DesignController::class, 'update']);
    Route::delete('/designs/{id}', [DesignController::class, 'destroy']);

    // TOKO
    Route::post('/toko', [FormPendaftaranController::class, 'store']);

    // QUEST
    Route::post('/quest/opening', [QuestController::class, 'opening']);
    Route::get('/quest/a', [QuestController::class, 'listA']);
    Route::post('/quest/a', [QuestController::class, 'storeA']);
    Route::post('/quest/a/submit', [QuestController::class, 'submitA']);

    // TANYA DEWI
    Route::prefix('tanya-dewi')->group(function () {
        Route::post('/sessions', [TanyaDewiController::class, 'startSession']);
        Route::post('/sessions/{sessionId}/messages', [TanyaDewiController::class, 'sendMessage']);
        Route::get('/sessions/{sessionId}/messages', [TanyaDewiController::class, 'getMessages']);
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth:sanctum', 'role:admin'])
    ->group(function () {

        Route::get('/dashboard', fn (Request $request) => response()->json([
            'message' => 'Halo Admin',
            'user' => $request->user(),
        ]));

        Route::get('/toko', [FormPendaftaranController::class, 'adminIndex']);
        Route::patch('/toko/{id}/approve', [FormPendaftaranController::class, 'approve']);
        Route::patch('/toko/{id}/reject', [FormPendaftaranController::class, 'reject']);

        Route::apiResource('/users', UserController::class);

        // DESIGN CRUD (ADMIN)
        Route::post('/designs', [DesignController::class, 'store']);
        Route::put('/designs/{id}', [DesignController::class, 'update']);
        Route::delete('/designs/{id}', [DesignController::class, 'destroy']);
    });
