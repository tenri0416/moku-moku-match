<?php

namespace App\Services\Trainings;

use App\Models\VocabularyWord;
use App\Services\AiProviderManager;
use Illuminate\Support\Facades\Log;
use Throwable;

class VocabularyAiTrainingService
{
    public function score(VocabularyWord $word, string $questionType, string $questionBody, string $answerBody): array
    {
        try {
            $result = $this->callAi(
                $this->buildScoringPrompt($word, $questionType, $questionBody, $answerBody),
                'ボキャブラリー復習採点'
            );

            $data = $this->decodeJson($result['content'] ?? '');
            $this->validateScoreData($data);

            return [
                'total_score' => $this->normalizeScore($data['total_score']),
                'meaning_score' => $this->normalizeSubScore($data['meaning_score']),
                'explanation_score' => $this->normalizeSubScore($data['explanation_score']),
                'usage_score' => $this->normalizeSubScore($data['usage_score']),
                'retention_score' => $this->normalizeSubScore($data['retention_score']),
                'good_point' => $data['good_point'],
                'improvement_point' => $data['improvement_point'],
                'correct_meaning' => $data['correct_meaning'],
                'next_task' => $data['next_task'],
                'ai_provider' => $result['provider'] ?? null,
                'ai_model' => $result['model'] ?? null,
                'ai_status' => 'success',
                'is_fallback' => false,
                'ai_attempts' => $result['attempts'] ?? 1,
            ];
        } catch (Throwable $e) {
            Log::warning('ボキャブラリー復習AI採点に失敗したためLaravel簡易採点へ切り替えました。', [
                'message' => $e->getMessage(),
                'word_id' => $word->id,
            ]);

            return $this->simpleScore($word, $questionType, $answerBody, $e->getMessage());
        }
    }

    private function buildScoringPrompt(VocabularyWord $word, string $questionType, string $questionBody, string $answerBody): string
    {
        return <<<PROMPT
あなたは、ボキャブラリー復習トレーニングの回答を採点するAIです。

ユーザーが登録した言葉の意味や例文が間違っている可能性があります。
そのため、登録内容だけを絶対的な正解とせず、一般的な日本語の意味・語法・文脈も踏まえて採点してください。

【登録された言葉】
{$word->word}

【登録された意味】
{$word->meaning}

【登録された例文】
{$word->example_sentence}

【問題形式】
{$questionType}

【問題文】
{$questionBody}

【ユーザー回答】
{$answerBody}

【採点基準】
・意味の正確さ
・自分の言葉で説明できているか
・例文や使い方が自然か
・文脈に合っているか
・誤用がないか

【出力ルール】
・JSONのみ返す
・Markdownは禁止
・余計な説明は禁止
・各スコアは0〜25点
・total_score は0〜100点
・correct_meaning には正しい意味を簡潔に書く
・改善点は短く分かりやすくする

【JSON形式】
{
  "total_score": 86,
  "meaning_score": 23,
  "explanation_score": 21,
  "usage_score": 22,
  "retention_score": 20,
  "good_point": "言葉の意味を自分の言葉で説明できています。",
  "improvement_point": "もう少し具体的な使用場面を添えると、理解が深まります。",
  "correct_meaning": "正しい意味の補足",
  "next_task": "次回の課題"
}
PROMPT;
    }

    private function simpleScore(VocabularyWord $word, string $questionType, string $answerBody, ?string $errorMessage = null): array
    {
        $length = mb_strlen($answerBody);

        $containsWord = str_contains($answerBody, $word->word);
        $hasReason = str_contains($answerBody, '意味')
            || str_contains($answerBody, 'こと')
            || str_contains($answerBody, '使')
            || str_contains($answerBody, '場面')
            || str_contains($answerBody, '例えば');

        $meaningScore = $length >= 20 ? 20 : 15;
        $explanationScore = $hasReason ? 21 : 16;
        $usageScore = ($questionType === '例文を作る問題' && $containsWord) ? 22 : 18;
        $retentionScore = $length >= 40 ? 20 : 16;

        $totalScore = min(100, $meaningScore + $explanationScore + $usageScore + $retentionScore);

        return [
            'total_score' => $totalScore,
            'meaning_score' => $meaningScore,
            'explanation_score' => $explanationScore,
            'usage_score' => $usageScore,
            'retention_score' => $retentionScore,
            'good_point' => '登録した言葉について、自分の言葉でアウトプットできています。',
            'improvement_point' => '意味だけでなく、どんな場面で使うかも一緒に書くと定着しやすくなります。',
            'correct_meaning' => $word->meaning,
            'next_task' => '次回は、この言葉を使った自然な例文も作ってみましょう。',
            'ai_provider' => 'laravel',
            'ai_model' => 'simple-scoring',
            'ai_status' => 'fallback',
            'ai_error_message' => $errorMessage,
            'is_fallback' => true,
            'ai_attempts' => 0,
        ];
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

    private function validateScoreData(array $data): void
    {
        foreach ([
            'total_score',
            'meaning_score',
            'explanation_score',
            'usage_score',
            'retention_score',
            'good_point',
            'improvement_point',
            'correct_meaning',
            'next_task',
        ] as $key) {
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
