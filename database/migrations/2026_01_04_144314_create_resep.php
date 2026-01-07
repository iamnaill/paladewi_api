<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseps', function (Blueprint $table) {
            $table->id();

            $table->enum('kategori_produk', ['makanan', 'minuman', 'kecantikan', 'wewangian']);
            $table->string('kategori_pala')->nullable();

            $table->string('nama_resep');
            $table->text('deskripsi')->nullable();

            $table->longText('alat_dan_bahan');
            $table->longText('steps');

            $table->string('foto_produk')->nullable();
            $table->json('foto_rekomendasi_kemasan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseps');
    }
};
