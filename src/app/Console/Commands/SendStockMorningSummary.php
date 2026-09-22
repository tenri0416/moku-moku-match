<?php

namespace App\Console\Commands;

use App\Services\Stocks\StockMorningSummaryService;
use Illuminate\Console\Command;

class SendStockMorningSummary extends Command
{
    protected $signature = 'stocks:morning-summary';

    protected $description = '監視銘柄の直近24時間の朝レポートをLINEへ送信する';

    public function handle(StockMorningSummaryService $service): int
    {
        try {
            $sent = $service->send();
            $this->info($sent ? 'Morning summary sent.' : 'Morning summary skipped by settings.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            report($e);
            return self::FAILURE;
        }
    }
}
