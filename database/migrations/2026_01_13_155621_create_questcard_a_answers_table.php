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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('nama_produk')->nullable();
            $table->string('jenis_produk')->nullable();
            $table->string('jenis_produk_lainnya')->nullable();

            $table->string('harga_jual')->nullable();

            $table->json('pembeli_utama')->nullable();
            $table->json('cara_jual')->nullable();

            $table->string('produk_paling_laku')->nullable();

            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();

            $table->string('kendala_kode')->nullable();
            $table->string('kendala_detail')->nullable();

            $table->json('waktu_laku')->nullable();
            $table->json('keperluan_pembeli')->nullable();

            $table->json('ancaman')->nullable();

            $table->string('kendala_produksi')->nullable();
            $table->string('kendala_jualan')->nullable();
            $table->string('kendala_bahan')->nullable();
            $table->string('kendala_alat_lingkungan')->nullable();

            $table->json('hasil_output')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questcard_a_answers');
    }
};
