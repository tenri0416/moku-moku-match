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

    $score = $this->decodeJsonText($text);

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

  /**
   * AI出題型トレーニングの問題を生成する
   */
  public function generateAiTrainingQuestion(string $type): array
  {
    $prompt = match ($type) {
      'summary' => $this->summaryQuestionPrompt(),
      'verbalization' => $this->verbalizationQuestionPrompt(),
      'abstraction' => $this->abstractionQuestionPrompt(),
      'concretization' => $this->concretizationQuestionPrompt(),
      default => throw new RuntimeException('不正なトレーニング種別です。'),
    };

    return $this->requestQuestion($prompt);
  }

  /**
   * AI出題型トレーニングを採点する
   */
  public function scoreAiTraining(string $type, string $questionTitle, string $questionBody, string $answerBody): array
  {
    $scoreLabels = match ($type) {
      'summary' => [
        'score_1' => '重要点の抽出',
        'score_2' => '簡潔さ',
        'score_3' => '正確性',
        'score_4' => 'わかりやすさ',
      ],
      'verbalization' => [
        'score_1' => '考えの明確さ',
        'score_2' => '理由の具体性',
        'score_3' => '構成',
        'score_4' => '伝わりやすさ',
      ],
      'abstraction' => [
        'score_1' => '共通点の発見',
        'score_2' => '本質の捉え方',
        'score_3' => '理由の説明',
        'score_4' => '言葉の簡潔さ',
      ],
      'concretization' => [
        'score_1' => '具体例のわかりやすさ',
        'score_2' => '行動への落とし込み',
        'score_3' => '相手目線',
        'score_4' => '実行しやすさ',
      ],
      default => throw new RuntimeException('不正なトレーニング種別です。'),
    };

    $prompt = <<<PROMPT
あなたは文章力・思考力トレーニングの先生です。
以下の問題に対する回答を採点してください。

トレーニング種別：
{$type}

問題タイトル：
{$questionTitle}

問題本文：
{$questionBody}

回答：
{$answerBody}

採点基準：
- 総合点：100点満点
- {$scoreLabels['score_1']}：25点満点
- {$scoreLabels['score_2']}：25点満点
- {$scoreLabels['score_3']}：25点満点
- {$scoreLabels['score_4']}：25点満点

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
PROMPT;

    return $this->requestScore($prompt);
  }

  /**
   * 要約力トレーニングの問題生成プロンプト
   */
  private function summaryQuestionPrompt(): string
  {
    return <<<PROMPT
あなたは要約力トレーニングの先生です。
要約力を鍛える問題を1つ作成してください。

条件：
- 日本語
- 元文章は300〜600文字
- テーマは仕事、学習、フリーランス、コミュニケーション、生活改善のどれか
- 難しすぎない
- 100〜150文字で要約させる問題にする
- 毎日取り組みやすい内容にする

必ず次のJSON形式だけで返してください。
説明文やMarkdownは不要です。

{
  "question_title": "150文字以内で要約してください",
  "question_body": "ここに要約対象の文章を書く"
}
PROMPT;
  }

  /**
   * 言語化力トレーニングの問題生成プロンプト
   */
  private function verbalizationQuestionPrompt(): string
  {
    return <<<PROMPT
あなたは言語化力トレーニングの先生です。
自分の考えを言葉にする力を鍛える問題を1つ作成してください。

条件：
- 日本語
- 仕事、学習、人間関係、説明力、継続力、改善力に関するテーマ
- 回答者が自分の経験や考えを書きやすいテーマ
- 以下の観点で回答させる
  1. 何があったか
  2. そのとき何を感じたか
  3. なぜそう感じたか
  4. 次にどう改善したいか

必ず次のJSON形式だけで返してください。
説明文やMarkdownは不要です。

{
  "question_title": "最近、説明がうまく伝わらなかった経験について",
  "question_body": "以下の4つに分けて書いてください。1. 何があったか 2. そのとき何を感じたか 3. なぜそう感じたか 4. 次にどう改善したいか"
}
PROMPT;
  }

  /**
   * 抽象化力トレーニングの問題生成プロンプト
   */
  private function abstractionQuestionPrompt(): string
  {
    return <<<PROMPT
あなたは抽象化力トレーニングの先生です。
具体例から共通点や本質を見つける力を鍛える問題を1つ作成してください。

条件：
- 日本語
- 3つの具体例を出す
- それらに共通する問題、本質、パターンを考えさせる
- テーマは仕事、学習、説明、改善、コミュニケーションのどれか
- 難しすぎない
- 毎日取り組みやすい内容

必ず次のJSON形式だけで返してください。
説明文やMarkdownは不要です。

{
  "question_title": "3つの具体例に共通する本質を考えてください",
  "question_body": "例1：...\\n例2：...\\n例3：...\\n質問：これらに共通する問題を一言で抽象化し、その理由も説明してください。"
}
PROMPT;
  }

  /**
   * 具体化力トレーニングの問題生成プロンプト
   */
  private function concretizationQuestionPrompt(): string
  {
    return <<<PROMPT
あなたは具体化力トレーニングの先生です。
抽象的な言葉を具体的な行動や場面に落とし込む力を鍛える問題を1つ作成してください。

条件：
- 日本語
- 抽象的なテーマを1つ出す
- 具体的な場面、実際の行動、相手への伝わり方、明日からできる小さな行動を書かせる
- テーマは仕事、学習、説明、改善、継続、コミュニケーションのどれか
- 難しすぎない
- 毎日取り組みやすい内容

必ず次のJSON形式だけで返してください。
説明文やMarkdownは不要です。

{
  "question_title": "抽象的な言葉を具体的な行動に落とし込んでください",
  "question_body": "テーマ：わかりやすく伝える\\n回答条件：1. 具体的な場面 2. 実際に取る行動 3. 相手にどう伝わるか 4. 明日からできる小さな行動"
}
PROMPT;
  }

  /**
   * Google AIへ問題生成を依頼する
   */
  private function requestQuestion(string $prompt): array
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
          'temperature' => 0.8,
          'response_mime_type' => 'application/json',
        ],
      ]);

    if (! $response->successful()) {
      Log::error('Google AI問題生成APIエラー', [
        'status' => $response->status(),
        'body' => $response->body(),
      ]);

      throw new RuntimeException('Google AIによる問題生成に失敗しました。');
    }

    $text = $response->json('candidates.0.content.parts.0.text');

    if (! $text) {
      Log::error('Google AI問題生成レスポンス取得失敗', [
        'response' => $response->json(),
      ]);

      throw new RuntimeException('Google AIの問題生成結果を取得できませんでした。');
    }

    $question = $this->decodeJsonText($text);

    return [
      'question_title' => trim((string) ($question['question_title'] ?? '本日のトレーニング問題')),
      'question_body' => trim((string) ($question['question_body'] ?? '')),
  ];
  }

  /**
   * AIレスポンス文字列をJSONとして解析する
   */
  private function decodeJsonText(string $text): array
  {
    $text = trim($text);
    $text = preg_replace('/^```json\s*/', '', $text);
    $text = preg_replace('/^```\s*/', '', $text);
    $text = preg_replace('/\s*```$/', '', $text);

    $decoded = json_decode($text, true);

    if (! is_array($decoded)) {
      Log::error('Google AI JSON変換失敗', [
        'text' => $text,
      ]);

      throw new RuntimeException('Google AIのレスポンスをJSONとして解析できませんでした。');
    }

    return $decoded;
  }
}
