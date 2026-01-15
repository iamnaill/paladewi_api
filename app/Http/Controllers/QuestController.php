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

    private function toJson($value): ?string
    {
        if ($value === null) return null;

        // kalau sudah string JSON, biarkan
        if (is_string($value)) {
            json_decode($value);
            if (json_last_error() === JSON_ERROR_NONE) return $value;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE);
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
            'pembeli_utama' => 'required|in:tetangga,sekolah,kantoran,wisatawan,online_luar_daerah',
            'cara_jual' => 'required|in:titip_warung,wa_grup_story,pasar_offline,marketplace,ig_fb',
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

        // === penting: kolom-kolom ini di DB wajib JSON valid ===
        $data['pembeli_utama']   = $this->toJson($data['pembeli_utama']);      // string -> "wisatawan"
        $data['cara_jual']       = $this->toJson($data['cara_jual']);          // string -> "marketplace"
        $data['q7_pujian']       = $this->toJson($data['q7_pujian']);          // array -> [...]
        $data['q8_kesulitan']    = $this->toJson($data['q8_kesulitan']);       // array -> [...]
        $data['q8_detail']       = $this->toJson($data['q8_detail']);          // object -> {...}
        $data['q9_waktu_ramai']  = $this->toJson($data['q9_waktu_ramai']);     // array -> [...]
        $data['q10_tujuan_beli'] = $this->toJson($data['q10_tujuan_beli']);    // array -> [...]
        $data['q11_ancaman']     = $this->toJson($data['q11_ancaman']);        // array -> [...]
        // ================================================

        $item = QuestcardAAnswer::create($data);

        return response()->json([
            'message' => 'QuestCard A tersimpan',
            'data' => $item,
        ], 201);
    }

    public function storeB(Request $request)
    {
        $request->validate([
            'q1_bahan_pala' => 'required|in:banyak_mudah,ada_tertentu,jarang,tidak_ada',
            'q2_pernah_olah' => 'required|in:pernah,belum',
            'q2b_olah_apa' => 'nullable|array',
            'q2b_olah_apa.*' => 'string',

            'q3_diminta_orang' => 'required|in:sering,kadang,sekali,tidak_pernah',
            'q4_bayar' => 'required|in:bayar_sesuai,bayar_tidak_tentu,bayar_ke_ortu,tidak_bayar',
            'q5_sanggup_bikin_sering' => 'required|in:sanggup,terbatas,berat,tidak_sanggup',
            'q6_mau_jual' => 'required|in:ingin_sekali,ingin_kalau_bantuan,ragu,tidak_ingin',
        ]);

        $mapQ1 = ['banyak_mudah' => 2, 'ada_tertentu' => 1, 'jarang' => 0, 'tidak_ada' => 0];
        $mapQ2 = ['pernah' => 2, 'belum' => 0];
        $mapQ3 = ['sering' => 2, 'kadang' => 1, 'sekali' => 0, 'tidak_pernah' => 0];
        $mapQ4 = ['bayar_sesuai' => 2, 'bayar_tidak_tentu' => 1, 'bayar_ke_ortu' => 0, 'tidak_bayar' => 0];
        $mapQ5 = ['sanggup' => 2, 'terbatas' => 1, 'berat' => 0, 'tidak_sanggup' => 0];
        $mapQ6 = ['ingin_sekali' => 2, 'ingin_kalau_bantuan' => 1, 'ragu' => 0, 'tidak_ingin' => 0];

        $score = 0;
        $score += $mapQ1[$request->q1_bahan_pala];
        $score += $mapQ2[$request->q2_pernah_olah];
        $score += $mapQ3[$request->q3_diminta_orang];
        $score += $mapQ4[$request->q4_bayar];
        $score += $mapQ5[$request->q5_sanggup_bikin_sering];
        $score += $mapQ6[$request->q6_mau_jual];

        $status = $this->statusB($score);

        $data = $request->only([
            'q1_bahan_pala',
            'q2_pernah_olah',
            'q2b_olah_apa',
            'q3_diminta_orang',
            'q4_bayar',
            'q5_sanggup_bikin_sering',
            'q6_mau_jual',
        ]);

        $data['user_id'] = $request->user()->id;
        $data['score'] = $score;
        $data['status'] = $status;

        // kalau di tabel B kamu pakai CHECK json_valid untuk q2b_olah_apa:
        $data['q2b_olah_apa'] = $this->toJson($data['q2b_olah_apa']);

        $item = QuestcardBAnswer::create($data);

        return response()->json([
            'message' => 'QuestCard B tersimpan',
            'data' => $item,
        ], 201);
    }

    public function listA(Request $request)
    {
        $data = QuestcardAAnswer::where('user_id', $request->user()->id)->latest()->get();
        return response()->json($data, 200);
    }

    public function listB(Request $request)
    {
        $data = QuestcardBAnswer::where('user_id', $request->user()->id)->latest()->get();
        return response()->json($data, 200);
    }

    public function submitA(Request $request)
    {
        $latestA = QuestcardAAnswer::where('user_id', $request->user()->id)->latest()->first();

        if (!$latestA) {
            return response()->json([
                'message' => 'Belum ada data QuestCard A untuk disubmit',
            ], 422);
        }

        return response()->json([
            'message' => 'QuestCard A berhasil disubmit',
            'data' => $latestA,
        ], 200);
    }

    public function submitB(Request $request)
    {
        $latestB = QuestcardBAnswer::where('user_id', $request->user()->id)->latest()->first();

        if (!$latestB) {
            return response()->json([
                'message' => 'Belum ada data QuestCard B untuk disubmit',
            ], 422);
        }

        return response()->json([
            'message' => 'QuestCard B berhasil disubmit',
            'score' => $latestB->score,
            'status' => $latestB->status,
            'data' => $latestB,
        ], 200);
    }
}
