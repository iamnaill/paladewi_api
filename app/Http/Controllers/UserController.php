<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * =========================
     * GET ALL USERS
     * =========================
     */
    public function index()
    {
        $users = User::all();

        return response()->json([
            'message' => 'List semua user',
            'data' => $users
        ], 200);
    }

    /**
     * =========================
     * CREATE USER
     * =========================
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users,email',
            'password'   => 'required|string|min:6',
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

        return response()->json([
            'message' => 'User berhasil dibuat',
            'data' => $user
        ], 201);
    }

    /**
     * =========================
     * GET USER BY ID
     * =========================
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail user',
            'data' => $user
        ], 200);
    }

    /**
     * =========================
     * UPDATE USER
     * =========================
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'nama'       => 'sometimes|required|string|max:255',
            'email'      => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password'   => 'sometimes|nullable|string|min:6',
            'no_telpon'  => 'nullable|string|max:20',
            'nama_usaha' => 'nullable|string|max:255',
        ]);

        $user->nama = $request->nama ?? $user->nama;
        $user->email = $request->email ?? $user->email;
        $user->no_telpon = $request->no_telpon ?? $user->no_telpon;
        $user->nama_usaha = $request->nama_usaha ?? $user->nama_usaha;

        // kalau password dikirim, update password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'User berhasil diupdate',
            'data' => $user
        ], 200);
    }

    /**
     * =========================
     * DELETE USER
     * =========================
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ], 200);
    }
}