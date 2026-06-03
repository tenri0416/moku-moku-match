<?php

namespace App\Services\Trainings\Ai;

class AbstractionAiTrainingService extends BaseQuestionAiTrainingService
{
    public function type(): string
    {
        return 'abstraction';
    }

    protected function typeLabel(): string
    {
        return '抽象化力トレーニング';
    }

    protected function scoreLabels(): array
    {
        return [
            'score_1' => '共通点の発見',
            'score_2' => '本質の捉え方',
            'score_3' => '理由の説明',
            'score_4' => '言葉の簡潔さ',
        ];
    }

    protected function questionPrompt(int|string $difficulty): string
    {
        return <<<PROMPT
抽象化力を鍛える問題を1つ作成してください。

条件：
- 日本語
- 3つの具体例を出す
- 共通点や本質を考えさせる
- テーマは仕事、学習、改善、説明のどれか
- 難易度：{$difficulty}

出力ルール：
- JSONのみ
- Markdown禁止
- question_bodyは長すぎない

{
  "question_title": "3つの具体例に共通する本質を考えてください",
  "question_body": "例1：...\\n例2：...\\n例3：...\\n質問：共通する問題を一言で抽象化してください。"
}
PROMPT;
    }

    protected function localQuestion(): array
    {
        return [
            'question_title' => '3つの具体例に共通する本質を考えてください',
            'question_body' => "例1：作業を後回しにしてしまった。\n例2：説明が長くなり伝わりにくかった。\n例3：目標はあるが行動に落とし込めなかった。\n質問：これらに共通する問題を一言で抽象化し、理由も説明してください。",
        ];
    }
}
