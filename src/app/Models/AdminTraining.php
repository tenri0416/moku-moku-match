<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminTraining extends Model
{
    public const TYPE_DIARY = 'diary';
    public const TYPE_CHALLENGE = 'challenge';

    protected $fillable = [
        'admin_id',
        'type',
        'training_date',
        'diary_body',
        'challenged_thing',
        'completed_thing',
        'difficult_thing',
        'next_improvement',
        'total_score',
        'readability_score',
        'specificity_score',
        'structure_score',
        'expression_score',
        'good_point',
        'improvement_point',
        'next_task',
        'ai_response',
    ];

    protected $casts = [
        'training_date' => 'date',
        'ai_response' => 'array',
    ];

    /**
     * 管理者
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * 日記トレーニングか判定する
     */
    public function isDiary(): bool
    {
        return $this->type === self::TYPE_DIARY;
    }

    /**
     * 今日のチャレンジか判定する
     */
    public function isChallenge(): bool
    {
        return $this->type === self::TYPE_CHALLENGE;
    }
}
