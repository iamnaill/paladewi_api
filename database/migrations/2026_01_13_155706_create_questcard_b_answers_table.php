<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questcard_b_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('q1_pala_ketersediaan')->nullable();
            $table->string('q2_pernah_olah')->nullable();
            $table->json('q2b_olah_apa')->nullable();

            $table->string('q3_diminta_orang')->nullable();
            $table->string('q4_bayar')->nullable();
            $table->string('q5_sanggup_produksi')->nullable();
            $table->string('q6_mau_jual')->nullable();

            $table->unsignedTinyInteger('skor_total')->default(0);
            $table->string('status')->nullable();
            $table->json('rekomendasi')->nullable();
            $table->text('output_text')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questcard_b_answers');
    }
};
