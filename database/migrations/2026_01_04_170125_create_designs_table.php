<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designs', function (Blueprint $table) {
            $table->id();

            // === ISI DATA DESIGN KEMASAN ===
            $table->string('title');             // Judul Desain Produk
            $table->string('template_link');     // Link template (Drive/Figma)
            $table->string('image_path');        // Path foto JPG/PNG
            $table->string('category');          // Kategori produk

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designs');
    }
};
