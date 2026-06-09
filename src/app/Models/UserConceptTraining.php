<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserConceptTraining extends Model
{
    protected $fillable = [
        'user_id',
        'training_date',
        'question_title',
        'theme_a',
        'theme_b',
        'normalized_pair_key',
        'difficulty_label',
        'question_body',
        'model_answer',
        'alternative_answer',
        'answer_point',
        'answer_body',
        'total_score',
        'common_point_score',
        'essence_score',
        'viewpoint_score',
        'explanation_score',
        'good_point',
        'improvement_point',
        'next_task',
        'earned_points',
        'ai_provider',
        'ai_model',
        'ai_status',
        'ai_error_message',
        'is_fallback',
        'ai_attempts',
    ];

    protected $casts = [
        'training_date' => 'date',
        'is_fallback' => 'boolean',
        'total_score' => 'integer',
        'common_point_score' => 'integer',
        'essence_score' => 'integer',
        'viewpoint_score' => 'integer',
        'explanation_score' => 'integer',
        'earned_points' => 'integer',
        'ai_attempts' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getThemePairLabelAttribute(): string
    {
        return "{$this->theme_a} × {$this->theme_b}";
    }

    public function getIsAnsweredAttribute(): bool
    {
        return filled($this->answer_body);
    }
}
