<?php

namespace App\Services\Trainings;

use App\Models\UserConceptTraining;
use App\Services\Ai\AiProviderManager;
use Illuminate\Support\Facades\Log;
use Throwable;

class ConceptAiTrainingService
{
    public function __construct(
        private readonly ConceptFixedQuestionService $fixedQuestionService,
    ) {
    }

    public function generateQuestion(string $difficultyLabel, array $usedPairKeys = []): array
    {
        $prompt = $this->buildQuestionPrompt($difficultyLabel, $usedPairKeys);

        try {
            $result = $this->callAi($prompt, '具体・抽象トレーニング問題生成');

            $data = $this->decodeJson($result['content'] ?? '');

            $this->validateQuestionData($data);

            $normalizedPairKey = $this->fixedQuestionService->makeNormalizedPairKey(
                $data['theme_a'],
                $data['theme_b']
            );

            if (in_array($normalizedPairKey, $usedPairKeys, true)) {
                throw new \RuntimeException('AIが過去出題済みのテーマを返しました。');
            }

            return [
                'question_title' => $data['question_title'] ?? '具体・抽象トレーニング',
                'theme_a' => $data['theme_a'],
                'theme_b' => $data['theme_b'],
                'normalized_pair_key' => $normalizedPairKey,
                'difficulty_label' => $data['difficulty_label'] ?? $difficultyLabel,
                'question_body' => $data['question_body'],
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
            Log::warning('具体・抽象トレーニング問題生成に失敗したため固定問題へ切り替えました。', [
                'message' => $e->getMessage(),
                'difficulty_label' => $difficultyLabel,
            ]);

            return $this->fixedQuestionService->makeQuestion($difficultyLabel, $usedPairKeys);
        }
    }

    public function score(UserConceptTraining $training, string $answerBody): array
    {
        $prompt = $this->buildScoringPrompt($training, $answerBody);

        try {
            $result = $this->callAi($prompt, '具体・抽象トレーニング採点');

            $data = $this->decodeJson($result['content'] ?? '');

            $this->validateScoreData($data);

            return [
                'total_score' => $this->normalizeScore($data['total_score']),
                'common_point_score' => $this->normalizeScore($data['common_point_score']),
                'essence_score' => $this->normalizeScore($data['essence_score']),
                'viewpoint_score' => $this->normalizeScore($data['viewpoint_score']),
                'explanation_score' => $this->normalizeScore($data['explanation_score']),
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
            Log::warning('具体・抽象トレーニング採点に失敗したためLaravel簡易採点へ切り替えました。', [
                'message' => $e->getMessage(),
                'training_id' => $training->id,
            ]);

            return $this->fixedQuestionService->simpleScore($training, $answerBody);
        }
    }

    private function buildQuestionPrompt(string $difficultyLabel, array $usedPairKeys): string
    {
        $usedPairsText = empty($usedPairKeys)
            ? 'なし'
            : implode("\n", array_slice($usedPairKeys, 0, 80));

        return <<<PROMPT
あなたは、具体・抽象トレーニングの問題を作成するAIです。

ユーザーは、2つのテーマ語を見て、その共通する本質を短文で回答します。

以下の条件で、問題を1問だけ作成してください。

【難易度】
{$difficultyLabel}

【難易度の意味】
初級：
身近なもの、日用品、道具、場所、食べ物など。
役割・機能・用途の共通点を見つけやすい問題にする。

中級：
反対概念、学び、仕事、お金、社会の仕組み、行動の違いなど。
見方を切り替える練習になる問題にする。

上級：
感情、価値観、時間、人生観、比喩的な言葉など。
抽象度が高く、深く考えられる問題にする。

【過去に出題済みの組み合わせ】
{$usedPairsText}

【禁止ルール】
・過去に出題済みの組み合わせと重複しない
・AとBを入れ替えただけの重複も禁止
・同じテーマ語を短期間に繰り返さない
・theme_a と theme_b は同じ意味にしない
・公序良俗に反するテーマは禁止
・性的、暴力的、差別的なテーマは禁止
・実在の政治家、宗教、病気、個人属性に強く関わるテーマは避ける

【回答の型】
基本の型：
AとBは抽象化してみると、〇〇という意味で一緒だ。

別解の型：
AとBは△△という見方でも一緒だ。

【出力ルール】
・JSONのみ返す
・Markdownは禁止
・余計な説明は禁止
・model_answer は80〜150文字程度
・alternative_answer は80〜150文字程度
・answer_point は50文字以内

【JSON形式】
{
  "question_title": "具体・抽象トレーニング",
  "theme_a": "テーマA",
  "theme_b": "テーマB",
  "difficulty_label": "{$difficultyLabel}",
  "question_body": "テーマAとテーマBは、抽象化してみるとどのような意味で一緒だと言えますか？",
  "model_answer": "模範解答例",
  "alternative_answer": "別解例",
  "answer_point": "回答時に意識するポイント"
}
PROMPT;
    }

    private function buildScoringPrompt(UserConceptTraining $training, string $answerBody): string
    {
        return <<<PROMPT
あなたは、具体・抽象トレーニングの回答を採点するAIです。

以下の問題とユーザー回答を採点してください。

【難易度】
{$training->difficulty_label}

【テーマ】
{$training->theme_a} × {$training->theme_b}

【問題文】
{$training->question_body}

【模範解答例】
{$training->model_answer}

【別解例】
{$training->alternative_answer}

【回答時のポイント】
{$training->answer_point}

【ユーザー回答】
{$answerBody}

【評価基準】
・AとBの表面的な共通点だけでなく、役割・目的・構造に踏み込めているか
・回答が短くても意味が伝わるか
・無理やりな共通点ではなく、納得感があるか
・別の見方や比喩として成立しているか
・初級では分かりやすさを重視する
・中級では視点の切り替えを重視する
・上級では抽象度と深さを重視する

【出力ルール】
・JSONのみ返す
・Markdownは禁止
・余計な説明は禁止
・各スコアは0〜25点
・total_score は0〜100点
・good_point、improvement_point、next_task は短く分かりやすくする

【JSON形式】
{
  "total_score": 85,
  "common_point_score": 22,
  "essence_score": 21,
  "viewpoint_score": 20,
  "explanation_score": 22,
  "good_point": "2つの共通する役割を分かりやすく捉えられています。",
  "improvement_point": "もう少し目的や構造まで踏み込むと、より抽象度が高くなります。",
  "next_task": "次回は、機能だけでなく、その奥にある目的も考えてみましょう。"
}
PROMPT;
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
        foreach ([
            'theme_a',
            'theme_b',
            'difficulty_label',
            'question_body',
            'model_answer',
            'alternative_answer',
            'answer_point',
        ] as $key) {
            if (blank($data[$key] ?? null)) {
                throw new \RuntimeException("AI問題生成レスポンスに {$key} がありません。");
            }
        }

        if (($data['theme_a'] ?? '') === ($data['theme_b'] ?? '')) {
            throw new \RuntimeException('theme_a と theme_b が同じです。');
        }
    }

    private function validateScoreData(array $data): void
    {
        foreach ([
            'total_score',
            'common_point_score',
            'essence_score',
            'viewpoint_score',
            'explanation_score',
            'good_point',
            'improvement_point',
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

    /**
 * 既存の AiProviderManager に接続するための薄いアダプタです。
 */
private function callAi(string $prompt, string $actionName): array
{
    if (! class_exists(AiProviderManager::class)) {
        throw new \RuntimeException('AiProviderManager が見つかりません。');
    }

    $manager = app(AiProviderManager::class);

    if (! method_exists($manager, 'requestJson')) {
        throw new \RuntimeException('AiProviderManager::requestJson が見つかりません。');
    }

    $result = $manager->requestJson(
        prompt: $prompt,
        temperature: 0.2,
        actionName: $actionName,
    );

    if (($result['success'] ?? false) !== true) {
        throw new \RuntimeException($result['error_message'] ?? 'AIリクエストに失敗しました。');
    }

    return [
        'content' => $result['text'] ?? '',
        'provider' => $result['provider'] ?? null,
        'model' => $result['model'] ?? null,
        'attempts' => $result['attempts'] ?? 1,
        'is_fallback' => $result['is_fallback'] ?? false,
    ];
}
}
