<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\EmailOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * =========================
     * REGISTER (tanpa token)
     * =========================
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',
            'no_telpon'  => 'nullable|string|max:20',
            'nama_usaha' => 'nullable|string|max:255',
        ]);

        // OTP 6 digit
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'nama'       => $request->nama,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'no_telpon'  => $request->no_telpon,
            'nama_usaha' => $request->nama_usaha,

            // simpan OTP
            'email_otp' => $otp,
            'email_otp_expires_at' => now()->addMinutes(10),
        ]);

        // safety check: pastikan kolom OTP benar-benar tersimpan
        if (!isset($user->email_otp)) {
            return response()->json([
                'message' => 'Kolom OTP belum ada di database. Pastikan migration email_otp sudah dijalankan.',
            ], 500);
        }

        // kirim OTP ke email
        Mail::to($user->email)->send(new EmailOtpMail($otp));

        return response()->json([
            'message' => 'Register berhasil, silakan cek email untuk kode verifikasi (OTP).',
            'user' => $user->only(['id', 'nama', 'email']),
        ], 201);
    }

    /**
     * =========================
     * VERIFY EMAIL VIA OTP
     * =========================
     */
    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah diverifikasi'], 200);
        }

        if (!$user->email_otp || !$user->email_otp_expires_at) {
            return response()->json(['message' => 'OTP tidak tersedia. Silakan minta OTP baru.'], 400);
        }

        if (now()->gt($user->email_otp_expires_at)) {
            return response()->json(['message' => 'OTP sudah expired. Silakan minta OTP baru.'], 400);
        }

        if ($user->email_otp !== $request->otp) {
            return response()->json(['message' => 'OTP salah'], 400);
        }

        // sukses: set verified_at
        $user->forceFill([
            'email_verified_at' => now(),
            'email_otp' => null,
            'email_otp_expires_at' => null,
        ])->save();

        return response()->json([
            'message' => 'Email berhasil diverifikasi',
            'email_verified_at' => $user->email_verified_at,
        ], 200);
    }

    /**
     * =========================
     * RESEND OTP (tanpa login)
     * =========================
     */
    public function resendEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah diverifikasi'], 200);
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'email_otp' => $otp,
            'email_otp_expires_at' => now()->addMinutes(10),
        ])->save();

        Mail::to($user->email)->send(new EmailOtpMail($otp));

        return response()->json([
            'message' => 'OTP baru berhasil dikirim ke email',
        ], 200);
    }

    /**
     * =========================
     * LOGIN (buat token)
     * =========================
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email belum diverifikasi. Silakan verifikasi dengan OTP terlebih dahulu.',
            ], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * =========================
     * LOGOUT
     * =========================
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->currentAccessToken()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil'], 200);
    }
}
