<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_telpon',
        'nama_usaha',
        'role',

        // ✅ tambahan OTP
        'email_otp',
        'email_otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_otp', // opsional, biar OTP nggak bocor di response
    ];

    protected $casts = [
        'email_verified_at'      => 'datetime',
        'email_otp_expires_at'   => 'datetime',
    ];
}
