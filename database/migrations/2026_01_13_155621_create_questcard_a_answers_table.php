<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questcard_a_answers', function (Blueprint $table) {
            $table->id();

            // relasi user (sesuaikan kalau user table kamu beda)
            $table->unsignedBigInteger('user_id');

            // ==== Quest A core ====
            $table->string('nama_produk', 255)->nullable();
            $table->string('jenis_produk', 50)->nullable(); // makanan|minuman|kerajinan|skincare
            $table->string('harga_jual', 20)->nullable();   // <5rb|5-10rb|10-25rb|>25rb

            // ==== ini yang bikin error kamu: harus JSON ====
            $table->json('pembeli_utama')->nullable();
            $table->json('cara_jual')->nullable();

            $table->string('produk_paling_laku', 255)->nullable();

            // ==== tambahan backend baru ====
            $table->json('q7_pujian')->nullable();
            $table->json('q8_kesulitan')->nullable();
            $table->json('q8_detail')->nullable();

            $table->json('q9_waktu_ramai')->nullable();
            $table->string('q9_hari_tentu', 255)->nullable();

            $table->json('q10_tujuan_beli')->nullable();
            $table->string('q10_lainnya', 255)->nullable();

            $table->json('q11_ancaman')->nullable();

            // ==== hasil generate backend (swot + saran) ====
            $table->longText('swot_s')->nullable();
            $table->longText('swot_w')->nullable();
            $table->longText('swot_o')->nullable();
            $table->longText('swot_t')->nullable();
            $table->json('saran_3_bulan')->nullable();

            $table->timestamps();

            // index + foreign key (optional tapi recommended)
            $table->index('user_id');

            // kalau tabel users ada:
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questcard_a_answers');
    }
};