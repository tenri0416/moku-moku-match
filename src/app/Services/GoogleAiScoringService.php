<?php

namespace App\Services;

use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GoogleAiScoringService
{
    /**
     * Google AI APIの最大試行回数
     *
     * モデルを切り替えながら最大5回まで試す。
     */
    private const MAX_ATTEMPT_COUNT = 5;

    /**
     * 日記トレーニングを採点する
     */
    public function scoreDiary(string $diaryBody, int|string $difficulty = 0): array
    {
        $prompt = <<<PROMPT
あなたは文章トレーニングの先生です。
以下の日記を採点してください。

{$this->difficultyInstruction($difficulty)}

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
    public function scoreChallenge(array $data, int|string $difficulty = 0): array
    {
        $prompt = <<<PROMPT
あなたは行動改善トレーニングの先生です。
以下の「今日のチャレンジ」を採点してください。

{$this->difficultyInstruction($difficulty)}

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
     * AI出題型トレーニングの問題を生成する
     */
    public function generateAiTrainingQuestion(string $type, int|string $difficulty = 0): array
    {
        $prompt = match ($type) {
            'summary' => $this->summaryQuestionPrompt($difficulty),
            'verbalization' => $this->verbalizationQuestionPrompt($difficulty),
            'abstraction' => $this->abstractionQuestionPrompt($difficulty),
            'concretization' => $this->concretizationQuestionPrompt($difficulty),
            default => throw new RuntimeException('不正なトレーニング種別です。'),
        };

        return $this->requestQuestion($prompt);
    }

    /**
     * AI出題型トレーニングを採点する
     */
    public function scoreAiTraining(
        string $type,
        string $questionTitle,
        string $questionBody,
        string $answerBody,
        int|string $difficulty = 0
    ): array {
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

{$this->difficultyInstruction($difficulty)}

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
     * ユーザーの総獲得ポイントに応じた難易度指示を作成する
     */
    private function difficultyInstruction(int|string $difficulty): string
    {
        $difficultyText = (string) $difficulty;

        $levelInstruction = match ($difficultyText) {
            '0' => '初心者向け。問題はやさしく、回答しやすい内容にする。アドバイスは基礎的で前向きにする。',
            '1', '2' => '初級向け。少しだけ理由や具体例を求める。アドバイスは改善しやすい内容にする。',
            '3', '4' => '初中級向け。理由、具体例、改善案を含めた回答を促す。採点では構成と具体性も見る。',
            '5', '6' => '中級向け。比較、原因分析、改善行動まで求める。採点では論理性と深さも重視する。',
            '7', '8' => '中上級向け。複数の観点から考えさせる。採点では本質理解、説得力、実行可能性を重視する。',
            '9', '10' => '上級向け。抽象度の高いテーマや複雑な状況を扱う。採点では論理構成、洞察、具体化の質を厳しめに見る。',
            'Max' => '最高難易度。実務・対人・学習改善など複合的なテーマにする。採点では深い洞察、再現性、行動への落とし込みを高く求める。',
            default => '初心者向け。問題はやさしく、回答しやすい内容にする。アドバイスは基礎的で前向きにする。',
        };

        return <<<TEXT
現在のトレーニング難易度：{$difficultyText}

難易度に応じた方針：
{$levelInstruction}

注意：
- 難易度は問題の難しさ、採点の厳しさ、アドバイス、次回課題に反映してください。
- ただし、ユーザーの継続意欲を下げないように、否定的すぎる表現は避けてください。
TEXT;
    }

    /**
     * Google AIへ採点依頼する
     */
    private function requestScore(string $prompt): array
    {
        $response = $this->postGeminiWithRetry(
            prompt: $prompt,
            temperature: 0.2,
            actionName: '採点'
        );

        if (! $response->successful()) {
            $this->logGoogleAiError('Google AI採点APIエラー', $response);

            throw new RuntimeException(
                $this->buildFriendlyErrorMessage($response, '採点')
            );
        }

        $text = $this->getTextFromResponse($response, '採点');

        $score = $this->decodeJsonText($text);

        return [
            'total_score' => (int) ($score['total_score'] ?? 0),
            'readability_score' => (int) ($score['readability_score'] ?? 0),
            'specificity_score' => (int) ($score['specificity_score'] ?? 0),
            'structure_score' => (int) ($score['structure_score'] ?? 0),
            'expression_score' => (int) ($score['expression_score'] ?? 0),
            'good_point' => $this->normalizeAiText((string) ($score['good_point'] ?? '')),
            'improvement_point' => $this->normalizeAiText((string) ($score['improvement_point'] ?? '')),
            'next_task' => $this->normalizeAiText((string) ($score['next_task'] ?? '')),
            'ai_response' => $score,
        ];
    }

    /**
     * Google AIへ問題生成を依頼する
     */
    private function requestQuestion(string $prompt): array
    {
        $response = $this->postGeminiWithRetry(
            prompt: $prompt,
            temperature: 0.8,
            actionName: '問題生成'
        );

        if (! $response->successful()) {
            $this->logGoogleAiError('Google AI問題生成APIエラー', $response);

            throw new RuntimeException(
                $this->buildFriendlyErrorMessage($response, '問題生成')
            );
        }

        $text = $this->getTextFromResponse($response, '問題生成');

        $question = $this->decodeJsonText($text);

        return [
            'question_title' => $this->normalizeAiText((string) ($question['question_title'] ?? '本日のトレーニング問題')),
            'question_body' => $this->normalizeAiText((string) ($question['question_body'] ?? '')),
        ];
    }

    /**
     * Gemini APIへモデル切り替え付きでリクエストする
     *
     * 無料枠やモデル別上限に達した場合、別モデルへ切り替えて最大5回まで試す。
     * ただし、APIキー・プロジェクト全体の上限に達している場合は、モデルを変えても失敗する可能性がある。
     */
    private function postGeminiWithRetry(string $prompt, float $temperature, string $actionName): HttpResponse
    {
        $apiKey = config('services.google_ai.api_key');

        if (! $apiKey) {
            throw new RuntimeException('GOOGLE_AI_API_KEY が設定されていません。');
        }

        $models = $this->fallbackModels();

        $response = null;
        $attempt = 0;

        foreach ($models as $model) {
            $attempt++;

            if ($attempt > self::MAX_ATTEMPT_COUNT) {
                break;
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

            try {
                $response = Http::timeout(60)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'X-goog-api-key' => $apiKey,
                    ])
                    ->post($url, [
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
                            'temperature' => $temperature,
                            'response_mime_type' => 'application/json',
                        ],
                    ]);
            } catch (\Throwable $e) {
                Log::warning('Google AI通信例外が発生しました。次のモデルを試します。', [
                    'action' => $actionName,
                    'model' => $model,
                    'attempt' => $attempt,
                    'max_attempts' => self::MAX_ATTEMPT_COUNT,
                    'message' => $e->getMessage(),
                ]);

                if ($attempt >= self::MAX_ATTEMPT_COUNT) {
                    throw new RuntimeException(
                        'Google AIとの通信に失敗しました。ネットワーク状況を確認して、少し時間を置いて再度お試しください。'
                    );
                }

                sleep($this->retryDelaySeconds(null, $attempt));
                continue;
            }

            if ($response->successful()) {
                if ($attempt > 1) {
                    Log::info('Google AI APIがモデル切り替え後に成功しました', [
                        'action' => $actionName,
                        'model' => $model,
                        'attempt' => $attempt,
                    ]);
                }

                return $response;
            }

            Log::warning('Google AI APIリクエスト失敗。必要に応じて次のモデルを試します。', [
                'action' => $actionName,
                'model' => $model,
                'attempt' => $attempt,
                'max_attempts' => self::MAX_ATTEMPT_COUNT,
                'status' => $response->status(),
                'google_status' => $response->json('error.status'),
                'message' => $response->json('error.message'),
                'retry_delay_seconds' => $this->retryDelaySeconds($response, $attempt),
            ]);

            if ($this->shouldStopModelFallback($response)) {
                return $response;
            }

            if ($attempt >= self::MAX_ATTEMPT_COUNT) {
                return $response;
            }

            sleep($this->retryDelaySeconds($response, $attempt));
        }

        if (! $response) {
            throw new RuntimeException(
                'Google AIへのリクエストに失敗しました。少し時間を置いて再度お試しください。'
            );
        }

        return $response;
    }

    /**
     * フォールバック用のGeminiモデル一覧
     *
     * 上から順番に試す。
     * .env に GOOGLE_AI_FALLBACK_MODELS を設定すれば、そちらを優先する。
     */
    private function fallbackModels(): array
    {
        $envModels = env('GOOGLE_AI_FALLBACK_MODELS');

        if ($envModels) {
            $models = collect(explode(',', $envModels))
                ->map(fn (string $model) => trim($model))
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (! empty($models)) {
                return $models;
            }
        }

        $mainModel = config('services.google_ai.model', 'gemini-2.5-flash');

        return collect([
            $mainModel,
            'gemini-2.5-flash',
            'gemini-2.0-flash',
            'gemini-1.5-flash',
            'gemini-2.5-flash-lite',
            'gemini-2.0-flash-lite',
        ])
            ->filter()
            ->unique()
            ->take(self::MAX_ATTEMPT_COUNT)
            ->values()
            ->all();
    }

    /**
     * モデルを切り替えても改善しにくいエラーか判定する
     */
    private function shouldStopModelFallback(HttpResponse $response): bool
    {
        $status = $response->status();
        $message = strtolower((string) $response->json('error.message'));

        // リクエスト内容が悪い場合は、モデルを変えても基本的に直らない
        if ($status === 400) {
            return true;
        }

        // APIキーや権限エラーは、モデルを変えても直らない
        if (in_array($status, [401, 403], true)) {
            return true;
        }

        // モデル名が存在しない場合は、次のモデルで成功する可能性がある
        if ($status === 404) {
            return false;
        }

        // 課金・APIキー・権限など、プロジェクト全体の問題は止める
        if (str_contains($message, 'prepayment credits are depleted')
            || str_contains($message, 'billing')
            || str_contains($message, 'api key not valid')
            || str_contains($message, 'permission')
        ) {
            return true;
        }

        // quota exceeded や limit: 0 はモデル別制限の可能性もあるため、
        // 次のモデルを試す。
        return false;
    }

    /**
     * リトライ待機秒数を取得する
     */
    private function retryDelaySeconds(?HttpResponse $response, int $attempt): int
    {
        if ($response) {
            $retryDelay = $this->extractRetryDelaySeconds($response);

            if ($retryDelay !== null) {
                return min($retryDelay, 10);
            }
        }

        return match ($attempt) {
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 6,
            default => 8,
        };
    }

    /**
     * Google APIレスポンスのRetryInfoから待機秒数を取り出す
     */
    private function extractRetryDelaySeconds(HttpResponse $response): ?int
    {
        $details = $response->json('error.details');

        if (! is_array($details)) {
            return null;
        }

        foreach ($details as $detail) {
            if (! is_array($detail)) {
                continue;
            }

            $retryDelay = $detail['retryDelay'] ?? null;

            if (! is_string($retryDelay)) {
                continue;
            }

            if (preg_match('/^(\d+)s$/', $retryDelay, $matches)) {
                return (int) $matches[1];
            }
        }

        return null;
    }

    /**
     * Google AIレスポンスから本文を取得する
     */
    private function getTextFromResponse(HttpResponse $response, string $actionName): string
    {
        $text = $response->json('candidates.0.content.parts.0.text');

        if (! $text) {
            Log::error('Google AIレスポンス本文取得失敗', [
                'action' => $actionName,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            throw new RuntimeException("Google AIの{$actionName}結果を取得できませんでした。少し時間を置いて再度お試しください。");
        }

        return (string) $text;
    }

    /**
     * Google AI APIエラーを開発者向けに詳細ログ出力する
     */
    private function logGoogleAiError(string $title, HttpResponse $response): void
    {
        Log::error($title, [
            'status' => $response->status(),
            'google_status' => $response->json('error.status'),
            'google_message' => $response->json('error.message'),
            'quota_details' => $response->json('error.details'),
            'body' => $response->body(),
            'fallback_models' => $this->fallbackModels(),
        ]);
    }

    /**
     * ユーザーにも開発者にも分かりやすいエラーメッセージを作る
     */
    private function buildFriendlyErrorMessage(HttpResponse $response, string $actionName): string
    {
        $status = $response->status();
        $message = strtolower((string) $response->json('error.message'));

        if ($status === 503) {
            return "Google AIが現在混雑しているため、{$actionName}に失敗しました。少し時間を置いて再度お試しください。";
        }

        if ($status === 429) {
            if (str_contains($message, 'prepayment credits are depleted')) {
                return "現在AIの利用枠が不足しているため、{$actionName}できませんでした。入力内容はそのまま残して、少し時間を置いて再度お試しください。";
            }

            if (str_contains($message, 'limit: 0')) {
                return "現在AIの無料利用枠に制限がかかっているため、{$actionName}できませんでした。少し時間を置いて再度お試しください。";
            }

            return "現在AIの利用上限に達しているため、{$actionName}できませんでした。少し時間を置いて再度お試しください。";
        }

        if ($status === 400) {
            return "AIへのリクエスト内容に問題があるため、{$actionName}できませんでした。入力内容を少し短くするか、内容を調整して再度お試しください。";
        }

        if ($status === 401 || $status === 403) {
            return "AI機能の設定に問題があるため、現在{$actionName}できません。時間を置いて再度お試しください。";
        }

        if ($status === 404) {
            return "AIモデルが利用できないため、{$actionName}できませんでした。少し時間を置いて再度お試しください。";
        }

        return "AIによる{$actionName}に失敗しました。少し時間を置いて再度お試しください。";
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

            throw new RuntimeException('Google AIのレスポンスをJSONとして解析できませんでした。少し時間を置いて再度お試しください。');
        }

        return $decoded;
    }

    /**
     * AIが返した文章の余計な空白・インデントを整える
     */
    private function normalizeAiText(string $text): string
    {
        $lines = preg_split("/\r\n|\n|\r/", $text);

        if (! is_array($lines)) {
            return trim($text);
        }

        $lines = array_map(function (string $line) {
            return trim($line);
        }, $lines);

        return trim(implode(PHP_EOL, $lines));
    }

    /**
     * 要約力トレーニングの問題生成プロンプト
     */
    private function summaryQuestionPrompt(int|string $difficulty): string
    {
        return <<<PROMPT
あなたは要約力トレーニングの先生です。
要約力を鍛える問題を1つ作成してください。

{$this->difficultyInstruction($difficulty)}

条件：
- 日本語
- 元文章は300〜600文字
- テーマは仕事、学習、フリーランス、コミュニケーション、生活改善のどれか
- 難しすぎない
- 100〜150文字で要約させる問題にする
- 毎日取り組みやすい内容にする
- question_bodyの先頭に空白や改行を入れない
- 各行の先頭に不要なスペースを入れない

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
    private function verbalizationQuestionPrompt(int|string $difficulty): string
    {
        return <<<PROMPT
あなたは言語化力トレーニングの先生です。
自分の考えを言葉にする力を鍛える問題を1つ作成してください。

{$this->difficultyInstruction($difficulty)}

条件：
- 日本語
- 仕事、学習、人間関係、説明力、継続力、改善力に関するテーマ
- 回答者が自分の経験や考えを書きやすいテーマ
- 以下の観点で回答させる
  1. 何があったか
  2. そのとき何を感じたか
  3. なぜそう感じたか
  4. 次にどう改善したいか
- question_bodyの先頭に空白や改行を入れない
- 各行の先頭に不要なスペースを入れない

必ず次のJSON形式だけで返してください。
説明文やMarkdownは不要です。

{
  "question_title": "最近、説明がうまく伝わらなかった経験について",
  "question_body": "以下の4つに分けて書いてください。\\n1. 何があったか\\n2. そのとき何を感じたか\\n3. なぜそう感じたか\\n4. 次にどう改善したいか"
}
PROMPT;
    }

    /**
     * 抽象化力トレーニングの問題生成プロンプト
     */
    private function abstractionQuestionPrompt(int|string $difficulty): string
    {
        return <<<PROMPT
あなたは抽象化力トレーニングの先生です。
具体例から共通点や本質を見つける力を鍛える問題を1つ作成してください。

{$this->difficultyInstruction($difficulty)}

条件：
- 日本語
- 3つの具体例を出す
- それらに共通する問題、本質、パターンを考えさせる
- テーマは仕事、学習、説明、改善、コミュニケーションのどれか
- 難しすぎない
- 毎日取り組みやすい内容
- question_bodyの先頭に空白や改行を入れない
- 各行の先頭に不要なスペースを入れない

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
    private function concretizationQuestionPrompt(int|string $difficulty): string
    {
        return <<<PROMPT
あなたは具体化力トレーニングの先生です。
抽象的な言葉を具体的な行動や場面に落とし込む力を鍛える問題を1つ作成してください。

{$this->difficultyInstruction($difficulty)}

条件：
- 日本語
- 抽象的なテーマを1つ出す
- 具体的な場面、実際の行動、相手への伝わり方、明日からできる小さな行動を書かせる
- テーマは仕事、学習、説明、改善、継続、コミュニケーションのどれか
- 難しすぎない
- 毎日取り組みやすい内容
- question_bodyの先頭に空白や改行を入れない
- 各行の先頭に不要なスペースを入れない

必ず次のJSON形式だけで返してください。
説明文やMarkdownは不要です。

{
  "question_title": "抽象的な言葉を具体的な行動に落とし込んでください",
  "question_body": "テーマ：わかりやすく伝える\\n回答条件：1. 具体的な場面 2. 実際に取る行動 3. 相手にどう伝わるか 4. 明日からできる小さな行動"
}
PROMPT;
    }
}
