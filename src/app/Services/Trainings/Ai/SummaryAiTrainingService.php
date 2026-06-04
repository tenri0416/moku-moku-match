<?php

namespace App\Services\Trainings\Ai;

class SummaryAiTrainingService extends BaseQuestionAiTrainingService
{
    public function type(): string
    {
        return 'summary';
    }

    protected function typeLabel(): string
    {
        return '要約力トレーニング';
    }

    protected function scoreLabels(): array
    {
        return [
            'score_1' => '重要点の抽出',
            'score_2' => '簡潔さ',
            'score_3' => '正確性',
            'score_4' => 'わかりやすさ',
        ];
    }

    protected function questionPrompt(int|string $difficulty): string
    {
        return <<<PROMPT
要約力を鍛える問題を1つ作成してください。

条件：
- 日本語
- 元文章は250〜400文字
- 回答は100〜150文字で要約させる
- テーマは仕事、学習、生活改善のどれか
- 難易度：{$difficulty}

出力ルール：
- JSONのみ
- Markdown禁止
- question_bodyは長すぎない
- model_answerは100〜150文字以内
- answer_pointは50文字以内
- 余計な説明は禁止

{
  "question_title": "150文字以内で要約してください",
  "question_body": "要約対象の文章",
  "model_answer": "模範解答例",
  "answer_point": "重要な内容を落とさず短くまとめる"
}
PROMPT;
    }

    protected function localQuestion(): array
    {
        return [
            'question_title' => '150文字以内で要約してください',
            'question_body' => '在宅で仕事や学習を続けると、自分のペースで進められる一方で、集中力が切れやすくなることがあります。特に一人で作業していると、休憩が長くなったり、後回しにしたりすることがあります。そのため、作業時間を決めたり、誰かと一緒に作業する環境を作ったりすることが、継続の助けになります。',
            'model_answer' => '在宅作業は自由に進められる反面、集中力が切れやすいため、時間管理や誰かと作業する環境づくりが継続の助けになる。',
            'answer_point' => '原因と対策を落とさず簡潔にまとめる',
        ];
    }
}
