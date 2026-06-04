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
- model_answerは120文字以内
- answer_pointは50文字以内
- 余計な説明は禁止

{
  "question_title": "抽象的な言葉を具体的な行動に落とし込んでください",
  "question_body": "テーマ：継続力を高める\\n1. 具体的な場面\\n2. 実際に取る行動\\n3. 明日からできること",
  "model_answer": "朝9時に机へ座り、最初の25分だけ学習する。終わったら進捗をメモし、翌日の最初の作業を1つ決める。",
  "answer_point": "いつ、どこで、何を、どのくらいやるかを書く"
}
PROMPT;
    }

    protected function localQuestion(): array
    {
        return [
            'question_title' => '抽象的な言葉を具体的な行動に落とし込んでください',
            'question_body' => "テーマ：継続力を高める\n1. 具体的な場面\n2. 実際に取る行動\n3. 自分にどう影響するか\n4. 明日からできる小さな行動",
            'model_answer' => '毎朝9時に机へ座り、25分だけ学習します。終わったら学んだ内容を1行で記録し、翌日にやることを1つ決めます。',
            'answer_point' => 'すぐ実行できる行動まで具体化する',
        ];
    }
}
