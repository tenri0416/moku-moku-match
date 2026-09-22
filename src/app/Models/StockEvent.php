<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StockEvent extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'raw_payload' => 'array',
        ];
    }

    public function watchlist(): BelongsTo
    {
        return $this->belongsTo(StockWatchlist::class, 'stock_watchlist_id');
    }

    public function analysis(): HasOne
    {
        return $this->hasOne(StockAiAnalysis::class);
    }
}
