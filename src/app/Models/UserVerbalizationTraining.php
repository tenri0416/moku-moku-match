<?php

namespace App\Models;

use App\Models\Concerns\HasUserTrainingScore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVerbalizationTraining extends Model
{
    use HasUserTrainingScore;

    public const TYPE = 'verbalization';

    protected $fillable = [
        'user_id',
        'training_date',
        'question_title',
        'question_body',
        'answer_body',
        'total_score',
        'readability_score',
        'specificity_score',
        'structure_score',
        'expression_score',
        'good_point',
        'improvement_point',
        'next_task',
        'earned_points',
        'ai_response',
        'ai_provider',
        'ai_model',
        'ai_status',
        'ai_error_message',
        'is_fallback',
        'ai_attempts',
    ];

    protected $casts = [
        'training_date' => 'date',
        'ai_response' => 'array',
        'is_fallback' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function typeLabel(): string
    {
        return '言語化力トレーニング';
    }

    public function scoreLabels(): array
    {
        return [
            'readability_score' => '考えの明確さ',
            'specificity_score' => '理由の具体性',
            'structure_score' => '構成',
            'expression_score' => '伝わりやすさ',
        ];
    }
}
