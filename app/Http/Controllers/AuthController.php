<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * =========================
     * REGISTER
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

        $user = User::create([
            'nama'       => $request->nama,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'no_telpon'  => $request->no_telpon,
            'nama_usaha' => $request->nama_usaha,
        ]);

        Auth::login($user);

        return response()->json([
            'message' => 'Register berhasil',
            'user' => $user
        ], 201);
    }

    /**
     * =========================
     * LOGIN
     * =========================
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {

            return response()->json([
                'message' => 'Login berhasil',
                'user' => Auth::user()
            ], 200);
        }        

        return response()->json([
            'message' => 'Email atau password salah'
        ], 401);
    }

    /**
     * =========================
     * LOGOUT
     * =========================
     */
    public function logout(Request $request)
{
    Auth::logout();

    return response()->json([
        'message' => 'Logout berhasil'
    ], 200);
}
}