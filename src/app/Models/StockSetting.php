<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockSetting extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'line_enabled' => 'boolean',
            'immediate_alert_enabled' => 'boolean',
            'morning_summary_enabled' => 'boolean',
        ];
    }

    public static function singleton(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
