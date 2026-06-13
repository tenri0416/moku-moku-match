<?php

namespace App\Services\Trainings;

use App\Models\VocabularyWord;
use App\Services\Ai\AiProviderManager;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class VocabularyPrintQuestionAiService
{
    public function __construct(
        private readonly AiProviderManager $aiProviderManager,
    ) {
    }

    public function generate(VocabularyWord $word, string $questionType): array
    {
        $result = $this->aiProviderManager->requestJson(
            prompt: $this->buildPrompt($word, $questionType),
            temperature: 0.2,
            actionName: 'ボキャブラリー印刷テスト問題生成'
        );

        if (($result['success'] ?? false) !== true) {
            throw new RuntimeException($result['error_message'] ?? 'AI問題生成に失敗しました。');
        }

        $data = $this->decodeJson($result['text'] ?? '');

        $this->validate($data, $questionType);

        return [
            'question_body' => $data['question_body'],
            'answer_text' => $data['answer_text'],
            'explanation_text' => $data['explanation_text'] ?? null,
            'choices_json' => $data['choices'] ?? null,
            'correct_choice' => $data['correct_choice'] ?? null,
            'scoring_rule_json' => $data['scoring_rule'] ?? $this->defaultScoringRule($questionType),
        ];
    }

    private function buildPrompt(VocabularyWord $word, string $questionType): string
    {
        return <<<PROMPT
あなたは、日本語検定風のボキャブラリー印刷テストを作るAIです。

以下の登録済み単語をもとに、指定された問題形式の問題を1問だけ作成してください。

【対象の言葉】
{$word->word}

【登録された意味】
{$word->meaning}

【登録された例文】
{$word->example_sentence}

【問題形式】
{$questionType}

【作成ルール】
・ログインユーザーが登録した言葉を中心にする
・日本語学習として自然な問題にする
・問題文は紙で読みやすくする
・不自然な日本語、難しすぎる専門表現は避ける
・性的、暴力的、差別的、政治的、宗教的に強い内容は禁止
・JSONのみ返す
・Markdownは禁止
・JSONの前後に説明文を付けない
・必ず最後を } で閉じる

【問題形式別ルール】

類義語問題：
対象の言葉に意味が近い言葉を答えさせる問題にする。

反対語問題：
対象の言葉と反対に近い意味の言葉を答えさせる問題にする。

選択問題：
4択問題にする。
choices は ["A. ...", "B. ...", "C. ...", "D. ..."] の形式にする。
correct_choice は "A" / "B" / "C" / "D" のどれかにする。

言葉の違い説明問題：
対象の言葉と似ているが意味が異なる言葉を1つ選び、その違いを説明させる問題にする。

読み問題：
対象の言葉が漢字を含む場合、その読みを答えさせる問題にする。
対象の言葉がひらがな・カタカナ中心の場合でも、自然な読み問題にする。

漢字書き取り問題：
対象の言葉が漢字を含む場合、ひらがな表記を問題にして漢字を書かせる。
対象の言葉が漢字にしにくい場合は、登録された意味に近い自然な漢字語彙問題にする。

【JSON形式】
{
  "question_body": "問題文",
  "answer_text": "模範解答",
  "explanation_text": "解説",
  "choices": null,
  "correct_choice": null,
  "scoring_rule": [
    {"label": "意味・答えが正しい", "point": 5},
    {"label": "表記が自然", "point": 3},
    {"label": "読みやすく書けている", "point": 2}
  ]
}
PROMPT;
    }

    private function decodeJson(string $content): array
    {
        $content = trim($content);

        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $content = preg_replace('/^```json\s*/u', '', $content);
        $content = preg_replace('/^```\s*/u', '', $content);
        $content = preg_replace('/\s*```$/u', '', $content);

        $start = mb_strpos($content, '{');
        $end = mb_strrpos($content, '}');

        if ($start !== false && $end !== false && $end > $start) {
            $content = mb_substr($content, $start, $end - $start + 1);
        }

        $data = json_decode($content, true);

        if (! is_array($data)) {
            Log::warning('ボキャブラリー印刷テストAI問題のJSON解析に失敗しました。', [
                'json_error' => json_last_error_msg(),
                'raw_response' => mb_substr($content, 0, 2000),
            ]);

            throw new RuntimeException('AI問題生成レスポンスのJSON解析に失敗しました。');
        }

        return $data;
    }

    private function validate(array $data, string $questionType): void
    {
        foreach (['question_body', 'answer_text'] as $key) {
            if (blank($data[$key] ?? null)) {
                throw new RuntimeException("AI問題生成レスポンスに {$key} がありません。");
            }
        }

        if ($questionType === '選択問題') {
            if (empty($data['choices']) || ! is_array($data['choices'])) {
                throw new RuntimeException('選択問題の choices がありません。');
            }

            if (blank($data['correct_choice'] ?? null)) {
                throw new RuntimeException('選択問題の correct_choice がありません。');
            }
        }
    }

    private function defaultScoringRule(string $questionType): array
    {
        return match ($questionType) {
            '選択問題', '読み問題', '漢字書き取り問題' => [
                ['label' => '答えが正しい', 'point' => 8],
                ['label' => '表記が丁寧で読みやすい', 'point' => 2],
            ],
            '類義語問題', '反対語問題' => [
                ['label' => '意味の方向性が合っている', 'point' => 6],
                ['label' => '言葉として自然', 'point' => 3],
                ['label' => '表記が正しい', 'point' => 1],
            ],
            default => [
                ['label' => '2つの言葉の意味を理解している', 'point' => 4],
                ['label' => '違いを説明できている', 'point' => 3],
                ['label' => '具体例がある', 'point' => 2],
                ['label' => '文章が分かりやすい', 'point' => 1],
            ],
        };
    }
}
