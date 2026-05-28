<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLoginCode extends Model
{
    protected $fillable = [
        'admin_id',
        'code_hash',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];
}
