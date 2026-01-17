<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questcard_a_answers', function (Blueprint $table) {
            // SWOT (JSON biar fleksibel, sesuai casts array)
            if (!Schema::hasColumn('questcard_a_answers', 'swot_s')) {
                $table->json('swot_s')->nullable();
            }
            if (!Schema::hasColumn('questcard_a_answers', 'swot_w')) {
                $table->json('swot_w')->nullable();
            }
            if (!Schema::hasColumn('questcard_a_answers', 'swot_o')) {
                $table->json('swot_o')->nullable();
            }
            if (!Schema::hasColumn('questcard_a_answers', 'swot_t')) {
                $table->json('swot_t')->nullable();
            }

            // Saran 3 bulan (JSON)
            if (!Schema::hasColumn('questcard_a_answers', 'saran_3_bulan')) {
                $table->json('saran_3_bulan')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('questcard_a_answers', function (Blueprint $table) {
            if (Schema::hasColumn('questcard_a_answers', 'swot_s')) $table->dropColumn('swot_s');
            if (Schema::hasColumn('questcard_a_answers', 'swot_w')) $table->dropColumn('swot_w');
            if (Schema::hasColumn('questcard_a_answers', 'swot_o')) $table->dropColumn('swot_o');
            if (Schema::hasColumn('questcard_a_answers', 'swot_t')) $table->dropColumn('swot_t');
            if (Schema::hasColumn('questcard_a_answers', 'saran_3_bulan')) $table->dropColumn('saran_3_bulan');
        });
    }
};
