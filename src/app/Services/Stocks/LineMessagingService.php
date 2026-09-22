<?php

namespace App\Services\Stocks;

use App\Models\StockAlert;
use App\Models\StockSetting;
use App\Models\StockEvent;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class LineMessagingService
{
    public function sendImmediate(StockEvent $event): bool
    {
        $event->loadMissing(['watchlist', 'analysis']);
        $analysis = $event->analysis;
        if ($analysis === null) {
            return false;
        }

        $setting = StockSetting::singleton();
        if (!$setting->line_enabled || !$setting->immediate_alert_enabled) {
            return false;
        }

        $message = $this->buildImmediateMessage($event);

        return $this->send(
            $message,
            'immediate',
            'event:'.$event->id,
            $event->watchlist->id,
            $event->id,
        );
    }

    public function sendMorningSummary(string $message, string $date): bool
    {
        $setting = StockSetting::singleton();
        if (!$setting->line_enabled || !$setting->morning_summary_enabled) {
            return false;
        }

        return $this->send($message, 'morning', 'morning:'.$date, null, null);
    }

    private function send(string $message, string $kind, string $dedupeKey, ?int $watchlistId, ?int $eventId): bool
    {
        if (StockAlert::query()->where('dedupe_key', $dedupeKey)->where('status', 'sent')->exists()) {
            return true;
        }

        $setting = StockSetting::singleton();
        if (!$setting->line_user_id) {
            throw new RuntimeException('LINE user ID has not been captured yet.');
        }

        $token = (string) config('stock_alert.line.channel_access_token');
        if ($token === '') {
            throw new RuntimeException('STOCK_LINE_CHANNEL_ACCESS_TOKEN is not configured.');
        }

        $message = mb_substr($message, 0, config('stock_alert.alert.line_text_limit', 4500));

        $alert = StockAlert::query()->updateOrCreate(
            ['dedupe_key' => $dedupeKey],
            [
                'stock_watchlist_id' => $watchlistId,
                'stock_event_id' => $eventId,
                'kind' => $kind,
                'channel' => 'line',
                'message' => $message,
                'status' => 'pending',
                'error' => null,
                'sent_at' => null,
            ],
        );

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->asJson()
                ->timeout(15)
                ->retry(2, 500)
                ->post(config('stock_alert.line.endpoint'), [
                    'to' => $setting->line_user_id,
                    'messages' => [
                        ['type' => 'text', 'text' => $message],
                    ],
                ]);

            if (!$response->successful()) {
                throw new RuntimeException('LINE Messaging API failed: HTTP '.$response->status().' '.$response->body());
            }

            $alert->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error' => null,
            ]);

            return true;
        } catch (\Throwable $e) {
            $alert->update([
                'status' => 'failed',
                'error' => mb_substr($e->getMessage(), 0, 5000),
            ]);

            throw $e;
        }
    }

    private function buildImmediateMessage(StockEvent $event): string
    {
        $a = $event->analysis;
        $impactLabel = match ($a->impact) {
            'positive' => 'プラス材料の可能性',
            'negative' => 'マイナス材料の可能性',
            'neutral' => '中立',
            default => '判断困難',
        };

        $lines = [
            '【株式監視アラート】',
            $event->watchlist->stock_code.' '.$event->watchlist->company_name,
            '',
            '重要度: '.strtoupper($a->importance),
            '材料方向: '.$impactLabel,
            'AI信頼度: '.$a->confidence.'%',
            '',
            '■ 内容',
            $a->summary,
            '',
            '■ 根拠',
            $a->reason,
            '',
            '■ 注意点',
            $a->risk ?: '特記事項なし',
            '',
            '■ 元ニュース',
            $event->title,
        ];

        if ($event->source_url) {
            $lines[] = $event->source_url;
        }

        $lines[] = '';
        $lines[] = '※AIによる情報整理であり、売買判断を保証するものではありません。';

        return implode("\n", $lines);
    }
}
