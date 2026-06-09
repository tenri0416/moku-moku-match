<?php

namespace App\Services\Trainings;

use App\Models\UserImaginationTraining;

class ImaginationFixedQuestionService
{
    public function makeQuestion(string $difficultyLabel, array $usedKeys = []): array
    {
        $questions = collect($this->questionsByDifficulty($difficultyLabel))
            ->reject(fn (array $question) => in_array($this->makeNormalizedQuestionKey($question['question_body']), $usedKeys, true));

        if ($questions->isEmpty()) {
            $questions = collect($this->questionsByDifficulty($difficultyLabel));
        }

        $question = $questions->random();
        $normalizedQuestionKey = $this->makeNormalizedQuestionKey($question['question_body']);

        return [
            'question_title' => '想像力トレーニング',
            'question_type' => $question['question_type'],
            'difficulty_label' => $difficultyLabel,
            'question_body' => $question['question_body'],
            'normalized_question_key' => $normalizedQuestionKey,
            'model_answer' => $question['model_answer'],
            'alternative_answer' => $question['alternative_answer'],
            'answer_point' => $question['answer_point'],
            'ai_provider' => 'laravel',
            'ai_model' => 'fixed-question',
            'ai_status' => 'fallback',
            'is_fallback' => true,
            'ai_attempts' => 0,
        ];
    }

    public function makeNormalizedQuestionKey(string $questionBody): string
    {
        $normalized = trim(mb_convert_kana($questionBody, 'asKV'));
        $normalized = preg_replace('/[ \t　\r\n]+/u', '', $normalized);

        return hash('sha256', $normalized);
    }

    public function simpleScore(UserImaginationTraining $training, string $answerBody): array
    {
        $length = mb_strlen($answerBody);

        $hasReason = str_contains($answerBody, '理由')
            || str_contains($answerBody, 'だから')
            || str_contains($answerBody, 'ため')
            || str_contains($answerBody, 'と思');

        $hasPerspective = str_contains($answerBody, '相手')
            || str_contains($answerBody, '気持ち')
            || str_contains($answerBody, '可能性')
            || str_contains($answerBody, '別');

        $imaginationScore = $length >= 40 ? 21 : 16;
        $reasonScore = $hasReason ? 21 : 16;
        $perspectiveScore = $hasPerspective ? 21 : 17;
        $expressionScore = $length >= 60 ? 21 : 16;

        $totalScore = min(100, $imaginationScore + $reasonScore + $perspectiveScore + $expressionScore);

        return [
            'total_score' => $totalScore,
            'imagination_score' => $imaginationScore,
            'reason_score' => $reasonScore,
            'perspective_score' => $perspectiveScore,
            'expression_score' => $expressionScore,
            'good_point' => '提示された状況から、自分なりに想像して文章にできています。',
            'improvement_point' => '想像した内容に理由や別の可能性を加えると、さらに広がりが出ます。',
            'next_task' => '次回は、相手の気持ち・背景・その後の展開をセットで考えてみましょう。',
            'ai_provider' => 'laravel',
            'ai_model' => 'simple-scoring',
            'ai_status' => 'fallback',
            'is_fallback' => true,
            'ai_attempts' => 0,
        ];
    }

    private function questionsByDifficulty(string $difficultyLabel): array
    {
        return match ($difficultyLabel) {
            '上級' => $this->advancedQuestions(),
            '中級' => $this->intermediateQuestions(),
            default => $this->beginnerQuestions(),
        };
    }

    private function beginnerQuestions(): array
    {
        return [
            [
                'question_type' => '状況想像型',
                'question_body' => '雨の日に、駅の前で立ち止まっている人がいます。この人は何を考えていると思いますか？',
                'model_answer' => '私は、この人は傘を忘れてしまい、どう帰るか迷っている状況だと想像しました。雨を見ながら立ち止まっているので、次の行動を決められずにいるのだと思います。',
                'alternative_answer' => '別の見方をすると、誰かを待っている可能性もあります。雨の中でも動かないのは、大切な約束があるからかもしれません。',
                'answer_point' => '状況・気持ち・理由をセットで考える',
            ],
            [
                'question_type' => '感情想像型',
                'question_body' => 'カフェで、誰かがスマホを見て小さく笑いました。どんなことが起きたと思いますか？',
                'model_answer' => '私は、友人や家族から楽しいメッセージが届いたのだと想像しました。声に出さずに小さく笑うのは、心の中で嬉しさを感じているからだと思います。',
                'alternative_answer' => '別の見方をすると、昔の写真や思い出を見て、懐かしい気持ちになっている可能性もあります。',
                'answer_point' => '行動から感情を想像する',
            ],
            [
                'question_type' => '状況想像型',
                'question_body' => '公園のベンチに、忘れられた本があります。どんな背景があると思いますか？',
                'model_answer' => '私は、誰かが読書に夢中になった後、急いで帰ることになり本を置き忘れたのだと想像しました。ベンチに本があることで、その人の時間の流れが少し見える気がします。',
                'alternative_answer' => '別の見方をすると、誰かに見つけてほしくて意図的に置いた本という可能性もあります。',
                'answer_point' => '物から持ち主の行動を想像する',
            ],
        ];
    }

