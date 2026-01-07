<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_sections', function (Blueprint $table) {
            $table->id();
            $table->enum('page', ['brand_bersuara', 'kenalimerk']);
            $table->unsignedTinyInteger('section');
            $table->string('judul');
            $table->longText('isi');
            $table->timestamps();

            $table->unique(['page', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_sections');
    }
};

