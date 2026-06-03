<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\Contracts\AiProviderInterface;
use App\Services\Ai\Data\AiProviderResult;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\Http;
use Throwable;

class GoogleAiService implements AiProviderInterface
{
    public function providerName(): string
    {
        return 'google';
    }

    public function modelName(): string
    {
        return (string) config('services.google_ai.model', 'gemini-2.5-flash');
    }

    public function isAvailable(): bool
    {
        return filled(config('services.google_ai.api_key'));
    }

    public function requestJson(string $prompt, float $temperature, int $maxOutputTokens): AiProviderResult
    {
        $apiKey = (string) config('services.google_ai.api_key');
        $model = $this->modelName();

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-goog-api-key' => $apiKey,
                ])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt,
                                ],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => $temperature,
                        'response_mime_type' => 'application/json',
                        'maxOutputTokens' => $maxOutputTokens,
                    ],
                ]);
        } catch (Throwable $e) {
            return AiProviderResult::failed(
                provider: $this->providerName(),
                model: $model,
                errorMessage: $e->getMessage(),
            );
        }

        if (! $response->successful()) {
            return AiProviderResult::failed(
                provider: $this->providerName(),
                model: $model,
                errorMessage: $this->errorMessage($response),
                statusCode: $response->status(),
                retryAfterSeconds: $this->extractRetryAfterSeconds($response),
            );
        }

        $text = $response->json('candidates.0.content.parts.0.text');

        if (! filled($text)) {
            return AiProviderResult::failed(
                provider: $this->providerName(),
                model: $model,
                errorMessage: 'Google AIのレスポンス本文を取得できませんでした。',
                statusCode: $response->status(),
            );
        }

        return AiProviderResult::success(
            provider: $this->providerName(),
            model: $model,
            text: (string) $text,
        );
    }

    private function errorMessage(HttpResponse $response): string
    {
        return (string) (
            $response->json('error.message')
            ?? $response->body()
            ?? 'Google AIリクエストに失敗しました。'
        );
    }

    /**
     * Googleのエラーメッセージに含まれる
     * "Please retry in 42.777s" から秒数を取り出す
     */
    private function extractRetryAfterSeconds(HttpResponse $response): ?int
    {
        $message = $this->errorMessage($response);

        if (preg_match('/Please retry in ([0-9.]+)s/i', $message, $matches)) {
            return (int) ceil((float) $matches[1]);
        }

        return null;
    }
}
