<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Paladewi API running'], 200);
});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return "Email berhasil diverifikasi. Silakan kembali ke aplikasi.";
})->middleware(['signed'])->name('verification.verify');
