<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineNotificationService
{
    /**
     * LINE Messaging API Push Message エンドポイント。
     */
    private const PUSH_ENDPOINT = 'https://api.line.me/v2/bot/message/push';

    /**
     * 管理者向けにLINE通知を送信する。
     *
     * LINE通知に失敗しても、呼び出し元の処理は止めない。
     */
    public function sendToAdmin(string $message): void
    {
        $accessToken = config('services.line.access_token');
        $to = config('services.line.admin_to');

        if (blank($accessToken) || blank($to)) {
            Log::warning('LINE通知を送信できませんでした。LINE_ACCESS_TOKEN または LINE_ADMIN_TO が未設定です。', [
                'has_access_token' => filled($accessToken),
                'has_to' => filled($to),
            ]);

            return;
        }

        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->post(self::PUSH_ENDPOINT, [
                    'to' => $to,
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => $message,
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::warning('LINE通知の送信に失敗しました。', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('LINE通知送信中に例外が発生しました。', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
