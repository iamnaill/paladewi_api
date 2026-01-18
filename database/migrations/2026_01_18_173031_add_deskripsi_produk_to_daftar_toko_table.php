<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daftar_toko', function (Blueprint $table) {
            $table->text('deskripsi_produk')->nullable()->after('bio_toko');
        });
    }

    public function down(): void
    {
        Schema::table('daftar_toko', function (Blueprint $table) {
            $table->dropColumn('deskripsi_produk');
        });
    }
};
