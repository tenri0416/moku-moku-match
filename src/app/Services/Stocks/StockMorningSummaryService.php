<?php

namespace App\Services\Stocks;

use App\Models\StockWatchlist;

class StockMorningSummaryService
{
    public function __construct(private readonly LineMessagingService $line)
    {
    }

    public function send(): bool
    {
        $from = now()->subDay();
        $watchlists = StockWatchlist::query()
            ->where('is_active', true)
            ->with(['events' => function ($query) use ($from): void {
                $query->whereHas('analysis', fn ($q) => $q->where('analyzed_at', '>=', $from))
                    ->with('analysis')
                    ->orderByDesc('published_at');
            }])
            ->orderBy('stock_code')
            ->get();

        $lines = [
            '【株式監視・朝レポート】',
            now()->format('Y/m/d H:i'),
            '',
        ];

        foreach ($watchlists as $watchlist) {
            $lines[] = '■ '.$watchlist->stock_code.' '.$watchlist->company_name;

            $events = $watchlist->events
                ->sortByDesc(function ($event) {
                    $rank = ['high' => 3, 'medium' => 2, 'low' => 1][$event->analysis?->importance ?? 'low'] ?? 1;
                    return $rank * 1000 + abs((int) ($event->analysis?->impact_score ?? 0));
                })
                ->take(3);

            if ($events->isEmpty()) {
                $lines[] = '直近24時間の新規分析情報なし';
                $lines[] = '';
                continue;
            }

            foreach ($events as $event) {
                $a = $event->analysis;
                $impact = match ($a->impact) {
                    'positive' => '＋材料候補',
                    'negative' => '－材料候補',
                    'neutral' => '中立',
                    default => '判断困難',
                };

                $lines[] = '・['.strtoupper($a->importance).'/'.$impact.'] '.$a->summary;
                $lines[] = '  信頼度 '.$a->confidence.'% / '.$event->source;
            }

            $lines[] = '';
        }

        $lines[] = '※AIによる情報整理であり、将来の株価や売買成果を保証するものではありません。';

        return $this->line->sendMorningSummary(implode("\n", $lines), now()->format('Y-m-d'));
    }
}
