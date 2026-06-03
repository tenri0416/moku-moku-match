<?php

namespace App\Services\Trainings\Ai;

use App\Services\Ai\AiProviderManager;
use App\Services\Ai\Support\AiJsonParser;
use App\Services\Ai\Support\AiTextNormalizer;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

abstract class BaseQuestionAiTrainingService extends BaseAiTrainingService
{
    public function __construct(
        AiProviderManager $aiProviderManager,
        AiJsonParser $jsonParser,
        LocalTrainingScoringService $localTrainingScoringService,
        TrainingScoreFormatter $scoreFormatter,
        protected readonly AiTextNormalizer $textNormalizer,
    ) {
        parent::__construct(
            aiProviderManager: $aiProviderManager,
            jsonParser: $jsonParser,
            localTrainingScoringService: $localTrainingScoringService,
            scoreFormatter: $scoreFormatter,
        );
    }

    abstract public function type(): string;

    abstract protected function typeLabel(): string;

    abstract protected function questionPrompt(int|string $difficulty): string;

    abstract protected function localQuestion(): array;

    abstract protected function scoreLabels(): array;

    public function generateQuestion(int|string $difficulty = 0): array
    {
        $result = $this->aiProviderManager->requestJson(
            prompt: $this->questionPrompt($difficulty),
            temperature: 0.7,
            actionName: $this->typeLabel() . '問題生成',
        );

        if ($result['success']) {
            try {
                $question = $this->jsonParser->parse($result['text']);

                $questionTitle = $this->textNormalizer->normalize(
                    (string) ($question['question_title'] ?? '')
                );

                $questionBody = $this->textNormalizer->normalize(
                    (string) ($question['question_body'] ?? '')
                );

                if (! filled($questionTitle) || ! filled($questionBody)) {
                    throw new RuntimeException('AI問題生成結果の question_title または question_body が空です。');
                }

                return [
                    'question_title' => $questionTitle,
                    'question_body' => $questionBody,
                    'ai_provider' => $result['provider'],
                    'ai_model' => $result['model'],
                    'ai_status' => 'success',
                    'ai_error_message' => $result['error_message'],
                    'is_fallback' => $result['is_fallback'],
                    'ai_attempts' => $result['attempts'],
                ];
            } catch (Throwable $e) {
                Log::warning('AI問題生成レスポンスの解析に失敗したため、Laravel固定問題へ切り替えました。', [
                    'type' => $this->type(),
                    'provider' => $result['provider'] ?? null,
                    'model' => $result['model'] ?? null,
                    'ai_text' => $result['text'] ?? null,
                    'message' => $e->getMessage(),
                ]);

                return $this->localQuestionResult(
                    errorMessage: 'AI問題生成レスポンスの解析に失敗しました: ' . $e->getMessage(),
                    attempts: ((int) ($result['attempts'] ?? 0)) + 1
                );
            }
        }

        Log::warning('AI問題生成に失敗したため、Laravel固定問題へ切り替えました。', [
            'type' => $this->type(),
            'error_message' => $result['error_message'] ?? null,
            'attempts' => $result['attempts'] ?? 0,
        ]);

        return $this->localQuestionResult(
            errorMessage: $result['error_message'] ?? 'AI問題生成に失敗しました。',
            attempts: ((int) ($result['attempts'] ?? 0)) + 1
        );
    }

    public function scoreAnswer(
        string $questionTitle,
        string $questionBody,
        string $answerBody,
        int|string $difficulty = 0
    ): array {
        $labels = $this->scoreLabels();

        $prompt = <<<PROMPT
あなたは{$this->typeLabel()}の先生です。
以下の回答を採点してください。

問題タイトル：
{$questionTitle}

問題本文：
{$questionBody}

回答：
{$answerBody}

採点基準：
- 総合点：100点満点
- {$labels['score_1']}：25点満点
- {$labels['score_2']}：25点満点
- {$labels['score_3']}：25点満点
- {$labels['score_4']}：25点満点

難易度：
{$difficulty}

{$this->shortJsonRule()}

必ず次のJSON形式だけで返してください。

{
  "total_score": 78,
  "readability_score": 20,
  "specificity_score": 18,
  "structure_score": 17,
  "expression_score": 23,
  "good_point": "良い点",
  "improvement_point": "改善点",
  "next_task": "次回の課題"
}
PROMPT;

        return $this->requestScore(
            prompt: $prompt,
            fallbackText: $answerBody,
            actionName: $this->typeLabel() . '採点',
        );
    }

    private function localQuestionResult(string $errorMessage, int $attempts): array
    {
        $question = $this->localQuestion();

        return [
            'question_title' => $question['question_title'],
            'question_body' => $question['question_body'],
            'ai_provider' => 'local',
            'ai_model' => 'laravel-rule-based',
            'ai_status' => 'success',
            'ai_error_message' => $errorMessage,
            'is_fallback' => true,
            'ai_attempts' => $attempts,
        ];
    }
}
