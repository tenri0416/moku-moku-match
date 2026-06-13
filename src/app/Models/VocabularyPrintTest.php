<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VocabularyPrintTest extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'question_count',
        'time_limit_minutes',
        'target_filter',
        'category',
        'importance',
        'question_types_json',
        'total_score',
        'status',
        'error_message',
        'generated_at',
    ];

    protected $casts = [
        'question_count' => 'integer',
        'time_limit_minutes' => 'integer',
        'importance' => 'integer',
        'question_types_json' => 'array',
        'total_score' => 'integer',
        'generated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(VocabularyPrintTestQuestion::class)
            ->orderBy('question_number');
    }
}
