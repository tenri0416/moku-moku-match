<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSatisfactionSurvey extends Model
{
    use HasFactory;

    public const STATUS_ANSWERED = 'answered';
    public const STATUS_SKIPPED = 'skipped';

    protected $fillable = [
        'user_id',
        'status',
        'satisfaction',
        'improvement_text',
        'next_display_at',
    ];

    protected $casts = [
        'satisfaction' => 'integer',
        'next_display_at' => 'datetime',
    ];

    /**
     * 回答ユーザーを取得する。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 状態ラベルを取得する。
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ANSWERED => '回答済み',
            self::STATUS_SKIPPED => '今月は回答しない',
            default => '不明',
        };
    }

    /**
     * 満足度ラベルを取得する。
     */
    public function getSatisfactionLabelAttribute(): string
    {
        return match ($this->satisfaction) {
            5 => 'とても満足',
            4 => '満足',
            3 => '普通',
            2 => 'やや不満',
            1 => '不満',
            default => '-',
        };
    }
}
