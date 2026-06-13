<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VocabularyPrintTestQuestion extends Model
{
    protected $fillable = [
        'vocabulary_print_test_id',
        'vocabulary_word_id',
        'question_number',
        'question_type',
        'question_body',
        'point',
        'answer_text',
        'explanation_text',
        'choices_json',
        'correct_choice',
        'scoring_rule_json',
    ];

    protected $casts = [
        'question_number' => 'integer',
        'point' => 'integer',
        'choices_json' => 'array',
        'scoring_rule_json' => 'array',
    ];

    public function printTest(): BelongsTo
    {
        return $this->belongsTo(VocabularyPrintTest::class, 'vocabulary_print_test_id');
    }

    public function vocabularyWord(): BelongsTo
    {
        return $this->belongsTo(VocabularyWord::class);
    }
}
