<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('daftar_toko', function (Blueprint $table) {
        $table->string('status_approval')->default('pending')->after('gambar_produk');
        $table->timestamp('approved_at')->nullable()->after('status_approval');
        $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
        $table->text('alasan_reject')->nullable()->after('approved_by');
    });
}

public function down(): void
{
    Schema::table('daftar_toko', function (Blueprint $table) {
        $table->dropColumn(['status_approval','approved_at','approved_by','alasan_reject']);
    });
}
};