    private function intermediateQuestions(): array
    {
        return [
            [
                'question_type' => '感情想像型',
                'question_body' => '友人が約束を急に断りました。相手の事情を3つ想像してください。',
                'model_answer' => '私は、体調が悪くなった、家族の用事が入った、気持ちに余裕がなくなったという3つの可能性を想像しました。急な断りには、相手にも言いにくい事情があるかもしれません。',
                'alternative_answer' => '別の見方をすると、約束が嫌になったのではなく、相手なりに迷惑をかけないため早めに伝えた可能性もあります。',
                'answer_point' => '複数の可能性を考える',
            ],
            [
                'question_type' => '別視点想像型',
                'question_body' => '同僚が会議中にあまり発言しませんでした。理由を想像してください。',
                'model_answer' => '私は、その同僚は意見がないのではなく、まだ考えを整理している途中だったのだと想像しました。発言しないことは、消極的ではなく慎重さの表れかもしれません。',
                'alternative_answer' => '別の見方をすると、会議の空気を見ながら、今は発言するタイミングではないと判断していた可能性もあります。',
                'answer_point' => '沈黙の理由を一つに決めない',
            ],
            [
                'question_type' => '感情想像型',
                'question_body' => 'いつも明るい人が、今日は静かにしています。どんな可能性がありますか？',
                'model_answer' => '私は、疲れている、悩みがある、考え事をしているという可能性を想像しました。明るい人でも、いつも同じ状態でいられるわけではないと思います。',
                'alternative_answer' => '別の見方をすると、何か大事なことに集中していて、あえて静かにしている可能性もあります。',
                'answer_point' => '普段との差から背景を考える',
            ],
        ];
    }

    private function advancedQuestions(): array
    {
        return [
            [
                'question_type' => '未来想像型',
                'question_body' => '10年後の自分が、今の自分に手紙を書くとしたら何を伝えると思いますか？',
                'model_answer' => '私は、10年後の自分は「焦らなくても大丈夫。今の小さな積み重ねがちゃんと未来につながっている」と伝えると思います。今の不安も、未来から見ると大切な途中経過かもしれません。',
                'alternative_answer' => '別の見方をすると、「もっと早く挑戦してよかった」と背中を押すような手紙になる可能性もあります。',
                'answer_point' => '未来の視点から今を見る',
            ],
            [
                'question_type' => 'もしも想像型',
                'question_body' => 'もし、失敗に価値がつく世界だったら、人の行動はどう変わると思いますか？',
                'model_answer' => '私は、人はもっと挑戦しやすくなると思います。失敗が恥ずかしいものではなく価値として見られるなら、完璧を待つより試して学ぶ行動が増えるはずです。',
                'alternative_answer' => '別の見方をすると、失敗そのものを目的にしてしまい、丁寧に準備する力が弱くなる可能性もあります。',
                'answer_point' => '良い面と悪い面の両方を想像する',
            ],
            [
                'question_type' => '価値観想像型',
                'question_body' => '「自由すぎる社会」では、人はどんな悩みを持つと思いますか？',
                'model_answer' => '私は、選択肢が多すぎて何を選べばよいか分からなくなる悩みを持つと思います。自由は魅力的ですが、基準がないと不安や迷いも大きくなるからです。',
                'alternative_answer' => '別の見方をすると、自由の中で自分の責任をどこまで持つべきか悩む人も増えると思います。',
                'answer_point' => '自由の裏側にある不安を見る',
            ],
        ];
    }
}
