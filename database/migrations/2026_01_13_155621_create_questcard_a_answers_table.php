
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questcard_a_answers', function (Blueprint $table) {
            // ✅ fix tipe kolom jadi JSON (kalau sebelumnya bukan json)
            // butuh doctrine/dbal kalau kamu pakai ->change()
            if (Schema::hasColumn('questcard_a_answers', 'pembeli_utama')) {
                $table->json('pembeli_utama')->nullable()->change();
            }
            if (Schema::hasColumn('questcard_a_answers', 'cara_jual')) {
                $table->json('cara_jual')->nullable()->change();
            }

            // ✅ kolom-kolom baru sesuai backend baru (add kalau belum ada)
            if (!Schema::hasColumn('questcard_a_answers', 'q7_pujian')) {
                $table->json('q7_pujian')->nullable();
            }
            if (!Schema::hasColumn('questcard_a_answers', 'q8_kesulitan')) {
                $table->json('q8_kesulitan')->nullable();
            }
            if (!Schema::hasColumn('questcard_a_answers', 'q8_detail')) {
                $table->json('q8_detail')->nullable();
            }
            if (!Schema::hasColumn('questcard_a_answers', 'q9_waktu_ramai')) {
                $table->json('q9_waktu_ramai')->nullable();
            }
            if (!Schema::hasColumn('questcard_a_answers', 'q9_hari_tentu')) {
                $table->string('q9_hari_tentu', 255)->nullable();
            }
            if (!Schema::hasColumn('questcard_a_answers', 'q10_tujuan_beli')) {
                $table->json('q10_tujuan_beli')->nullable();
            }
            if (!Schema::hasColumn('questcard_a_answers', 'q10_lainnya')) {
                $table->string('q10_lainnya', 255)->nullable();
            }
            if (!Schema::hasColumn('questcard_a_answers', 'q11_ancaman')) {
                $table->json('q11_ancaman')->nullable();
            }

            if (!Schema::hasColumn('questcard_a_answers', 'swot_s')) {
                $table->longText('swot_s')->nullable();
            }
            if (!Schema::hasColumn('questcard_a_answers', 'swot_w')) {
                $table->longText('swot_w')->nullable();
            }
            if (!Schema::hasColumn('questcard_a_answers', 'swot_o')) {
                $table->longText('swot_o')->nullable();
            }
            if (!Schema::hasColumn('questcard_a_answers', 'swot_t')) {
                $table->longText('swot_t')->nullable();
            }

            if (!Schema::hasColumn('questcard_a_answers', 'saran_3_bulan')) {
                $table->json('saran_3_bulan')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('questcard_a_answers', function (Blueprint $table) {
            // optional: rollback kolom-kolom tambahan
            $drops = [
                'q7_pujian','q8_kesulitan','q8_detail','q9_waktu_ramai','q9_hari_tentu',
                'q10_tujuan_beli','q10_lainnya','q11_ancaman',
                'swot_s','swot_w','swot_o','swot_t','saran_3_bulan',
            ];

            foreach ($drops as $col) {
                if (Schema::hasColumn('questcard_a_answers', $col)) {
                    $table->dropColumn($col);
                }
            }

            // kalau mau balikin tipe lama pembeli_utama/cara_jual, tentuin tipe lamanya.
            // (biasanya tidak perlu)
        });
    }
};