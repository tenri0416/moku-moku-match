<?php

namespace App\Services\Trainings\Ai;

class ConcretizationAiTrainingService extends BaseQuestionAiTrainingService
{
    public function type(): string
    {
        return 'concretization';
    }

    protected function typeLabel(): string
    {
        return '具体化力トレーニング';
    }

    protected function scoreLabels(): array
    {
        return [
            'score_1' => '具体例のわかりやすさ',
            'score_2' => '行動への落とし込み',
            'score_3' => '相手目線',
            'score_4' => '実行しやすさ',
        ];
    }

    protected function questionPrompt(int|string $difficulty): string
    {
        return <<<PROMPT
具体化力を鍛える問題を1つ作成してください。

条件：
- 日本語
- 抽象的なテーマを1つ出す
- 実際の行動に落とし込ませる
- テーマは仕事、学習、説明、改善、継続のどれか
- 難易度：{$difficulty}

出力ルール：
- JSONのみ
- Markdown禁止
- question_bodyは長すぎない

{
  "question_title": "抽象的な言葉を具体的な行動に落とし込んでください",
  "question_body": "テーマ：継続力を高める\\n1. 具体的な場面\\n2. 実際に取る行動\\n3. 明日からできること"
}
PROMPT;
    }

    protected function localQuestion(): array
    {
        return [
            'question_title' => '抽象的な言葉を具体的な行動に落とし込んでください',
            'question_body' => "テーマ：継続力を高める\n1. 具体的な場面\n2. 実際に取る行動\n3. 自分にどう影響するか\n4. 明日からできる小さな行動",
        ];
    }
}
