<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockWatchlist extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'is_active' => 'boolean',
            'notify_news' => 'boolean',
            'initialized_at' => 'datetime',
            'last_checked_at' => 'datetime',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(StockEvent::class);
    }
}
