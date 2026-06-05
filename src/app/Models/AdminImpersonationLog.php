<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminImpersonationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'user_id',
        'started_at',
        'ended_at',
        'last_activity_at',
        'start_ip',
        'end_ip',
        'user_agent',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
