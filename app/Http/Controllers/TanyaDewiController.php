<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\ChatSession;
use App\Models\ChatMessage;

class TanyaDewiController extends Controller
{
    /**
     * POST /api/tanya-dewi/sessions
     * Buat session baru, balikin session_id ke frontend
     */
    public function startSession(Request $request)
    {
        $userId = $request->user()->id; // kalau pakai auth

        $sessionId = (string) Str::uuid();

        ChatSession::create([
            'id' => $sessionId,
            'user_id' => $userId,
        ]);

        return response()->json([
            'session_id' => $sessionId,
        ]);
    }

    /**
     * POST /api/tanya-dewi/sessions/{sessionId}/messages
     * Simpan pesan user -> call agent python -> simpan jawaban -> balikin ke frontend
     */
        public function sendMessage(Request $request, string $sessionId)
        {
            $request->validate([
                'message' => 'required|string|max:2000',
            ]);

            $userId  = $request->user()->id;
            $message = $request->input('message');

            // pastikan session valid & milik user (kalau tidak -> 404)
            ChatSession::where('id', $sessionId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $limit = (int) env('CHAT_HISTORY_LIMIT', 12);

            return DB::transaction(function () use ($sessionId, $userId, $message, $limit) {

                // 1) simpan pesan user
                ChatMessage::create([
                    'session_id' => $sessionId,
                    'role'       => 'user',
                    'content'    => $message,
                    'meta'       => [], // aman kalau json nullable / cast
                ]);

                // 2) ambil history terakhir buat konteks agent
                $history = ChatMessage::where('session_id', $sessionId)
                    ->orderBy('id', 'desc')
                    ->limit($limit)
                    ->get(['role', 'content'])
                    ->reverse()
                    ->values()
                    ->map(fn ($m) => [
                        'role' => $m->role,
                        'content' => $m->content,
                    ])
                    ->all();

                // 3) call python agent
                $agentUrl   = env('AGENT_URL');
                $agentToken = env('AGENT_TOKEN');
                $timeout    = (int) env('AGENT_TIMEOUT', 30);

                try {
                    $resp = Http::timeout($timeout)
                        ->withHeaders([
                            'X-App-Token' => $agentToken,
                            'Accept'      => 'application/json',
                        ])
                        ->post($agentUrl, [
                            'session_id' => $sessionId,
                            'user_id'    => $userId,
                            'message'    => $message,
                            'history'    => $history,
                        ]);
                } catch (\Throwable $e) {
                    $fallback = "Maaf ya, sistem sedang sibuk. Coba lagi sebentar.";

                    ChatMessage::create([
                        'session_id' => $sessionId,
                        'role'       => 'assistant',
                        'content'    => $fallback,
                        'meta'       => [
                            'error'     => 'agent_unreachable',
                            'exception' => $e->getMessage(),
                        ],
                    ]);

                    return response()->json([
                        'session_id' => $sessionId,
                        'answer'     => $fallback,
                        'error'      => true,
                    ], 200);
                }

                if (!$resp->successful()) {
                    $fallback = "Maaf ya, sistem sedang bermasalah. Coba lagi sebentar.";

                    ChatMessage::create([
                        'session_id' => $sessionId,
                        'role'       => 'assistant',
                        'content'    => $fallback,
                        'meta'       => [
                            'error'  => 'agent_error',
                            'status' => $resp->status(),
                            'body'   => $resp->body(),
                        ],
                    ]);

                    return response()->json([
                        'session_id' => $sessionId,
                        'answer'     => $fallback,
                        'error'      => true,
                    ], 200);
                }

                $data = $resp->json();
                $answer    = (string) ($data['answer'] ?? '');
                $citations = $data['citations'] ?? [];
                $meta      = $data['meta'] ?? [];

                // 4) simpan jawaban assistant
                ChatMessage::create([
                    'session_id' => $sessionId,
                    'role'       => 'assistant',
                    'content'    => $answer,
                    'meta'       => [
                        'citations' => $citations,
                        'meta'      => $meta,
                    ],
                ]);

                return response()->json([
                    'session_id' => $sessionId,
                    'answer'     => $answer,
                    'citations'  => $citations,
                ], 200);
            });
        }

       
    /**
     * GET /api/tanya-dewi/sessions/{sessionId}/messages
     * Ambil history chat untuk ditampilkan di frontend
     */
    public function getMessages(Request $request, string $sessionId)
    {
        $userId = $request->user()->id;

        ChatSession::where('id', $sessionId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $messages = ChatMessage::where('session_id', $sessionId)
            ->orderBy('id', 'asc')
            ->get(['role', 'content', 'meta', 'created_at'])
            ->map(fn ($m) => [
                'role' => $m->role,
                'content' => $m->content,
                'meta' => $m->meta ?? [],
                'created_at' => $m->created_at?->toISOString(),
            ]);

        return response()->json([
            'session_id' => $sessionId,
            'messages' => $messages,
        ]);
    }
    
}

