<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GoogleAiScoringService
{
    /**
     * 日記トレーニングを採点する
     */
    public function scoreDiary(string $diaryBody): array
    {
        $prompt = <<<PROMPT
あなたは文章トレーニングの先生です。
以下の日記を採点してください。

採点基準：
- 総合点：100点満点
- 読みやすさ：25点満点
- 具体性：25点満点
- 構成：25点満点
- 表現力：25点満点

必ず次のJSON形式だけで返してください。
説明文やMarkdownは不要です。

{
  "total_score": 78,
  "readability_score": 20,
  "specificity_score": 18,
  "structure_score": 17,
  "expression_score": 23,
  "good_point": "良い点を日本語で書く",
  "improvement_point": "改善点を日本語で書く",
  "next_task": "次回の課題を日本語で書く"
}

日記：
{$diaryBody}
PROMPT;

        return $this->requestScore($prompt);
    }

    /**
     * 今日のチャレンジを採点する
     */
    public function scoreChallenge(array $data): array
    {
        $prompt = <<<PROMPT
あなたは行動改善トレーニングの先生です。
以下の「今日のチャレンジ」を採点してください。

採点基準：
- 総合点：100点満点
- 読みやすさ：25点満点
- 具体性：25点満点
- 構成：25点満点
- 表現力：25点満点

必ず次のJSON形式だけで返してください。
説明文やMarkdownは不要です。

{
  "total_score": 78,
  "readability_score": 20,
  "specificity_score": 18,
  "structure_score": 17,
  "expression_score": 23,
  "good_point": "良い点を日本語で書く",
  "improvement_point": "改善点を日本語で書く",
  "next_task": "次回の課題を日本語で書く"
}

今日チャレンジしたこと：
{$data['challenged_thing']}

できたこと：
{$data['completed_thing']}

難しかったこと：
{$data['difficult_thing']}

次に改善したいこと：
{$data['next_improvement']}
PROMPT;

        return $this->requestScore($prompt);
    }

    /**
     * Google AIへ採点依頼する
     */
    private function requestScore(string $prompt): array
    {
        $apiKey = config('services.google_ai.api_key');
        $model = config('services.google_ai.model', 'gemini-2.0-flash');

        if (! $apiKey) {
            throw new RuntimeException('GOOGLE_AI_API_KEY が設定されていません。');
        }

        $response = Http::timeout(30)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $apiKey,
            ])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt,
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'response_mime_type' => 'application/json',
                ],
            ]);

        if (! $response->successful()) {
            Log::error('Google AI採点APIエラー', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Google AIによる採点に失敗しました。');
        }

        $text = $response->json('candidates.0.content.parts.0.text');

        if (! $text) {
            Log::error('Google AI採点レスポンス取得失敗', [
                'response' => $response->json(),
            ]);

            throw new RuntimeException('Google AIの採点結果を取得できませんでした。');
        }

        $score = json_decode($text, true);

        if (! is_array($score)) {
            Log::error('Google AI採点JSON変換失敗', [
                'text' => $text,
            ]);

            throw new RuntimeException('Google AIの採点結果をJSONとして解析できませんでした。');
        }

        return [
            'total_score' => (int) ($score['total_score'] ?? 0),
            'readability_score' => (int) ($score['readability_score'] ?? 0),
            'specificity_score' => (int) ($score['specificity_score'] ?? 0),
            'structure_score' => (int) ($score['structure_score'] ?? 0),
            'expression_score' => (int) ($score['expression_score'] ?? 0),
            'good_point' => (string) ($score['good_point'] ?? ''),
            'improvement_point' => (string) ($score['improvement_point'] ?? ''),
            'next_task' => (string) ($score['next_task'] ?? ''),
            'ai_response' => $score,
        ];
    }
}
