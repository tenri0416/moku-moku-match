<?php

namespace App\Models\Concerns;

trait HasUserTrainingScore
{
    public function isAnswered(): bool
    {
        return filled($this->answer_body)
            || filled($this->diary_body)
            || filled($this->challenged_thing);
    }

    public function scoreLabels(): array
    {
        return [
            'readability_score' => '読みやすさ',
            'specificity_score' => '具体性',
            'structure_score' => '構成',
            'expression_score' => '表現力',
        ];
    }
}
