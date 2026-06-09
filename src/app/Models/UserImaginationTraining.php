<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserImaginationTraining extends Model
{
    public const TYPE = 'imagination';

    protected $fillable = [
        'user_id',
        'training_date',
        'question_title',
        'question_type',
        'difficulty_label',
        'question_body',
        'normalized_question_key',
        'model_answer',
        'alternative_answer',
        'answer_point',
        'answer_body',
        'total_score',
        'imagination_score',
        'reason_score',
        'perspective_score',
        'expression_score',
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
        'imagination_score' => 'integer',
        'reason_score' => 'integer',
        'perspective_score' => 'integer',
        'expression_score' => 'integer',
        'earned_points' => 'integer',
        'ai_attempts' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function typeLabel(): string
    {
        return '想像力トレーニング';
    }

    public function isAnswered(): bool
    {
        return filled($this->answer_body);
    }
}
