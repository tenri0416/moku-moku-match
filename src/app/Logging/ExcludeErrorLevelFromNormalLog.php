<?php

namespace App\Logging;

use Illuminate\Log\Logger as IlluminateLogger;
use Monolog\Handler\FilterHandler;
use Monolog\Level;

class ExcludeErrorLevelFromNormalLog
{
    /**
     * 通常ログには debug / info / notice / warning までを出力する。
     * error / critical / alert / emergency は error_daily 側に出す。
     */
    public function __invoke(IlluminateLogger $logger): void
    {
        $monolog = $logger->getLogger();

        $filteredHandlers = [];

        foreach ($monolog->getHandlers() as $handler) {
            $filteredHandlers[] = new FilterHandler(
                handler: $handler,
                minLevelOrList: Level::Debug,
                maxLevel: Level::Warning,
                bubble: true,
            );
        }

        $monolog->setHandlers($filteredHandlers);
    }
}
