<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\Api\DesignController;
use App\Http\Controllers\KenaliMerkController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\FormPendaftaranController;
use App\Http\Controllers\TanyaDewiController;
use App\Models\DaftarToko;

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
| PUBLIC API
|--------------------------------------------------------------------------
*/

// RESEP
Route::get('/reseps', [ResepController::class, 'index']);
Route::get('/reseps/{resep}', [ResepController::class, 'show']);

// TOKO (hanya approved)
Route::get('/toko', [FormPendaftaranController::class, 'index']);

// DESIGN
Route::get('/designs', [DesignController::class, 'index']);
Route::get('/designs/{id}', [DesignController::class, 'show']);
Route::get('/designs/{id}/template', [DesignController::class, 'redirectToTemplate']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER (SANCTUM)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
            'verified' => $request->user()->hasVerifiedEmail(),
        ]);
    });

    // RESEP CRUD
    Route::post('/reseps', [ResepController::class, 'store']);
    Route::put('/reseps/{resep}', [ResepController::class, 'update']);
    Route::delete('/reseps/{resep}', [ResepController::class, 'destroy']);

    // TOKO (USER LOGIN)
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

        Route::get('/dashboard', function (Request $request) {
            return response()->json([
                'message' => 'Halo Admin',
                'user' => $request->user(),
            ]);
        });

        // TOKO MANAGEMENT
        Route::get('/toko', [FormPendaftaranController::class, 'adminIndex']);
        Route::patch('/toko/{id}/approve', [FormPendaftaranController::class, 'approve']);
        Route::patch('/toko/{id}/reject', [FormPendaftaranController::class, 'reject']);

        // USER MANAGEMENT
        Route::apiResource('/users', UserController::class);

        // DESIGN MANAGEMENT
        Route::post('/designs', [DesignController::class, 'store']);
        Route::put('/designs/{id}', [DesignController::class, 'update']);
        Route::delete('/designs/{id}', [DesignController::class, 'destroy']);
    });

/*
|--------------------------------------------------------------------------
| USER ROLE
|--------------------------------------------------------------------------
*/
Route::prefix('user')
    ->middleware(['auth:sanctum', 'role:user'])
    ->group(function () {

        Route::get('/dashboard', function (Request $request) {
            return response()->json([
                'message' => 'Halo User',
                'user' => $request->user(),
            ]);
        });

        Route::get('/profile', fn (Request $request) => response()->json($request->user()));

        Route::patch('/profile', function (Request $request) {
            $user = $request->user();

            $validated = $request->validate([
                'nama' => ['sometimes', 'string', 'max:255'],
                'email' => [
                    'sometimes',
                    'email',
                    Rule::unique('users', 'email')->ignore($user->id),
                ],
                'no_telpon' => ['sometimes', 'string', 'max:30'],
                'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            }

            unset($validated['role']);

            $user->update($validated);

            return response()->json([
                'message' => 'Profile updated',
                'user' => $user->fresh(),
            ]);
        });
    });
