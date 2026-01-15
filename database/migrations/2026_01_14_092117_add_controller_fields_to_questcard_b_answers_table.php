<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('questcard_b_answers', function (Blueprint $table) {
            if (!Schema::hasColumn('questcard_b_answers', 'q1_bahan_pala')) {
                $table->string('q1_bahan_pala')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('questcard_b_answers', 'q2_pernah_olah')) {
                $table->string('q2_pernah_olah')->nullable();
            }

            if (!Schema::hasColumn('questcard_b_answers', 'q2b_olah_apa')) {
                $table->json('q2b_olah_apa')->nullable();
            }

            if (!Schema::hasColumn('questcard_b_answers', 'q3_diminta_orang')) {
                $table->string('q3_diminta_orang')->nullable();
            }

            if (!Schema::hasColumn('questcard_b_answers', 'q4_bayar')) {
                $table->string('q4_bayar')->nullable();
            }

            if (!Schema::hasColumn('questcard_b_answers', 'q5_sanggup_bikin_sering')) {
                $table->string('q5_sanggup_bikin_sering')->nullable();
            }

            if (!Schema::hasColumn('questcard_b_answers', 'q6_mau_jual')) {
                $table->string('q6_mau_jual')->nullable();
            }

            if (!Schema::hasColumn('questcard_b_answers', 'score')) {
                $table->unsignedTinyInteger('score')->default(0);
            }

            if (!Schema::hasColumn('questcard_b_answers', 'status')) {
                $table->string('status')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('questcard_b_answers', function (Blueprint $table) {
            $cols = [
                'q1_bahan_pala',
                'q2_pernah_olah',
                'q2b_olah_apa',
                'q3_diminta_orang',
                'q4_bayar',
                'q5_sanggup_bikin_sering',
                'q6_mau_jual',
                'score',
                'status',
            ];

            foreach ($cols as $col) {
                if (Schema::hasColumn('questcard_b_answers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
