<?php

namespace App\Models;

use App\Models\Concerns\HasUserTrainingScore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserConcretizationTraining extends Model
{
    use HasUserTrainingScore;

    public const TYPE = 'concretization';

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
    ];

    protected $casts = [
        'training_date' => 'date',
        'ai_response' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function typeLabel(): string
    {
        return '具体化力トレーニング';
    }

    public function scoreLabels(): array
    {
        return [
            'readability_score' => '具体例のわかりやすさ',
            'specificity_score' => '行動への落とし込み',
            'structure_score' => '相手目線',
            'expression_score' => '実行しやすさ',
        ];
    }
}
