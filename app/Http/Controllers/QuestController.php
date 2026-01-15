<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestcardAAnswer;
use App\Models\QuestcardBAnswer;

class QuestController extends Controller
{
    private function statusB(int $score): string
    {
        if ($score >= 9) return 'layak';
        if ($score >= 6) return 'berpotensi';
        return 'belum_layak';
    }

    /**
     * Normalisasi value menjadi JSON string (atau null).
     * - Kalau sudah JSON valid string, biarkan.
     * - Kalau array/object, encode.
     */
    private function toJson($value): ?string
    {
        if ($value === null) return null;

        if (is_string($value)) {
            json_decode($value);
            if (json_last_error() === JSON_ERROR_NONE) return $value;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

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
                        ['value' => 'skincare',  'label' => 'Skincare (Perawatan tubuh dan kulit)'],
                        ['value' => 'jasa',      'label' => 'Jasa'],
                        ['value' => 'lainnya',   'label' => 'Lainnya'],
                    ],
                    'show_when' => [
                        'key' => 'jenis_produk_lainnya',
                        'condition' => ['jenis_produk' => 'lainnya'],
                    ],
                ],
                [
                    'key' => 'jenis_produk_lainnya',
                    'label' => 'Jika lainnya, tulis jenis produk',
                    'type' => 'text',
                    'required' => false,
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

                // ✅ Revisi: jadi checkbox (bisa pilih lebih dari 1)
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

                // ✅ Revisi: jadi checkbox (bisa pilih lebih dari 1)
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

    public function opening(Request $request)
    {
        $request->validate([
            'quest_id' => 'required|integer',
        ]);

        return response()->json([
            'message' => 'Opening quest berhasil',
            'quest_id' => $request->quest_id,
            'user_id' => $request->user()->id,
        ], 200);
    }

    public function storeA(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'jenis_produk' => 'required|in:makanan,minuman,kerajinan,skincare,jasa,lainnya',
            'jenis_produk_lainnya' => 'nullable|string|max:255',
            'harga_jual' => 'required|in:<5rb,5-10rb,10-25rb,>25rb',

            // ✅ Revisi: jadi multi-select (checkbox)
            'pembeli_utama' => 'required|array|min:1',
            'pembeli_utama.*' => 'in:tetangga,sekolah,kantoran,wisatawan,online_luar_daerah',

            // ✅ Revisi: jadi multi-select (checkbox)
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

        $data = $request->only([
            'nama_produk',
            'jenis_produk',
            'jenis_produk_lainnya',
            'harga_jual',
            'pembeli_utama',
            'cara_jual',
            'produk_paling_laku',
            'q7_pujian',
            'q8_kesulitan',
            'q8_detail',
            'q9_waktu_ramai',
            'q9_hari_tentu',
            'q10_tujuan_beli',
            'q10_lainnya',
            'q11_ancaman',
        ]);

        $data['user_id'] = $request->user()->id;

        // simpan sebagai JSON string (konsisten dengan struktur awal)
        $data['pembeli_utama']   = $this->toJson($data['pembeli_utama']);   // sekarang array
        $data['cara_jual']       = $this->toJson($data['cara_jual']);       // sekarang array
        $data['q7_pujian']       = $this->toJson($data['q7_pujian']);
        $data['q8_kesulitan']    = $this->toJson($data['q8_kesulitan']);
        $data['q8_detail']       = $this->toJson($data['q8_detail']);
        $data['q9_waktu_ramai']  = $this->toJson($data['q9_waktu_ramai']);
        $data['q10_tujuan_beli'] = $this->toJson($data['q10_tujuan_beli']);
        $data['q11_ancaman']     = $this->toJson($data['q11_ancaman']);

        $data['result'] = $this->toJson($this->buildResultA($request));

        $item = QuestcardAAnswer::create($data);

        return response()->json([
            'message' => 'QuestCard A tersimpan',
            'data' => $item,
        ], 201);
    }

    public function listA(Request $request)
    {
        return response()->json(
            QuestcardAAnswer::where('user_id', $request->user()->id)->latest()->get(),
            200
        );
    }

    public function submitA(Request $request)
    {
        $latestA = QuestcardAAnswer::where('user_id', $request->user()->id)->latest()->first();

        if (!$latestA) {
            return response()->json(['message' => 'Belum ada data QuestCard A untuk disubmit'], 422);
        }

        return response()->json([
            'message' => 'QuestCard A berhasil disubmit',
            'data' => $latestA,
        ], 200);
    }

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

        $ringkasan = [
            'nama_produk' => $request->nama_produk,
            'jenis_produk' => $this->optionLabel($jenisOpts, $request->jenis_produk) ?? $request->jenis_produk,
            'harga_jual' => $this->optionLabel($hargaOpts, $request->harga_jual) ?? $request->harga_jual,

            // ✅ Revisi: jadi list (multi)
            'pembeli_utama' => $pembeliLabels,
            'cara_jual' => $caraJualLabels,

            'produk_paling_laku' => $request->produk_paling_laku,
        ];

        $swot = [
            'strength' => [
                'items' => $strengthLabels,
                'text' => 'Kekuatan utama usaha anda ada pada ' . implode(' dan ', $strengthLabels) . '.',
            ],
            'weakness' => [
                'items' => $weakLabels,
                'detail' => $request->q8_detail ?? null,
                'text' => 'Kelemahan utama usaha terletak pada ' . implode(' dan ', $weakLabels) . '.',
            ],
            'opportunity' => [
                'waktu_ramai' => $oppTimeLabels,
                'keperluan_pembeli' => $oppNeedLabels,
                'hari_tentu' => $request->q9_hari_tentu,
                'text' => 'Peluang usaha terlihat dari pola penjualan yang ramai pada ' . implode(' dan ', $oppTimeLabels) .
                    ' serta kebutuhan pembeli: ' . implode(', ', $oppNeedLabels) . '.',
            ],
            'threat' => [
                'items' => $threatLabels,
                'text' => 'Ancaman utama berasal dari ' . implode(' dan ', $threatLabels) . '.',
            ],
        ];

        $prioritas1 = $weakLabels[0] ?? null;
        $prioritas2 = $threatLabels[0] ?? null;
        $prioritas3 = $oppTimeLabels[0] ?? null;

        $kesimpulan = "Berdasarkan input, usaha {$request->nama_produk} memiliki kekuatan pada " .
            implode(' dan ', $strengthLabels) .
            ", namun masih terkendala pada " . implode(' dan ', $weakLabels) .
            ". Peluang terlihat dari " . implode(' dan ', $oppTimeLabels) .
            " dan kebutuhan pembeli: " . implode(', ', $oppNeedLabels) .
            ". Ancaman utama: " . implode(' dan ', $threatLabels) . ".";

        return [
            'ringkasan' => $ringkasan,
            'swot' => $swot,
            'kesimpulan' => $kesimpulan,
            'saran_3_bulan' => [
                'prioritas_1' => $prioritas1,
                'prioritas_2' => $prioritas2,
                'prioritas_3' => $prioritas3,
            ],
        ];
    }
}
