<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\Contracts\AiProviderInterface;
use App\Services\Ai\Data\AiProviderResult;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\Http;
use Throwable;

class GroqAiService implements AiProviderInterface
{
    public function providerName(): string
    {
        return 'groq';
    }

    public function modelName(): string
    {
        return (string) config('services.groq.model', 'llama-3.1-8b-instant');
    }

    public function isAvailable(): bool
    {
        return filled(config('services.groq.api_key'));
    }

    public function requestJson(string $prompt, float $temperature, int $maxOutputTokens): AiProviderResult
    {
        $apiKey = (string) config('services.groq.api_key');
        $model = $this->modelName();

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => '日本語で回答してください。必ずJSONだけを返してください。Markdownは禁止です。',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => $temperature,
                    'max_tokens' => $maxOutputTokens,
                    'response_format' => [
                        'type' => 'json_object',
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

        $text = $response->json('choices.0.message.content');

        if (! filled($text)) {
            return AiProviderResult::failed(
                provider: $this->providerName(),
                model: $model,
                errorMessage: 'Groqのレスポンス本文を取得できませんでした。',
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
            ?? $response->json('message')
            ?? $response->body()
            ?? 'Groqリクエストに失敗しました。'
        );
    }

    private function extractRetryAfterSeconds(HttpResponse $response): ?int
    {
        $retryAfter = $response->header('Retry-After');

        if (is_numeric($retryAfter)) {
            return (int) ceil((float) $retryAfter);
        }

        return null;
    }
}
