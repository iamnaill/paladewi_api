<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestcardAAnswer;

class QuestController extends Controller
{
    // ==========================
    // Helpers
    // ==========================
    private function optionLabel(array $options, $value): ?string
    {
        foreach ($options as $opt) {
            if (($opt['value'] ?? null) === $value) return $opt['label'] ?? null;
        }
        return null;
    }

    private function optionLabels(array $options, array $values): array
    {
        $out = [];
        foreach ($values as $v) {
            $lbl = $this->optionLabel($options, $v);
            $out[] = $lbl ?? $v;
        }
        return $out;
    }

    // ==========================
    // QUEST A - SCHEMA (tanpa jasa / tanpa lainnya)
    // ==========================
    public function schemaA()
    {
        return response()->json([
            'quest' => 'A',
            'title' => 'QuestCard A (Sudah punya produk)',
            'fields' => [
                [
                    'key' => 'nama_produk',
                    'label' => 'Nama Produk?',
                    'type' => 'text',
                    'required' => true,
                ],
                [
                    'key' => 'jenis_produk',
                    'label' => 'Jenis Produk',
                    'type' => 'radio',
                    'required' => true,
                    'options' => [
                        ['value' => 'makanan',   'label' => 'Makanan'],
                        ['value' => 'minuman',   'label' => 'Minuman'],
                        ['value' => 'kerajinan', 'label' => 'Kerajinan'],
                        ['value' => 'skincare',  'label' => 'Skincare'],
                    ],
                ],
                [
                    'key' => 'harga_jual',
                    'label' => 'Harga Jual',
                    'type' => 'radio',
                    'required' => true,
                    'options' => [
                        ['value' => '<5rb',     'label' => '< 5rb'],
                        ['value' => '5-10rb',   'label' => '5-10rb'],
                        ['value' => '10-25rb',  'label' => '10-25rb'],
                        ['value' => '>25rb',    'label' => '>25rb'],
                    ],
                ],
                [
                    'key' => 'pembeli_utama',
                    'label' => 'Pembeli Utama',
                    'type' => 'checkbox',
                    'required' => true,
                    'options' => [
                        ['value' => 'tetangga',           'label' => 'Tetangga / Satu Daerah'],
                        ['value' => 'sekolah',            'label' => 'Anak atau Pegawai Sekolah'],
                        ['value' => 'kantoran',           'label' => 'Orang Kantoran'],
                        ['value' => 'wisatawan',          'label' => 'Wisatawan'],
                        ['value' => 'online_luar_daerah', 'label' => 'Pembeli Online (Luar Daerah)'],
                    ],
                ],
                [
                    'key' => 'cara_jual',
                    'label' => 'Cara Jual Paling Sering',
                    'type' => 'checkbox',
                    'required' => true,
                    'options' => [
                        ['value' => 'titip_warung',  'label' => 'Titip ke Warung Terdekat'],
                        ['value' => 'wa_grup_story', 'label' => 'Promosi ke Grup WhatsApp dan Story WA'],
                        ['value' => 'pasar_offline', 'label' => 'Pasar Offline'],
                        ['value' => 'marketplace',   'label' => 'Marketplace (Shopee, Tiktok Shop, dsb)'],
                        ['value' => 'ig_fb',         'label' => 'IG/FB'],
                    ],
                ],
                [
                    'key' => 'produk_paling_laku',
                    'label' => 'Produk paling laku itu apa?',
                    'type' => 'text',
                    'required' => true,
                ],
                [
                    'key' => 'q7_pujian',
                    'label' => 'Hal yang paling sering dipuji (max 2)',
                    'type' => 'checkbox',
                    'max' => 2,
                    'required' => true,
                    'options' => [
                        ['value' => 'rasanya_enak',      'label' => 'Rasanya enak dan konsisten'],
                        ['value' => 'bahan_berkualitas', 'label' => 'Bahan terasa bagus/berkualitas'],
                        ['value' => 'porsi_pas',         'label' => 'Porsi/isi pas dan sesuai harapan'],
                        ['value' => 'harga_sepadan',     'label' => 'Harga terjangkau/sepadan kualitas'],
                        ['value' => 'kemasan_aman',      'label' => 'Kemasan rapi dan aman'],
                        ['value' => 'label_jelas',       'label' => 'Label/informasi jelas'],
                        ['value' => 'ciri_khas',         'label' => 'Produk punya ciri khas/beda'],
                    ],
                ],
                [
                    'key' => 'q8_kesulitan',
                    'label' => 'Bagian usaha yang paling sering bikin kesulitan (max 2)',
                    'type' => 'checkbox',
                    'max' => 2,
                    'required' => true,
                    'options' => [
                        ['value' => 'produksi',     'label' => 'Produksi'],
                        ['value' => 'penjualan',    'label' => 'Penjualan'],
                        ['value' => 'promosi',      'label' => 'Promosi'],
                        ['value' => 'pengemasan',   'label' => 'Pengemasan'],
                        ['value' => 'bahan_baku',   'label' => 'Bahan baku'],
                        ['value' => 'tenaga_waktu', 'label' => 'Tenaga/Waktu'],
                    ],
                ],
                [
                    'key' => 'q8_detail',
                    'label' => 'Detail kendala (isi sesuai yang dipilih di Q8)',
                    'type' => 'object',
                    'required' => false,
                ],
                [
                    'key' => 'q9_waktu_ramai',
                    'label' => 'Usaha paling sering laku pada waktu (max 2)',
                    'type' => 'checkbox',
                    'max' => 2,
                    'required' => true,
                    'options' => [
                        ['value' => 'pagi',          'label' => 'Pagi (05.00–10.00)'],
                        ['value' => 'siang',         'label' => 'Siang (10.00–14.00)'],
                        ['value' => 'sore',          'label' => 'Sore (14.00–18.00)'],
                        ['value' => 'malam',         'label' => 'Malam (18.00–22.00)'],
                        ['value' => 'akhir_pekan',   'label' => 'Akhir pekan (Sabtu–Minggu)'],
                        ['value' => 'hari_tentu',    'label' => 'Hari festival/hari tertentu'],
                        ['value' => 'acara',         'label' => 'Kalau ada acara'],
                        ['value' => 'musim',         'label' => 'Musim tertentu (Ramadan/Lebaran/liburan/panen)'],
                        ['value' => 'tidak_menentu', 'label' => 'Tidak menentu/tergantung'],
                    ],
                ],
                [
                    'key' => 'q9_hari_tentu',
                    'label' => 'Jika pilih "hari_tentu", tulis harinya',
                    'type' => 'text',
                    'required' => false,
                ],
                [
                    'key' => 'q10_tujuan_beli',
                    'label' => 'Biasanya pembeli membeli untuk.. (boleh lebih dari satu)',
                    'type' => 'checkbox',
                    'required' => true,
                    'options' => [
                        ['value' => 'konsumsi',  'label' => 'Konsumsi sehari-hari'],
                        ['value' => 'acara',     'label' => 'Acara (arisan, hajatan, pengajian)'],
                        ['value' => 'reseller',  'label' => 'Dijual kembali (reseller/warung)'],
                        ['value' => 'oleh_oleh', 'label' => 'Oleh-oleh'],
                        ['value' => 'lainnya',   'label' => 'Lainnya'],
                    ],
                ],
                [
                    'key' => 'q10_lainnya',
                    'label' => 'Jika lainnya, tulis tujuan beli',
                    'type' => 'text',
                    'required' => false,
                ],
                [
                    'key' => 'q11_ancaman',
                    'label' => 'Hal yang paling sering mengganggu usaha (max 2)',
                    'type' => 'checkbox',
                    'max' => 2,
                    'required' => true,
                    'options' => [
                        ['value' => 'produk_saingan',     'label' => 'Banyak pesaing / produk mirip'],
                        ['value' => 'harga_bahan_naik',   'label' => 'Harga bahan baku naik'],
                        ['value' => 'bahan_sulit',        'label' => 'Bahan baku sulit / musiman'],
                        ['value' => 'cuaca_listrik_alat', 'label' => 'Cuaca/listrik/alat mengganggu produksi'],
                        ['value' => 'daya_beli_turun',    'label' => 'Pembeli sepi karena daya beli turun'],
                        ['value' => 'pengiriman_akses',   'label' => 'Masalah pengiriman/jarak/akses pasar'],
                    ],
                ],
            ],
        ], 200);
    }

