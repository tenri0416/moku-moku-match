<?php

namespace App\Services\Trainings\Ai;

use App\Services\Ai\AiProviderManager;
use App\Services\Ai\Support\AiJsonParser;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class BaseAiTrainingService
{
    public function __construct(
        protected readonly AiProviderManager $aiProviderManager,
        protected readonly AiJsonParser $jsonParser,
        protected readonly LocalTrainingScoringService $localTrainingScoringService,
        protected readonly TrainingScoreFormatter $scoreFormatter,
    ) {
    }

    protected function requestScore(
        string $prompt,
        string $fallbackText,
        string $actionName,
        float $temperature = 0.2
    ): array {
        $result = $this->aiProviderManager->requestJson(
            prompt: $prompt,
            temperature: $temperature,
        );

        if ($result['success']) {
            try {
                $score = $this->jsonParser->parse($result['text']);

                return $this->scoreFormatter->format(
                    score: $score,
                    aiProvider: $result['provider'],
                    aiModel: $result['model'],
                    aiStatus: 'success',
                    aiErrorMessage: $result['error_message'],
                    isFallback: $result['is_fallback'],
                    aiAttempts: $result['attempts'],
                );
            } catch (Throwable $e) {
                Log::warning('AIレスポンスのJSON解析に失敗したため、Laravel簡易採点へ切り替えました。', [
                    'action' => $actionName,
                    'provider' => $result['provider'] ?? null,
                    'model' => $result['model'] ?? null,
                    'ai_text' => $result['text'] ?? null,
                    'message' => $e->getMessage(),
                ]);

                $score = $this->localTrainingScoringService->score($fallbackText);

                return $this->scoreFormatter->format(
                    score: $score,
                    aiProvider: 'local',
                    aiModel: 'laravel-rule-based',
                    aiStatus: 'success',
                    aiErrorMessage: 'AIレスポンスのJSON解析に失敗しました: ' . $e->getMessage(),
                    isFallback: true,
                    aiAttempts: ((int) ($result['attempts'] ?? 0)) + 1,
                );
            }
        }

        Log::warning('AI採点に失敗したためLaravel簡易採点へ切り替えました。', [
            'action' => $actionName,
            'error_message' => $result['error_message'],
            'attempts' => $result['attempts'],
        ]);

        $score = $this->localTrainingScoringService->score($fallbackText);

        return $this->scoreFormatter->format(
            score: $score,
            aiProvider: 'local',
            aiModel: 'laravel-rule-based',
            aiStatus: 'success',
            aiErrorMessage: $result['error_message'],
            isFallback: true,
            aiAttempts: ((int) ($result['attempts'] ?? 0)) + 1,
        );
    }

    protected function shortJsonRule(): string
    {
        return <<<TEXT
出力ルール：
- 必ずJSONのみ返す
- Markdownは禁止
- 各文章は40文字以内
- good_point、improvement_point、next_taskは各1文だけ
- 余計な説明は書かない
TEXT;
    }
}
