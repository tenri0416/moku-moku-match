<?php

namespace App\Console\Commands;

use App\Services\Stocks\StockMonitorService;
use Illuminate\Console\Command;

class ScanStocks extends Command
{
    protected $signature = 'stocks:scan {--no-alerts : LINEの即時通知を送らず、取得とAI分析だけ行う}';

    protected $description = '監視中の株式ニュースを取得し、新規情報をAI分析する';

    public function handle(StockMonitorService $service): int
    {
        $result = $service->scan(!$this->option('no-alerts'));

        $this->table(
            ['watchlists', 'new_events', 'analyzed', 'alerts_sent', 'errors'],
            [[
                $result['watchlists'],
                $result['new_events'],
                $result['analyzed'],
                $result['alerts_sent'],
                $result['errors'],
            ]],
        );

        return $result['errors'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
