<?php

namespace App\Services\Ai\Support;

use Illuminate\Support\Facades\Log;
use RuntimeException;

class AiJsonParser
{
    public function parse(string $text): array
    {
        $text = trim($text);

        $text = preg_replace('/^```json\s*/', '', $text);
        $text = preg_replace('/^```\s*/', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);

        $decoded = json_decode($text, true);

        if (! is_array($decoded)) {
            Log::error('AI JSON変換失敗', [
                'text' => $text,
            ]);

            throw new RuntimeException('AIのレスポンスをJSONとして解析できませんでした。');
        }

        return $decoded;
    }
}
