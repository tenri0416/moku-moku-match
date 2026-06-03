<?php

namespace App\Services\Ai;

use App\Models\Admin;
use App\Notifications\AiRetryFailedNotification;
use App\Services\Ai\Providers\GoogleAiService;
use App\Services\Ai\Providers\GroqAiService;
use App\Services\Ai\Providers\OpenRouterAiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AiProviderManager
{
    public function __construct(
        private readonly GoogleAiService $googleAiService,
        private readonly OpenRouterAiService $openRouterAiService,
        private readonly GroqAiService $groqAiService,
    ) {
    }

    public function requestJson(string $prompt, float $temperature = 0.2): array
    {
        $attempts = 0;
        $lastErrorMessage = null;
        $availableProviderCount = 0;
        $failedProviders = [];

        foreach ($this->providers() as $provider) {
            if (! $provider->isAvailable()) {
                Log::warning('AIプロバイダーが未設定のためスキップされました。', [
                    'provider' => $provider->providerName(),
                    'model' => $provider->modelName(),
                    'reason' => 'APIキーが未設定、またはconfigが読み込めていません。',
                ]);

                continue;
            }

            $availableProviderCount++;
            $attempts++;

            $result = $provider->requestJson(
                prompt: $prompt,
                temperature: $temperature,
                maxOutputTokens: (int) config('services.ai.max_output_tokens', 350),
            );

            if ($result->success) {
                Log::info('AIリクエストに成功しました。', [
                    'provider' => $result->provider,
                    'model' => $result->model,
                    'attempt' => $attempts,
                    'is_fallback' => $attempts > 1,
                ]);

                return [
                    'success' => true,
                    'provider' => $result->provider,
                    'model' => $result->model,
                    'text' => $result->text,
                    'error_message' => $lastErrorMessage,
                    'is_fallback' => $attempts > 1,
                    'attempts' => $attempts,
                ];
            }

            $lastErrorMessage = $result->errorMessage;

            $failedProviders[] = [
                'provider' => $result->provider,
                'model' => $result->model,
                'status_code' => $result->statusCode,
                'message' => $result->errorMessage,
                'retry_after_seconds' => $result->retryAfterSeconds,
                'retry_after_minutes' => $this->secondsToMinutes($result->retryAfterSeconds),
            ];

            Log::warning('AIリクエストに失敗しました。次のAIを試します。', [
                'provider' => $result->provider,
                'model' => $result->model,
                'status_code' => $result->statusCode,
                'message' => $result->errorMessage,
                'retry_after_seconds' => $result->retryAfterSeconds,
                'retry_after_minutes' => $this->secondsToMinutes($result->retryAfterSeconds),
                'attempt' => $attempts,
            ]);
        }

        if ($availableProviderCount === 0) {
            $lastErrorMessage = '利用可能なAIプロバイダーがありません。Google/OpenRouter/GroqのAPIキー設定を確認してください。';

            Log::warning('利用可能なAIプロバイダーがありません。', [
                'message' => $lastErrorMessage,
            ]);
        }

        $this->notifyAdminsWhenAllRetriesFailed($failedProviders, $lastErrorMessage);

        return [
            'success' => false,
            'provider' => null,
            'model' => null,
            'text' => null,
            'error_message' => $lastErrorMessage,
            'is_fallback' => true,
            'attempts' => $attempts,
        ];
    }

    private function providers(): array
    {
        return [
            $this->googleAiService,
            $this->openRouterAiService,
            $this->groqAiService,
        ];
    }

    private function secondsToMinutes(?int $seconds): ?int
    {
        if ($seconds === null) {
            return null;
        }

        return max(1, (int) ceil($seconds / 60));
    }

    private function notifyAdminsWhenAllRetriesFailed(array $failedProviders, ?string $lastErrorMessage): void
    {
        if (empty($failedProviders)) {
            return;
        }

        /**
         * 通知が大量に飛ばないように、10分に1回までに制限
         */
        $cacheKey = 'ai-retry-failed-notified';

        if (Cache::has($cacheKey)) {
            return;
        }

        Cache::put($cacheKey, true, now()->addMinutes(10));

        $admins = Admin::query()
            ->where('status', 1)
            ->get();

        if ($admins->isEmpty()) {
            Log::warning('AIリトライ失敗通知を送信できる管理者が存在しません。');
            return;
        }

        foreach ($admins as $admin) {
            $admin->notify(new AiRetryFailedNotification(
                failedProviders: $failedProviders,
                lastErrorMessage: $lastErrorMessage,
            ));
        }

        Log::warning('AIリトライ全失敗の管理者通知を作成しました。', [
            'admin_count' => $admins->count(),
            'failed_providers' => $failedProviders,
        ]);
    }
}
