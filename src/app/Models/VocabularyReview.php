<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VocabularyReview extends Model
{
    protected $fillable = [
        'user_id',
        'vocabulary_word_id',
        'question_type',
        'question_body',
        'answer_body',
        'total_score',
        'meaning_score',
        'explanation_score',
        'usage_score',
        'retention_score',
        'good_point',
        'improvement_point',
        'correct_meaning',
        'next_task',
        'earned_points',
        'ai_provider',
        'ai_model',
        'ai_status',
        'ai_error_message',
        'is_fallback',
        'ai_attempts',
        'reviewed_at',
    ];

    protected $casts = [
        'total_score' => 'integer',
        'meaning_score' => 'integer',
        'explanation_score' => 'integer',
        'usage_score' => 'integer',
        'retention_score' => 'integer',
        'earned_points' => 'integer',
        'is_fallback' => 'boolean',
        'ai_attempts' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vocabularyWord(): BelongsTo
    {
        return $this->belongsTo(VocabularyWord::class);
    }
}
