<?php

namespace App\Models;

use App\Models\Concerns\HasUserTrainingScore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAbstractionTraining extends Model
{
    use HasUserTrainingScore;

    public const TYPE = 'abstraction';

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
        return '抽象化力トレーニング';
    }

    public function scoreLabels(): array
    {
        return [
            'readability_score' => '共通点の発見',
            'specificity_score' => '本質の捉え方',
            'structure_score' => '理由の説明',
            'expression_score' => '言葉の簡潔さ',
        ];
    }
}