    // ==========================
    // QUEST A - STORE
    // ==========================
    public function storeA(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'jenis_produk' => 'required|in:makanan,minuman,kerajinan,skincare',
            'harga_jual' => 'required|in:<5rb,5-10rb,10-25rb,>25rb',

            'pembeli_utama' => 'required|array|min:1',
            'pembeli_utama.*' => 'in:tetangga,sekolah,kantoran,wisatawan,online_luar_daerah',

            'cara_jual' => 'required|array|min:1',
            'cara_jual.*' => 'in:titip_warung,wa_grup_story,pasar_offline,marketplace,ig_fb',

            'produk_paling_laku' => 'required|string|max:255',

            'q7_pujian' => 'required|array|max:2',
            'q7_pujian.*' => 'string',

            'q8_kesulitan' => 'required|array|max:2',
            'q8_kesulitan.*' => 'string',

            'q8_detail' => 'nullable|array',

            'q9_waktu_ramai' => 'required|array|max:2',
            'q9_waktu_ramai.*' => 'string',

            'q9_hari_tentu' => 'nullable|string|max:255',

            'q10_tujuan_beli' => 'required|array',
            'q10_tujuan_beli.*' => 'string',

            'q10_lainnya' => 'nullable|string|max:255',

            'q11_ancaman' => 'required|array|max:2',
            'q11_ancaman.*' => 'string',
        ]);

        $userId = $request->user()->id;

        // default optional
        $validated['q8_detail'] = $validated['q8_detail'] ?? [];
        $validated['q9_hari_tentu'] = $validated['q9_hari_tentu'] ?? '-';
        $validated['q10_lainnya'] = $validated['q10_lainnya'] ?? '-';

        $resultArr = $this->buildResultA($request);
        if (!$resultArr) $resultArr = [];

        $data = [
            'user_id' => $userId,
            'nama_produk' => $validated['nama_produk'],
            'jenis_produk' => $validated['jenis_produk'],
            'harga_jual' => $validated['harga_jual'],
            'pembeli_utama' => $validated['pembeli_utama'],
            'cara_jual' => $validated['cara_jual'],
            'produk_paling_laku' => $validated['produk_paling_laku'],

            'q7_pujian' => $validated['q7_pujian'],
            'q8_kesulitan' => $validated['q8_kesulitan'],
            'q8_detail' => $validated['q8_detail'],
            'q9_waktu_ramai' => $validated['q9_waktu_ramai'],
            'q9_hari_tentu' => $validated['q9_hari_tentu'],
            'q10_tujuan_beli' => $validated['q10_tujuan_beli'],
            'q10_lainnya' => $validated['q10_lainnya'],
            'q11_ancaman' => $validated['q11_ancaman'],

            'swot_s' => $resultArr['swot']['strength'] ?? null,
            'swot_w' => $resultArr['swot']['weakness'] ?? null,
            'swot_o' => $resultArr['swot']['opportunity'] ?? null,
            'swot_t' => $resultArr['swot']['threat'] ?? null,

            'saran_3_bulan' => $resultArr['saran_3_bulan'] ?? null,
        ];

        $item = QuestcardAAnswer::create($data);

        return response()->json([
            'message' => 'QuestCard A tersimpan',
            'data' => $item,
        ], 201);
    }

    // ==========================
    // QUEST A - BUILD RESULT (SWOT sesuai modul + kesimpulan rapih + saran 3 bulan)
    // ==========================
    private function buildResultA(Request $request): array
    {
        $schema = $this->schemaA()->getData(true);
        $fields = collect($schema['fields']);

        $jenisOpts   = $fields->firstWhere('key', 'jenis_produk')['options'] ?? [];
        $hargaOpts   = $fields->firstWhere('key', 'harga_jual')['options'] ?? [];
        $pembeliOpts = $fields->firstWhere('key', 'pembeli_utama')['options'] ?? [];
        $caraOpts    = $fields->firstWhere('key', 'cara_jual')['options'] ?? [];

        $q7Opts  = $fields->firstWhere('key', 'q7_pujian')['options'] ?? [];
        $q8Opts  = $fields->firstWhere('key', 'q8_kesulitan')['options'] ?? [];
        $q9Opts  = $fields->firstWhere('key', 'q9_waktu_ramai')['options'] ?? [];
        $q10Opts = $fields->firstWhere('key', 'q10_tujuan_beli')['options'] ?? [];
        $q11Opts = $fields->firstWhere('key', 'q11_ancaman')['options'] ?? [];

        $pembeliLabels  = $this->optionLabels($pembeliOpts, (array) $request->pembeli_utama);
        $caraJualLabels = $this->optionLabels($caraOpts, (array) $request->cara_jual);

        $strengthLabels = $this->optionLabels($q7Opts, (array) $request->q7_pujian);
        $weakLabels     = $this->optionLabels($q8Opts, (array) $request->q8_kesulitan);
        $oppTimeLabels  = $this->optionLabels($q9Opts, (array) $request->q9_waktu_ramai);
        $oppNeedLabels  = $this->optionLabels($q10Opts, (array) $request->q10_tujuan_beli);
        $threatLabels   = $this->optionLabels($q11Opts, (array) $request->q11_ancaman);

        $q7_1 = $strengthLabels[0] ?? '-';
        $q7_2 = $strengthLabels[1] ?? '-';

        $q8_1 = $weakLabels[0] ?? '-';
        $q8_2 = $weakLabels[1] ?? '-';

        $q9_1 = $oppTimeLabels[0] ?? '-';
        $q9_2 = $oppTimeLabels[1] ?? '-';

        $q11_1 = $threatLabels[0] ?? '-';
        $q11_2 = $threatLabels[1] ?? '-';

        // detail kendala Q8 per pilihan
        $q8Detail = $request->q8_detail ?? [];
        if (!is_array($q8Detail)) $q8Detail = [];

        $k1 = $request->q8_kesulitan[0] ?? null;
        $k2 = $request->q8_kesulitan[1] ?? null;

        $detail1 = ($k1 && array_key_exists($k1, $q8Detail)) ? $q8Detail[$k1] : null;
        $detail2 = ($k2 && array_key_exists($k2, $q8Detail)) ? $q8Detail[$k2] : null;

        $ringkasan = [
            'nama_produk' => $request->nama_produk,
            'jenis_produk' => $this->optionLabel($jenisOpts, $request->jenis_produk) ?? $request->jenis_produk,
            'harga_jual' => $this->optionLabel($hargaOpts, $request->harga_jual) ?? $request->harga_jual,
            'pembeli_utama' => $pembeliLabels,
            'cara_jual' => $caraJualLabels,
            'produk_paling_laku' => $request->produk_paling_laku,
        ];

        // SWOT text sesuai modul
        $strengthText = "Kekuatan utama usaha anda ada pada {$q7_1} dan {$q7_2}, terlihat dari hal yang paling sering dipuji oleh pembeli.";
        $weaknessText = "Kelemahan utama usaha terletak pada {$q8_1} dan {$q8_2}, sehingga proses usaha belum berjalan optimal.";
        $opportunityText = "Peluang usaha terlihat dari pola penjualan yang ramai pada {$q9_1} dan {$q9_2}, serta kebutuhan pembeli yang membeli untuk "
            . implode(', ', $oppNeedLabels) . ", yang menunjukkan potensi pembelian berulang.";
        $threatText = "Ancaman utama yang paling sering mengganggu usaha berasal dari {$q11_1} dan {$q11_2} yang berpotensi menurunkan stabilitas produksi/penjualan dan daya saing.";

        $swot = [
            'strength' => [
                'items' => $strengthLabels,
                'text' => $strengthText,
            ],
            'weakness' => [
                'items' => $weakLabels,
                'text' => $weaknessText,
                'kelemahan_1' => $q8_1,
                'detail_kendala_1' => $detail1,
                'kelemahan_2' => $q8_2,
                'detail_kendala_2' => $detail2,
            ],
            'opportunity' => [
                'waktu_ramai' => $oppTimeLabels,
                'keperluan_pembeli' => $oppNeedLabels,
                'text' => $opportunityText,
            ],
            'threat' => [
                'items' => $threatLabels,
                'text' => $threatText,
            ],
        ];

        // ✅ Kesimpulan rapih (pakai detail kendala kalau ada)
        $kendalaText = [];

        if ($q8_1 !== '-') {
            $d = $detail1 ? " ({$detail1})" : '';
            $kendalaText[] = $q8_1 . $d;
        }
        if ($q8_2 !== '-') {
            $d = $detail2 ? " ({$detail2})" : '';
            $kendalaText[] = $q8_2 . $d;
        }

        $kesimpulan = "Berdasarkan hasil input, usaha {$request->nama_produk} memiliki kekuatan pada {$q7_1} dan {$q7_2}, "
            . "namun masih terkendala pada " . (count($kendalaText) ? implode(' dan ', $kendalaText) : '-') . ". "
            . "Peluang pasar terlihat dari penjualan ramai pada {$q9_1} serta {$q9_2}, "
            . "dan kebutuhan pembeli untuk " . (count($oppNeedLabels) ? implode(', ', $oppNeedLabels) : '-') . ". "
            . "Ancaman utama berasal dari {$q11_1} dan {$q11_2}.";

        // SARAN 3 BULAN KE DEPAN
        $prioritas1 = ($q8_1 !== '-') ? $q8_1 : null;
        $prioritas2 = ($q11_1 !== '-') ? $q11_1 : null;
        $prioritas3 = ($q9_1 !== '-') ? $q9_1 : null;

        $rekomendasi = [];
        if ($prioritas1) $rekomendasi[] = "Bulan 1: fokus perbaiki kendala utama pada {$prioritas1} supaya proses usaha lebih optimal.";
        if ($prioritas2) $rekomendasi[] = "Bulan 2: siapkan langkah antisipasi untuk mengurangi dampak {$prioritas2} agar stabilitas produksi/penjualan tetap terjaga.";
        if ($prioritas3) $rekomendasi[] = "Bulan 3: maksimalkan peluang pada waktu ramai {$prioritas3} dengan promo, stok siap, dan konsistensi kualitas.";

        $saran3Bulan = [
            'prioritas_1' => $prioritas1,
            'prioritas_2' => $prioritas2,
            'prioritas_3' => $prioritas3,
            'rekomendasi' => $rekomendasi,
        ];

        return [
            'ringkasan' => $ringkasan,
            'swot' => $swot,
            'kesimpulan' => $kesimpulan,
            'saran_3_bulan' => $saran3Bulan,
        ];
    }
}