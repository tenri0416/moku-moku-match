<?php

namespace App\Services;

use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class GoogleAiScoringService
{
    /**
     * AI会社ごとの最大試行回数
     */
    private const MAX_ATTEMPT_COUNT = 3;

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

        return $this->requestScore(
            prompt: $prompt,
            fallbackText: $diaryBody,
            actionName: '日記採点'
        );
    }

    /**
     * 今日のチャレンジを採点する
     */
    public function scoreChallenge(array $data, int|string $difficulty = 0): array
    {
        $fallbackText = implode("\n", [
            $data['challenged_thing'] ?? '',
            $data['completed_thing'] ?? '',
            $data['difficult_thing'] ?? '',
            $data['next_improvement'] ?? '',
        ]);

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

        return $this->requestScore(
            prompt: $prompt,
            fallbackText: $fallbackText,
            actionName: '今日のチャレンジ採点'
        );
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

        return $this->requestQuestion($prompt, $type);
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

        return $this->requestScore(
            prompt: $prompt,
            fallbackText: $answerBody,
            actionName: $type . '採点'
        );
    }

    /**
     * 採点処理
     *
     * Google → OpenRouter → Groq → Laravel簡易採点 の順番で試す。
     */
    private function requestScore(string $prompt, string $fallbackText, string $actionName): array
    {
        $result = $this->requestAiWithFallback(
            prompt: $prompt,
            temperature: 0.2,
            actionName: $actionName
        );

        if ($result['success']) {
            $score = $this->decodeJsonText($result['text']);

            return $this->formatScoreResult(
                score: $score,
                aiProvider: $result['provider'],
                aiModel: $result['model'],
                aiStatus: 'success',
                aiErrorMessage: $result['error_message'],
                isFallback: $result['is_fallback'],
                aiAttempts: $result['attempts']
            );
        }

        Log::warning('すべてのAI採点に失敗したため、Laravel簡易採点へ切り替えました。', [
            'action' => $actionName,
            'error_message' => $result['error_message'],
            'attempts' => $result['attempts'],
        ]);

        $score = $this->localScore($fallbackText);

        return $this->formatScoreResult(
            score: $score,
            aiProvider: 'local',
            aiModel: 'laravel-rule-based',
            aiStatus: 'success',
            aiErrorMessage: $result['error_message'],
            isFallback: true,
            aiAttempts: $result['attempts'] + 1
        );
    }

    /**
     * 問題生成処理
     *
     * AIが全て失敗した場合は、Laravel側で固定問題を出す。
     */
    private function requestQuestion(string $prompt, string $type): array
    {
        $result = $this->requestAiWithFallback(
            prompt: $prompt,
            temperature: 0.8,
            actionName: $type . '問題生成'
        );

        if ($result['success']) {
            $question = $this->decodeJsonText($result['text']);

            return [
                'question_title' => $this->normalizeAiText((string) ($question['question_title'] ?? '本日のトレーニング問題')),
                'question_body' => $this->normalizeAiText((string) ($question['question_body'] ?? '')),
                'ai_provider' => $result['provider'],
                'ai_model' => $result['model'],
                'ai_status' => 'success',
                'ai_error_message' => $result['error_message'],
                'is_fallback' => $result['is_fallback'],
                'ai_attempts' => $result['attempts'],
            ];
        }

        $question = $this->localQuestion($type);

        return [
            'question_title' => $question['question_title'],
            'question_body' => $question['question_body'],
            'ai_provider' => 'local',
            'ai_model' => 'laravel-rule-based',
            'ai_status' => 'success',
            'ai_error_message' => $result['error_message'],
            'is_fallback' => true,
            'ai_attempts' => $result['attempts'] + 1,
        ];
    }

    /**
     * AI会社をまたいでフォールバックする
     */
    private function requestAiWithFallback(string $prompt, float $temperature, string $actionName): array
    {
        $providers = $this->providers();

        $attempts = 0;
        $lastErrorMessage = null;

        foreach ($providers as $provider) {
            $attempts++;

            try {
                $response = match ($provider['type']) {
                    'google' => $this->postGoogle(
                        model: $provider['model'],
                        apiKey: $provider['api_key'],
                        prompt: $prompt,
                        temperature: $temperature
                    ),
                    'openai_compatible' => $this->postOpenAiCompatible(
                        endpoint: $provider['endpoint'],
                        apiKey: $provider['api_key'],
                        model: $provider['model'],
                        prompt: $prompt,
                        temperature: $temperature,
                        extraHeaders: $provider['extra_headers'] ?? []
                    ),
                    default => throw new RuntimeException('未対応のAIプロバイダーです。'),
                };

                if ($response->successful()) {
                    return [
                        'success' => true,
                        'provider' => $provider['name'],
                        'model' => $provider['model'],
                        'text' => $this->extractText($response, $provider['type']),
                        'error_message' => $lastErrorMessage,
                        'is_fallback' => $attempts > 1,
                        'attempts' => $attempts,
                    ];
                }

                $lastErrorMessage = $this->responseErrorMessage($response);

                Log::warning('AIリクエストに失敗しました。次のAI会社を試します。', [
                    'action' => $actionName,
                    'provider' => $provider['name'],
                    'model' => $provider['model'],
                    'status' => $response->status(),
                    'message' => $lastErrorMessage,
                    'attempt' => $attempts,
                ]);

                if ($attempts >= self::MAX_ATTEMPT_COUNT) {
                    break;
                }

                sleep($this->retryDelaySeconds($response, $attempts));
            } catch (Throwable $e) {
                $lastErrorMessage = $e->getMessage();

                Log::warning('AI通信例外が発生しました。次のAI会社を試します。', [
                    'action' => $actionName,
                    'provider' => $provider['name'],
                    'model' => $provider['model'],
                    'message' => $e->getMessage(),
                    'attempt' => $attempts,
                ]);

                if ($attempts >= self::MAX_ATTEMPT_COUNT) {
                    break;
                }

                sleep($this->retryDelaySeconds(null, $attempts));
            }
        }

        return [
            'success' => false,
            'provider' => null,
            'model' => null,
            'text' => null,
            'error_message' => $lastErrorMessage,
            'is_fallback' => true,
            'attempts' => $attempts,
        ];
    }

    /**
     * 使用するAI会社の一覧
     *
     * APIキーが未設定の会社は自動スキップする。
     */
    private function providers(): array
    {
        $providers = [
            [
                'name' => 'google',
                'type' => 'google',
                'api_key' => config('services.google_ai.api_key'),
                'model' => config('services.google_ai.model', 'gemini-2.5-flash'),
            ],
            [
                'name' => 'openrouter',
                'type' => 'openai_compatible',
                'api_key' => config('services.openrouter.api_key'),
                'model' => config('services.openrouter.model', 'deepseek/deepseek-r1-0528:free'),
                'endpoint' => 'https://openrouter.ai/api/v1/chat/completions',
                'extra_headers' => [
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name', 'MokuMoku Match'),
                ],
            ],
            [
                'name' => 'groq',
                'type' => 'openai_compatible',
                'api_key' => config('services.groq.api_key'),
                'model' => config('services.groq.model', 'llama-3.1-8b-instant'),
                'endpoint' => 'https://api.groq.com/openai/v1/chat/completions',
                'extra_headers' => [],
            ],
        ];

        return collect($providers)
            ->filter(fn (array $provider) => filled($provider['api_key'] ?? null))
            ->values()
            ->all();
    }

    /**
     * Google Gemini APIへ送信
     */
    private function postGoogle(string $model, string $apiKey, string $prompt, float $temperature): HttpResponse
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        return Http::timeout(60)
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
    }

    /**
     * OpenRouter / Groq などOpenAI互換APIへ送信
     */
    private function postOpenAiCompatible(
        string $endpoint,
        string $apiKey,
        string $model,
        string $prompt,
        float $temperature,
        array $extraHeaders = []
    ): HttpResponse {
        return Http::timeout(60)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
                ...$extraHeaders,
            ])
            ->post($endpoint, [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'あなたは日本語で回答する文章トレーニングの先生です。必ずJSONだけで返してください。',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => $temperature,
                'response_format' => [
                    'type' => 'json_object',
                ],
            ]);
    }

    /**
     * AIレスポンスから本文を取り出す
     */
    private function extractText(HttpResponse $response, string $providerType): string
    {
        $text = match ($providerType) {
            'google' => $response->json('candidates.0.content.parts.0.text'),
            'openai_compatible' => $response->json('choices.0.message.content'),
            default => null,
        };

        if (! filled($text)) {
            Log::error('AIレスポンス本文取得失敗', [
                'provider_type' => $providerType,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('AIのレスポンス本文を取得できませんでした。');
        }

        return (string) $text;
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
            Log::error('AI JSON変換失敗', [
                'text' => $text,
            ]);

            throw new RuntimeException('AIのレスポンスをJSONとして解析できませんでした。');
        }

        return $decoded;
    }

    /**
     * 採点結果をDB保存しやすい形へ整える
     */
    private function formatScoreResult(
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
            'good_point' => $this->normalizeAiText((string) ($score['good_point'] ?? '')),
            'improvement_point' => $this->normalizeAiText((string) ($score['improvement_point'] ?? '')),
            'next_task' => $this->normalizeAiText((string) ($score['next_task'] ?? '')),
            'ai_response' => $score,

            // ここが今回追加した履歴
            'ai_provider' => $aiProvider,
            'ai_model' => $aiModel,
            'ai_status' => $aiStatus,
            'ai_error_message' => $aiErrorMessage,
            'is_fallback' => $isFallback,
            'ai_attempts' => $aiAttempts,
        ];
    }

    /**
     * Laravel簡易採点
     *
     * AIが全て使えなかった場合でも、サービスを止めないための最低限の採点。
     */
    private function localScore(string $text): array
    {
        $plainText = trim(strip_tags($text));
        $length = mb_strlen($plainText);

        $readabilityScore = $this->clampScore(
            10
            + (str_contains($plainText, '。') ? 5 : 0)
            + (str_contains($plainText, "\n") ? 5 : 0)
            + ($length >= 100 ? 5 : 0)
        );

        $specificityScore = $this->clampScore(
            8
            + (preg_match('/[0-9０-９]/u', $plainText) ? 5 : 0)
            + (preg_match('/なぜ|理由|ため|だから|ので/u', $plainText) ? 6 : 0)
            + ($length >= 150 ? 6 : 0)
        );

        $structureScore = $this->clampScore(
            8
            + (preg_match('/まず|次に|最後|結論|理由|改善/u', $plainText) ? 7 : 0)
            + (substr_count($plainText, "\n") >= 2 ? 5 : 0)
            + ($length >= 120 ? 5 : 0)
        );

        $expressionScore = $this->clampScore(
            10
            + ($length >= 80 ? 5 : 0)
            + ($length >= 200 ? 5 : 0)
            + (preg_match('/感じ|学び|気づき|改善|挑戦/u', $plainText) ? 5 : 0)
        );

        $totalScore = min(100, $readabilityScore + $specificityScore + $structureScore + $expressionScore);

        return [
            'total_score' => $totalScore,
            'readability_score' => $readabilityScore,
            'specificity_score' => $specificityScore,
            'structure_score' => $structureScore,
            'expression_score' => $expressionScore,
            'good_point' => '入力内容をもとに、Laravel側の簡易採点で評価しました。継続して記録できている点は良いです。',
            'improvement_point' => 'AI採点が利用できなかったため、細かな表現面の評価は簡易的です。次回は、理由・具体例・学びをもう少し入れるとより良くなります。',
            'next_task' => '次回は「出来事 → 感情 → 理由 → 学び → 次の行動」の順番で書いてみましょう。',
        ];
    }

    private function clampScore(int $score): int
    {
        return max(0, min(25, $score));
    }

    /**
     * Laravel簡易問題生成
     */
    private function localQuestion(string $type): array
    {
        return match ($type) {
            'summary' => [
                'question_title' => '150文字以内で要約してください',
                'question_body' => '在宅で仕事や学習を続けると、自分のペースで進められる一方で、集中力が切れやすくなることがあります。特に一人で作業していると、誰にも見られていない安心感から、つい休憩が長くなったり、後回しにしたりすることがあります。そのため、作業時間を決めたり、誰かと一緒に作業する環境を作ったりすることが、継続の助けになります。',
            ],
            'verbalization' => [
                'question_title' => '最近うまく説明できなかったことについて',
                'question_body' => "以下の4つに分けて書いてください。\n1. 何があったか\n2. そのとき何を感じたか\n3. なぜそう感じたか\n4. 次にどう改善したいか",
            ],
            'abstraction' => [
                'question_title' => '3つの具体例に共通する本質を考えてください',
                'question_body' => "例1：作業を後回しにしてしまった。\n例2：説明が長くなり、相手に伝わりにくかった。\n例3：目標はあったが、具体的な行動に落とし込めなかった。\n質問：これらに共通する問題を一言で抽象化し、その理由も説明してください。",
            ],
            'concretization' => [
                'question_title' => '抽象的な言葉を具体的な行動に落とし込んでください',
                'question_body' => "テーマ：継続力を高める\n回答条件：\n1. 具体的な場面\n2. 実際に取る行動\n3. 相手や自分にどう影響するか\n4. 明日からできる小さな行動",
            ],
            default => [
                'question_title' => '本日のトレーニング問題',
                'question_body' => '今日の学び、気づき、改善したいことを具体的に書いてください。',
            ],
        };
    }

    /**
     * リトライ待機秒数
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
            default => 3,
        };
    }

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

    private function responseErrorMessage(HttpResponse $response): string
    {
        return (string) (
            $response->json('error.message')
            ?? $response->json('message')
            ?? $response->body()
            ?? 'AIリクエストに失敗しました。'
        );
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
     * AIが返した文章の余計な空白・インデントを整える
     */
    private function normalizeAiText(string $text): string
    {
        $lines = preg_split("/\r\n|\n|\r/", $text);

        if (! is_array($lines)) {
            return trim($text);
        }

        $lines = array_map(fn (string $line) => trim($line), $lines);

        return trim(implode(PHP_EOL, $lines));
    }

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
