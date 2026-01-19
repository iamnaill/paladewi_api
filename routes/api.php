<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\Api\DesignController;
use App\Http\Controllers\KenaliMerkController;
use App\Http\Controllers\QuestController;
use App\Models\DaftarToko;
use App\Http\Controllers\FormPendaftaranController;

// AUTH (tanpa throttle)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/email/verify-otp', [AuthController::class, 'verifyEmailOtp']);
Route::post('/email/resend-otp', [AuthController::class, 'resendEmailOtp']);

// PUBLIC
Route::get('/reseps', [ResepController::class, 'index']);
Route::get('/reseps/{resep}', [ResepController::class, 'show']);

Route::get('/designs', [DesignController::class, 'index']);
Route::get('/designs/{id}', [DesignController::class, 'show']);
Route::get('/designs/{id}/template', [DesignController::class, 'redirectToTemplate']);

// AUTHENTICATED (sanctum)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return response()->json([
            'message' => 'User login saat ini',
            'user' => $request->user(),
            'verified' => $request->user()->hasVerifiedEmail(),
            'email_verified_at' => $request->user()->email_verified_at,
        ], 200);
    });

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Link verifikasi email dikirim ulang',
        ], 200);
    })->name('verification.send');

    // RESEP CRUD
    Route::post('/reseps', [ResepController::class, 'store']);
    Route::put('/reseps/{resep}', [ResepController::class, 'update']);
    Route::delete('/reseps/{resep}', [ResepController::class, 'destroy']);

    // KENALI MERK CRUD
    Route::get('/kenali-merk', [KenaliMerkController::class, 'index']);
    Route::post('/kenali-merk', [KenaliMerkController::class, 'store']);
    Route::get('/kenali-merk/{kenaliMerk}', [KenaliMerkController::class, 'show']);
    Route::put('/kenali-merk/{kenaliMerk}', [KenaliMerkController::class, 'update']);
    Route::delete('/kenali-merk/{kenaliMerk}', [KenaliMerkController::class, 'destroy']);

    // TOKO
    Route::post('/toko', [FormPendaftaranController::class, 'store']);

    Route::put('/toko/{toko}', function (DaftarToko $toko) {
        return $toko;
    });

    Route::delete('/toko/{toko}', function (DaftarToko $toko) {
        $toko->delete();
        return response()->json(['message' => 'Dihapus']);
    });

    // QUEST
    Route::post('/quest/opening', [QuestController::class, 'opening']);
    Route::get('/quest/a', [QuestController::class, 'listA']);
    Route::post('/quest/a', [QuestController::class, 'storeA']);
    Route::post('/quest/a/submit', [QuestController::class, 'submitA']);

    // MODUL (kayaknya duplicate ke KenaliMerk, tapi aku biarin sesuai aslinya)
    Route::get('/modul', [KenaliMerkController::class, 'index']);
    Route::post('/modul', [KenaliMerkController::class, 'store']);
});

// ADMIN
Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {

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

// USER
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
