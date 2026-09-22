<?php

namespace App\Http\Controllers;

use App\Models\StockSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockLineWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $secret = (string) config('stock_alert.line.channel_secret');
        if ($secret === '') {
            return response()->json(['message' => 'LINE channel secret is not configured.'], 503);
        }

        $signature = (string) $request->header('X-Line-Signature', '');
        $expected = base64_encode(hash_hmac('sha256', $request->getContent(), $secret, true));

        if ($signature === '' || !hash_equals($expected, $signature)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $payload = $request->json()->all();
        foreach (($payload['events'] ?? []) as $event) {
            $userId = $event['source']['userId'] ?? null;
            if (is_string($userId) && $userId !== '') {
                StockSetting::singleton()->update(['line_user_id' => $userId]);
                break;
            }
        }

        return response()->json(['ok' => true]);
    }
}
