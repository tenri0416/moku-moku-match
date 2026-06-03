<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiProviderAttemptLog extends Model
{
    protected $fillable = [
        'provider',
        'model',
        'status',
        'status_code',
        'error_message',
        'retry_after_seconds',
        'retry_available_at',
        'attempt',
        'is_fallback',
        'action_name',
        'attempted_at',
    ];

    protected $casts = [
        'retry_available_at' => 'datetime',
        'attempted_at' => 'datetime',
        'is_fallback' => 'boolean',
    ];
}
