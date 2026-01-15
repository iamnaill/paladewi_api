<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kenali_merks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->text('makna_dibalik_produk')->nullable();          // Point 1
            $table->text('kenapa_dibutuhkan_dan_apa_beda')->nullable(); // Point 2 + 4 digabung
            $table->text('siapa_yang_kita_tuju')->nullable();          // Point 3

            $table->unsignedTinyInteger('skor')->default(0);           // 0 - 12
            $table->string('status')->default('belum_dinilai');        // belum_dinilai | agak_layak | layak_bgt

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kenali_merks');
    }
};
