<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTrainingPointHistory extends Model
{
    protected $fillable = [
        'user_id',
        'training_type',
        'training_id',
        'point_type',
        'points',
        'earned_on',
        'note',
    ];

    protected $casts = [
        'earned_on' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
