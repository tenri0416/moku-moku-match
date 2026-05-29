<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar_path',
        'display_name',
        'job_type',
        'prefecture_id',
        'skills',
        'bio',
        'purpose',
        'work_style',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class);
    }
}
