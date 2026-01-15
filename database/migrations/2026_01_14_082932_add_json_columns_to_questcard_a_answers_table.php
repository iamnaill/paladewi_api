<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questcard_a_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('nama_produk');
            $table->string('jenis_produk');
            $table->string('jenis_produk_lainnya')->nullable();
            $table->string('harga_jual');
            $table->string('pembeli_utama');
            $table->string('cara_jual');
            $table->string('produk_paling_laku');

            $table->json('q7_pujian')->nullable();
            $table->json('q8_kesulitan')->nullable();
            $table->json('q8_detail')->nullable();
            $table->json('q9_waktu_ramai')->nullable();
            $table->string('q9_hari_tentu')->nullable();
            $table->json('q10_tujuan_beli')->nullable();
            $table->string('q10_lainnya')->nullable();
            $table->json('q11_ancaman')->nullable();

            $table->json('result')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questcard_a_answers');
    }
};
