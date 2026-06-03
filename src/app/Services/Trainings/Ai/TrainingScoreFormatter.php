<?php

namespace App\Services\Trainings\Ai;

use App\Services\Ai\Support\AiTextNormalizer;

class TrainingScoreFormatter
{
    public function __construct(
        private readonly AiTextNormalizer $normalizer,
    ) {
    }

    public function format(
        array $score,
        string $aiProvider,
        string $aiModel,
        string $aiStatus,
        ?string $aiErrorMessage,
        bool $isFallback,
        int $aiAttempts
    ): array {
        return [
            'total_score' => (int) ($score['total_score'] ?? 0),
            'readability_score' => (int) ($score['readability_score'] ?? 0),
            'specificity_score' => (int) ($score['specificity_score'] ?? 0),
            'structure_score' => (int) ($score['structure_score'] ?? 0),
            'expression_score' => (int) ($score['expression_score'] ?? 0),
            'good_point' => $this->normalizer->normalize((string) ($score['good_point'] ?? '')),
            'improvement_point' => $this->normalizer->normalize((string) ($score['improvement_point'] ?? '')),
            'next_task' => $this->normalizer->normalize((string) ($score['next_task'] ?? '')),
            'ai_response' => $score,
            'ai_provider' => $aiProvider,
            'ai_model' => $aiModel,
            'ai_status' => $aiStatus,
            'ai_error_message' => $aiErrorMessage,
            'is_fallback' => $isFallback,
            'ai_attempts' => $aiAttempts,
        ];
    }
}
