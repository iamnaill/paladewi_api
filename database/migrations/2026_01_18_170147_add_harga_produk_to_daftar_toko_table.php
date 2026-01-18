<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('daftar_toko', function (Blueprint $table) {
        $table->decimal('harga_produk', 12, 0)->default(0)->after('bio_toko');
    });
}

public function down(): void
{
    Schema::table('daftar_toko', function (Blueprint $table) {
        $table->dropColumn('harga_produk');
    });
}

};
