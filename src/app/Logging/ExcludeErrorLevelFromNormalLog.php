<?php

namespace App\Logging;

use Monolog\Handler\FilterHandler;
use Monolog\Level;
use Monolog\Logger;

class ExcludeErrorLevelFromNormalLog
{
    /**
     * 通常ログには debug / info / notice / warning までを出力する。
     * error / critical / alert / emergency は error_daily 側に出す。
     */
    public function __invoke(Logger $logger): void
    {
        $filteredHandlers = [];

        foreach ($logger->getHandlers() as $handler) {
            $filteredHandlers[] = new FilterHandler(
                handler: $handler,
                minLevelOrList: Level::Debug,
                maxLevel: Level::Warning,
                bubble: true,
            );
        }

        $logger->setHandlers($filteredHandlers);
    }
}
