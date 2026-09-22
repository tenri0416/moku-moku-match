<?php

namespace App\Services\Stocks;

use App\Models\StockAiAnalysis;
use App\Models\StockEvent;
use App\Models\StockWatchlist;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class StockMonitorService
{
    public function __construct(
        private readonly GoogleNewsService $news,
        private readonly GroqStockAnalyzer $analyzer,
        private readonly LineMessagingService $line,
    ) {
    }

    public function scan(bool $allowImmediateAlerts = true): array
    {
        $summary = [
            'watchlists' => 0,
            'new_events' => 0,
            'analyzed' => 0,
            'alerts_sent' => 0,
            'errors' => 0,
        ];

        StockWatchlist::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->each(function (StockWatchlist $watchlist) use (&$summary, $allowImmediateAlerts): void {
                $summary['watchlists']++;

                try {
                    $result = $this->scanOne($watchlist, $allowImmediateAlerts);
                    foreach (['new_events', 'analyzed', 'alerts_sent'] as $key) {
                        $summary[$key] += $result[$key];
                    }
                } catch (\Throwable $e) {
                    $summary['errors']++;
                    $watchlist->update([
                        'last_checked_at' => now(),
                        'last_error' => mb_substr($e->getMessage(), 0, 5000),
                    ]);

                    Log::error('Stock monitor failed.', [
                        'stock_code' => $watchlist->stock_code,
                        'exception' => $e,
                    ]);
                }
            });

        return $summary;
    }

    private function scanOne(StockWatchlist $watchlist, bool $allowImmediateAlerts): array
    {
        $isInitial = $watchlist->initialized_at === null;
        $items = $this->news->search($watchlist->stock_code, $watchlist->company_name);

        $lookback = now()->subDays(config('stock_alert.news.lookback_days', 7));
        $items = array_values(array_filter($items, function (array $item) use ($lookback): bool {
            return $item['published_at'] === null || $item['published_at']->greaterThanOrEqualTo($lookback);
        }));

        if ($isInitial) {
            $items = array_slice($items, 0, config('stock_alert.news.initial_items', 5));
        }

        $result = ['new_events' => 0, 'analyzed' => 0, 'alerts_sent' => 0];

        foreach ($items as $item) {
            $hashSource = $item['source_id'] ?: implode('|', [
                preg_replace('/\s+/u', ' ', mb_strtolower(trim($item['title']))),
                $item['source'],
                $item['published_at']?->format('Y-m-d H:i:s') ?? '',
            ]);
            $contentHash = hash('sha256', $hashSource);

            if (StockEvent::query()
                ->where('stock_watchlist_id', $watchlist->id)
                ->where('content_hash', $contentHash)
                ->exists()) {
                continue;
            }

            try {
                $event = StockEvent::query()->create([
                    'stock_watchlist_id' => $watchlist->id,
                    'source' => $item['source'],
                    'source_id' => $item['source_id'],
                    'event_type' => 'news',
                    'title' => $item['title'],
                    'summary_text' => $item['summary_text'],
                    'source_url' => $item['source_url'],
                    'published_at' => $item['published_at'],
                    'content_hash' => $contentHash,
                    'raw_payload' => $item['raw_payload'],
                ]);
            } catch (QueryException $e) {
                if ($this->isDuplicateKey($e)) {
                    continue;
                }
                throw $e;
            }

            $result['new_events']++;
            $analysis = $this->analyzeSafely($event);
            $result['analyzed']++;

            if (!$isInitial && $allowImmediateAlerts && $analysis->should_alert && $watchlist->notify_news) {
                try {
                    if ($this->line->sendImmediate($event->fresh(['watchlist', 'analysis']))) {
                        $result['alerts_sent']++;
                    }
                } catch (\Throwable $e) {
                    Log::warning('LINE immediate alert failed.', [
                        'event_id' => $event->id,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }

        $watchlist->update([
            'initialized_at' => $watchlist->initialized_at ?? now(),
            'last_checked_at' => now(),
            'last_error' => null,
        ]);

        return $result;
    }

    private function analyzeSafely(StockEvent $event): StockAiAnalysis
    {
        try {
            $data = $this->analyzer->analyze($event);
        } catch (\Throwable $e) {
            Log::warning('Stock AI analysis failed; fallback used.', [
                'event_id' => $event->id,
                'message' => $e->getMessage(),
            ]);

            $data = [
                'provider' => 'fallback',
                'model' => null,
                'impact' => 'uncertain',
                'impact_score' => 0,
                'confidence' => 0,
                'importance' => 'low',
                'classified_event_type' => 'other',
                'should_alert' => false,
                'summary' => $event->title,
                'reason' => 'AI分析に失敗したため、自動判定を行っていません。',
                'risk' => mb_substr($e->getMessage(), 0, 1000),
                'raw_json' => ['error' => $e->getMessage()],
            ];
        }

        return StockAiAnalysis::query()->updateOrCreate(
            ['stock_event_id' => $event->id],
            array_merge($data, ['analyzed_at' => now()]),
        );
    }

    private function isDuplicateKey(QueryException $e): bool
    {
        $sqlState = $e->errorInfo[0] ?? null;
        $driverCode = (int) ($e->errorInfo[1] ?? 0);

        return $sqlState === '23000' && $driverCode === 1062;
    }
}
