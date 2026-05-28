<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'method',
        'url',
        'path',
        'route_name',
        'referer',
        'ip_address',
        'user_agent',
        'status_code',
        'duration_ms',
        'request_data',
    ];

    protected $casts = [
        'request_data' => 'array',
    ];
}
