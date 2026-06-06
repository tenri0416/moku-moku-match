<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReadingReflectionTraining extends Model
{
    protected $fillable = [
        'user_id',
        'read_on',
        'book_title',
        'read_minutes',
        'mood',
        'reflection_text',
    ];

    protected $casts = [
        'read_on' => 'date',
        'read_minutes' => 'integer',
    ];

    /**
     * 読書振り返りを登録したユーザー。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 読書後の感覚を日本語で返す。
     */
    public function getMoodLabelAttribute(): string
    {
        return match ($this->mood) {
            'good' => 'よく理解できた',
            'normal' => 'ふつう',
            'difficult' => '少し難しかった',
            default => '未選択',
        };
    }
}
