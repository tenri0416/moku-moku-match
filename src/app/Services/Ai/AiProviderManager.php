<?php

namespace App\Services\Ai;

use App\Models\Admin;
use App\Models\AiProviderAttemptLog;
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

    public function requestJson(string $prompt, float $temperature = 0.2, ?string $actionName = null): array
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

                $this->storeAttemptLog([
                    'provider' => $provider->providerName(),
                    'model' => $provider->modelName(),
                    'status' => 'skipped',
                    'error_message' => 'APIキーが未設定、またはconfigが読み込めていません。',
                    'attempt' => null,
                    'is_fallback' => false,
                    'action_name' => $actionName,
                ]);

                continue;
            }

            $availableProviderCount++;
            $attempts++;

            $result = $provider->requestJson(
                prompt: $prompt,
                temperature: $temperature,
                maxOutputTokens: (int) config('services.ai.max_output_tokens', 5000),
            );

            if ($result->success) {
                Log::info('AIリクエストに成功しました。', [
                    'provider' => $result->provider,
                    'model' => $result->model,
                    'attempt' => $attempts,
                    'is_fallback' => $attempts > 1,
                    'action_name' => $actionName,
                ]);

                $this->storeAttemptLog([
                    'provider' => $result->provider,
                    'model' => $result->model,
                    'status' => 'success',
                    'status_code' => $result->statusCode,
                    'attempt' => $attempts,
                    'is_fallback' => $attempts > 1,
                    'action_name' => $actionName,
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

            $retryAvailableAt = $result->retryAfterSeconds !== null
                ? now()->addSeconds($result->retryAfterSeconds)
                : null;

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
                'action_name' => $actionName,
            ]);

            $this->storeAttemptLog([
                'provider' => $result->provider,
                'model' => $result->model,
                'status' => 'failed',
                'status_code' => $result->statusCode,
                'error_message' => $result->errorMessage,
                'retry_after_seconds' => $result->retryAfterSeconds,
                'retry_available_at' => $retryAvailableAt,
                'attempt' => $attempts,
                'is_fallback' => $attempts > 1,
                'action_name' => $actionName,
            ]);
        }

        if ($availableProviderCount === 0) {
            $lastErrorMessage = '利用可能なAIプロバイダーがありません。Google/OpenRouter/GroqのAPIキー設定を確認してください。';

            Log::warning('利用可能なAIプロバイダーがありません。', [
                'message' => $lastErrorMessage,
                'action_name' => $actionName,
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

    private function storeAttemptLog(array $data): void
    {
        try {
            AiProviderAttemptLog::create([
                'provider' => $data['provider'],
                'model' => $data['model'] ?? null,
                'status' => $data['status'],
                'status_code' => $data['status_code'] ?? null,
                'error_message' => $data['error_message'] ?? null,
                'retry_after_seconds' => $data['retry_after_seconds'] ?? null,
                'retry_available_at' => $data['retry_available_at'] ?? null,
                'attempt' => $data['attempt'] ?? null,
                'is_fallback' => $data['is_fallback'] ?? false,
                'action_name' => $data['action_name'] ?? null,
                'attempted_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('AI試行ログの保存に失敗しました。', [
                'message' => $e->getMessage(),
                'data' => $data,
            ]);
        }
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
