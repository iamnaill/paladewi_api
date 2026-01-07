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
        Schema::create('daftar_toko', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko');
            $table->string('no_wa');
            $table->string('link_ecommerce')->nullable();
            $table->string('kategori_produk');
            $table->text('bio_toko');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_toko');
    }
};
