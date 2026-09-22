<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAiAnalysis extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'should_alert' => 'boolean',
            'raw_json' => 'array',
            'analyzed_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(StockEvent::class, 'stock_event_id');
    }
}
