<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('diary:check')
    ->cron('30 20,21 * * *')
    ->timezone('Asia/Tokyo')
    ->withoutOverlapping();
