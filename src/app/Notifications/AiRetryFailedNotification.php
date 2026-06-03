<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AiRetryFailedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly array $failedProviders,
        private readonly ?string $lastErrorMessage = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'ai_retry_failed',
            'title' => 'AIリトライがすべて失敗しました',
            'message' => $this->buildMessage(),
            'failed_providers' => $this->failedProviders,
            'last_error_message' => $this->lastErrorMessage,
            'detected_at' => now()->toDateTimeString(),
        ];
    }

    private function buildMessage(): string
    {
        $lines = [
            'AIリトライがすべて失敗したため、Laravel簡易採点または固定問題に切り替えました。',
        ];

        foreach ($this->failedProviders as $provider) {
            $providerName = $provider['provider'] ?? '-';
            $model = $provider['model'] ?? '-';
            $waitMinutes = $provider['retry_after_minutes'] ?? null;

            if ($waitMinutes !== null) {
                $lines[] = "{$providerName}（{$model}）は約{$waitMinutes}分後に再利用できる可能性があります。";
            } else {
                $lines[] = "{$providerName}（{$model}）は再利用可能時間を取得できませんでした。";
            }
        }

        return implode("\n", $lines);
    }
}
