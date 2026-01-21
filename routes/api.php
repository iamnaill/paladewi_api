<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\Api\DesignController;
use App\Http\Controllers\KenaliMerkController;
use App\Http\Controllers\QuestController;
use App\Models\DaftarToko;
use App\Http\Controllers\FormPendaftaranController;
use Illuminate\Validation\Rule;
use App\Http\Controllers\TanyaDewiController;

// AUTH (tanpa throttle)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/email/verify-otp', [AuthController::class, 'verifyEmailOtp']);
Route::post('/email/resend-otp', [AuthController::class, 'resendEmailOtp']);

// PUBLIC
Route::apiResource('reseps', ResepController::class);

Route::get('/reseps', [ResepController::class, 'index']);
Route::get('/reseps/{resep}', [ResepController::class, 'show']);

Route::get('/toko', function () {
    return response()->json([
        'data' => DaftarToko::latest()->get()
    ], 200);
});

Route::get('/reseps', [ResepController::class, 'index']);
Route::get('/reseps/{resep}', [ResepController::class, 'show']);

Route::get('/toko', [FormPendaftaranController::class, 'index']);

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
    Route::get('/reseps', [ResepController::class, 'index']); // search di sini
    Route::get('/reseps/{resep}', [ResepController::class, 'show']);
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
     // ✅ APPROVAL TOKO/PRODUK
    Route::patch('/toko/{toko}/approve', function (DaftarToko $toko, Request $request) {
        $toko->update([
            'status_approval' => 'approved',
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'alasan_reject' => null,
        ]);

        return response()->json([
            'message' => 'Produk berhasil di-approve',
            'data' => $toko
        ], 200);
    });

    Route::patch('/toko/{toko}/reject', function (DaftarToko $toko, Request $request) {
        $request->validate([
            'alasan_reject' => ['nullable','string','max:255'],
        ]);

        $toko->update([
            'status_approval' => 'rejected',
            'approved_at' => null,
            'approved_by' => $request->user()->id,
            'alasan_reject' => $request->alasan_reject,
        ]);

        return response()->json([
            'message' => 'Produk berhasil di-reject',
            'data' => $toko
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

    // ✅ UPDATE profile user (PATCH lebih cocok untuk update sebagian field)
    Route::patch('/profile', function (Request $request) {
        $user = $request->user();

        $validated = $request->validate([
            'nama'       => ['sometimes', 'string', 'max:255'],
            'email'      => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'no_telpon'  => ['sometimes', 'string', 'max:30'],
            'nama_usaha' => ['sometimes', 'string', 'max:255'],

            // opsional: update password
            'password'   => ['sometimes', 'string', 'min:8', 'confirmed'],
            // butuh field: password_confirmation
        ]);

        // kalau email berubah, biasanya verifikasi ulang + set verified_at null
        if (isset($validated['email']) && $validated['email'] !== $user->email) {
            $validated['email_verified_at'] = null;
            // optional: di sini kamu bisa trigger kirim verifikasi email lagi
        }

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        // ✅ keamanan: user tidak boleh update role lewat endpoint ini
        unset($validated['role']);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile berhasil diperbarui',
            'user' => $user->fresh(),
        ], 200);
    });

});

Route::middleware('auth:sanctum')->prefix('tanya-dewi')->group(function () {
    Route::post('/sessions', [TanyaDewiController::class, 'startSession']);
    Route::post('/sessions/{sessionId}/messages', [TanyaDewiController::class, 'sendMessage']);
    Route::get('/sessions/{sessionId}/messages', [TanyaDewiController::class, 'getMessages']);
});

// Route::get('/debug-agent', function () {
//     return response()->json([
//         'APP_URL' => env('APP_URL'),
//         'AGENT_URL' => env('AGENT_URL'),
//         'AGENT_TIMEOUT' => env('AGENT_TIMEOUT'),
//         'has_token' => (bool) env('AGENT_TOKEN'),
//     ]);
// });

// Route::get('/agent-ping', function () {
//     $resp = Http::timeout(10)
//         ->withHeaders([
//             'X-App-Token' => env('AGENT_TOKEN'),
//             'Accept' => 'application/json',
//         ])
//         ->post(env('AGENT_URL'), [
//             'session_id' => 'test-session',
//             'user_id' => 1,
//             'message' => 'ping',
//             'history' => [],
//         ]);

//     return response()->json([
//         'status' => $resp->status(),
//         'body_raw' => $resp->body(),
//     ]);
// });