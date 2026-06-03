<?php

namespace App\Services\Trainings\Ai;

class VerbalizationAiTrainingService extends BaseQuestionAiTrainingService
{
    public function type(): string
    {
        return 'verbalization';
    }

    protected function typeLabel(): string
    {
        return '言語化力トレーニング';
    }

    protected function scoreLabels(): array
    {
        return [
            'score_1' => '考えの明確さ',
            'score_2' => '理由の具体性',
            'score_3' => '構成',
            'score_4' => '伝わりやすさ',
        ];
    }

    protected function questionPrompt(int|string $difficulty): string
    {
        return <<<PROMPT
言語化力を鍛える問題を1つ作成してください。

条件：
- 日本語
- 仕事、学習、人間関係、説明力、改善力のテーマ
- 回答者が自分の経験を書きやすい
- 難易度：{$difficulty}

出力ルール：
- JSONのみ
- Markdown禁止
- question_bodyは長すぎない

{
  "question_title": "最近うまく説明できなかった経験について",
  "question_body": "1. 何があったか\\n2. 何を感じたか\\n3. なぜそう感じたか\\n4. 次にどう改善するか"
}
PROMPT;
    }

    protected function localQuestion(): array
    {
        return [
            'question_title' => '最近うまく説明できなかった経験について',
            'question_body' => "以下の4つに分けて書いてください。\n1. 何があったか\n2. そのとき何を感じたか\n3. なぜそう感じたか\n4. 次にどう改善したいか",
        ];
    }
}
