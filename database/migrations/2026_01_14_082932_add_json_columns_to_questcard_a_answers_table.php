<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questcard_a_answers', function (Blueprint $table) {

            /**
             * 1) FIX kolom yang KEJADIAN jadi JSON CHECK (longtext + CHECK json_valid)
             *    Kalau kolomnya sudah benar string/enum, bagian drop ini aman karena pakai hasColumn.
             */
            if (Schema::hasColumn('questcard_a_answers', 'pembeli_utama')) {
                $table->dropColumn('pembeli_utama');
            }
            if (Schema::hasColumn('questcard_a_answers', 'cara_jual')) {
                $table->dropColumn('cara_jual');
            }

            /**
             * 2) FIX kolom "hasil_output" / "ancaman" / dll kalau sebelumnya pernah kebentuk beda nama
             *    (opsional: drop kalau memang kamu gak pakai)
             *    Kalau di tabel kamu ada kolom-kolom lama dari versi sebelumnya, hapus biar gak bingung.
             *
             *    NOTE: Kalau kolom-kolom ini memang kamu masih butuh, jangan di-drop.
             */
            $oldColumns = [
                'strengths',
                'weaknesses',
                'kendala_kode',
                'kendala_detail',
                'waktu_laku',
                'keperluan_pembeli',
                'ancaman',
                'kendala_produksi',
                'kendala_jualan',
                'kendala_bahan',
                'kendala_alat_lingkungan',
                'hasil_output',
            ];

            foreach ($oldColumns as $col) {
                if (Schema::hasColumn('questcard_a_answers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('questcard_a_answers', function (Blueprint $table) {

            /**
             * 3) Tambah ulang kolom yang bener (STRING/ENUM, BUKAN JSON)
             */
            $table->enum('pembeli_utama', [
                'tetangga',
                'sekolah',
                'kantoran',
                'wisatawan',
                'online_luar_daerah',
            ])->after('harga_jual');

            $table->enum('cara_jual', [
                'titip_warung',
                'wa_grup_story',
                'pasar_offline',
                'marketplace',
                'ig_fb',
            ])->after('pembeli_utama');

            /**
             * 4) Pastikan JSON columns ada (kalau belum ada)
             */
            if (!Schema::hasColumn('questcard_a_answers', 'q7_pujian')) {
                $table->json('q7_pujian')->nullable()->after('produk_paling_laku');
            }
            if (!Schema::hasColumn('questcard_a_answers', 'q8_kesulitan')) {
                $table->json('q8_kesulitan')->nullable()->after('q7_pujian');
            }
            if (!Schema::hasColumn('questcard_a_answers', 'q8_detail')) {
                $table->json('q8_detail')->nullable()->after('q8_kesulitan');
            }
            if (!Schema::hasColumn('questcard_a_answers', 'q9_waktu_ramai')) {
                $table->json('q9_waktu_ramai')->nullable()->after('q8_detail');
            }
            if (!Schema::hasColumn('questcard_a_answers', 'q9_hari_tentu')) {
                $table->string('q9_hari_tentu')->nullable()->after('q9_waktu_ramai');
            }
            if (!Schema::hasColumn('questcard_a_answers', 'q10_tujuan_beli')) {
                $table->json('q10_tujuan_beli')->nullable()->after('q9_hari_tentu');
            }
            if (!Schema::hasColumn('questcard_a_answers', 'q10_lainnya')) {
                $table->string('q10_lainnya')->nullable()->after('q10_tujuan_beli');
            }
            if (!Schema::hasColumn('questcard_a_answers', 'q11_ancaman')) {
                $table->json('q11_ancaman')->nullable()->after('q10_lainnya');
            }
            if (!Schema::hasColumn('questcard_a_answers', 'result')) {
                $table->json('result')->nullable()->after('q11_ancaman');
            }
        });
    }

    public function down(): void
    {
        Schema::table('questcard_a_answers', function (Blueprint $table) {
            // rollback: hapus kolom yang kita tambahin
            $cols = [
                'q7_pujian',
                'q8_kesulitan',
                'q8_detail',
                'q9_waktu_ramai',
                'q9_hari_tentu',
                'q10_tujuan_beli',
                'q10_lainnya',
                'q11_ancaman',
                'result',
            ];

            foreach ($cols as $col) {
                if (Schema::hasColumn('questcard_a_answers', $col)) {
                    $table->dropColumn($col);
                }
            }

            if (Schema::hasColumn('questcard_a_answers', 'pembeli_utama')) {
                $table->dropColumn('pembeli_utama');
            }
            if (Schema::hasColumn('questcard_a_answers', 'cara_jual')) {
                $table->dropColumn('cara_jual');
            }
        });
    }
};
