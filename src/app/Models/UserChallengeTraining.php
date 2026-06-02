<?php

namespace App\Models;

use App\Models\Concerns\HasUserTrainingScore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserChallengeTraining extends Model
{
    use HasUserTrainingScore;

    public const TYPE = 'challenge';

    protected $fillable = [
        'user_id',
        'training_date',
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
        return '今日のチャレンジ';
    }
}
