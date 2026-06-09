<?php

namespace App\Services\Trainings;

use App\Models\UserImaginationTraining;
use App\Services\AiProviderManager;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImaginationAiTrainingService
{
    public function __construct(
        private readonly ImaginationFixedQuestionService $fixedQuestionService,
    ) {
    }

    public function generateQuestion(string $difficultyLabel, array $usedKeys = []): array
    {
        try {
            $result = $this->callAi(
                $this->buildQuestionPrompt($difficultyLabel, $usedKeys),
                '想像力トレーニング問題生成'
            );

            $data = $this->decodeJson($result['content'] ?? '');
            $this->validateQuestionData($data);

            $normalizedQuestionKey = $this->fixedQuestionService->makeNormalizedQuestionKey($data['question_body']);

            if (in_array($normalizedQuestionKey, $usedKeys, true)) {
                throw new \RuntimeException('AIが過去出題済みの問題を返しました。');
            }

            return [
                'question_title' => $data['question_title'] ?? '想像力トレーニング',
                'question_type' => $data['question_type'],
                'difficulty_label' => $data['difficulty_label'] ?? $difficultyLabel,
                'question_body' => $data['question_body'],
                'normalized_question_key' => $normalizedQuestionKey,
                'model_answer' => $data['model_answer'],
                'alternative_answer' => $data['alternative_answer'],
                'answer_point' => $data['answer_point'],
                'ai_provider' => $result['provider'] ?? null,
                'ai_model' => $result['model'] ?? null,
                'ai_status' => 'success',
                'is_fallback' => false,
                'ai_attempts' => $result['attempts'] ?? 1,
            ];
        } catch (Throwable $e) {
            Log::warning('想像力トレーニング問題生成に失敗したため固定問題へ切り替えました。', [
                'message' => $e->getMessage(),
                'difficulty_label' => $difficultyLabel,
            ]);

            return $this->fixedQuestionService->makeQuestion($difficultyLabel, $usedKeys);
        }
    }

    public function score(UserImaginationTraining $training, string $answerBody): array
    {
        try {
            $result = $this->callAi(
                $this->buildScoringPrompt($training, $answerBody),
                '想像力トレーニング採点'
            );

            $data = $this->decodeJson($result['content'] ?? '');
            $this->validateScoreData($data);

            return [
                'total_score' => $this->normalizeScore($data['total_score']),
                'imagination_score' => $this->normalizeSubScore($data['imagination_score']),
                'reason_score' => $this->normalizeSubScore($data['reason_score']),
                'perspective_score' => $this->normalizeSubScore($data['perspective_score']),
                'expression_score' => $this->normalizeSubScore($data['expression_score']),
                'good_point' => $data['good_point'],
                'improvement_point' => $data['improvement_point'],
                'next_task' => $data['next_task'],
                'ai_provider' => $result['provider'] ?? null,
                'ai_model' => $result['model'] ?? null,
                'ai_status' => 'success',
                'is_fallback' => false,
                'ai_attempts' => $result['attempts'] ?? 1,
            ];
        } catch (Throwable $e) {
            Log::warning('想像力トレーニング採点に失敗したためLaravel簡易採点へ切り替えました。', [
                'message' => $e->getMessage(),
                'training_id' => $training->id,
            ]);

            return $this->fixedQuestionService->simpleScore($training, $answerBody);
        }
    }

    private function buildQuestionPrompt(string $difficultyLabel, array $usedKeys): string
    {
        $usedKeysText = empty($usedKeys) ? 'なし' : implode("\n", array_slice($usedKeys, 0, 80));

        return <<<PROMPT
あなたは、想像力トレーニングの問題を作成するAIです。

ユーザーは、短いお題を見て、状況・感情・背景・未来・別視点を想像して回答します。

【難易度】
{$difficultyLabel}

【難易度の意味】
初級：日常の身近な場面。状況や気持ちを想像しやすい問題。
中級：人間関係、仕事、選択、失敗、迷い。複数の可能性を考える問題。
上級：価値観、人生観、未来、社会、比喩。抽象度が高い問題。

【過去に出題済みの normalized_question_key】
{$usedKeysText}

【禁止ルール】
・過去に出題済みの問題と重複しない
・同じ場面やテーマを短期間に繰り返さない
・重すぎるテーマ、不安を強く煽るテーマは禁止
・性的、暴力的、差別的テーマは禁止
・実在の政治家、宗教、病気、個人属性に強く関わるテーマは避ける

【出力ルール】
・JSONのみ返す
・Markdownは禁止
・余計な説明は禁止
・question_body は短くスマホで読みやすくする
・model_answer は100〜180文字程度
・alternative_answer は100〜180文字程度
・answer_point は50文字以内

【JSON形式】
{
  "question_title": "想像力トレーニング",
  "question_type": "状況想像型",
  "difficulty_label": "{$difficultyLabel}",
  "question_body": "静かなカフェで、1人の人が何度も時計を見ています。この人はどんな状況だと思いますか？",
  "model_answer": "模範解答例",
  "alternative_answer": "別解例",
  "answer_point": "回答時に意識するポイント"
}
PROMPT;
    }

    private function buildScoringPrompt(UserImaginationTraining $training, string $answerBody): string
    {
        return <<<PROMPT
あなたは、想像力トレーニングの回答を採点するAIです。

【問題タイプ】
{$training->question_type}

【難易度】
{$training->difficulty_label}

【問題文】
{$training->question_body}

【模範解答例】
{$training->model_answer}

【別解例】
{$training->alternative_answer}

【回答ポイント】
{$training->answer_point}

【ユーザー回答】
{$answerBody}

【評価基準】
・提示された状況から自然に想像できているか
・想像した内容に理由があるか
・別の可能性にも目を向けられているか
・相手の気持ちや立場を考えられているか
・短い文章でも伝わりやすいか

【出力ルール】
・JSONのみ返す
・Markdownは禁止
・各スコアは0〜25点
・total_score は0〜100点

【JSON形式】
{
  "total_score": 84,
  "imagination_score": 22,
  "reason_score": 21,
  "perspective_score": 20,
  "expression_score": 21,
  "good_point": "良い点",
  "improvement_point": "改善点",
  "next_task": "次回の課題"
}
PROMPT;
    }

    private function callAi(string $prompt, string $actionName): array
    {
        if (! class_exists(AiProviderManager::class)) {
            throw new \RuntimeException('AiProviderManager が見つかりません。');
        }

        $manager = app(AiProviderManager::class);

        foreach (['generate', 'generateText', 'ask', 'request'] as $method) {
            if (method_exists($manager, $method)) {
                return $manager->{$method}($prompt, $actionName);
            }
        }

        throw new \RuntimeException('AiProviderManager の呼び出しメソッドが見つかりません。');
    }

    private function decodeJson(string $content): array
    {
        $content = trim($content);
        $content = preg_replace('/^```json\s*/u', '', $content);
        $content = preg_replace('/^```\s*/u', '', $content);
        $content = preg_replace('/\s*```$/u', '', $content);

        $data = json_decode($content, true);

        if (! is_array($data)) {
            throw new \RuntimeException('AIレスポンスのJSON解析に失敗しました。');
        }

        return $data;
    }

    private function validateQuestionData(array $data): void
    {
        foreach (['question_type', 'difficulty_label', 'question_body', 'model_answer', 'alternative_answer', 'answer_point'] as $key) {
            if (blank($data[$key] ?? null)) {
                throw new \RuntimeException("AI問題生成レスポンスに {$key} がありません。");
            }
        }
    }

    private function validateScoreData(array $data): void
    {
        foreach (['total_score', 'imagination_score', 'reason_score', 'perspective_score', 'expression_score', 'good_point', 'improvement_point', 'next_task'] as $key) {
            if (! array_key_exists($key, $data)) {
                throw new \RuntimeException("AI採点レスポンスに {$key} がありません。");
            }
        }
    }

    private function normalizeScore(mixed $score): int
    {
        return max(0, min(100, (int) $score));
    }

    private function normalizeSubScore(mixed $score): int
    {
        return max(0, min(25, (int) $score));
    }
}
