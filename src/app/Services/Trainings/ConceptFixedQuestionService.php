<?php

namespace App\Services\Trainings;

use App\Models\UserConceptTraining;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ConceptFixedQuestionService
{
    public function makeQuestion(string $difficultyLabel, array $usedPairKeys = []): array
    {
        $questions = collect($this->questionsByDifficulty($difficultyLabel))
            ->reject(function (array $question) use ($usedPairKeys) {
                return in_array(
                    $this->makeNormalizedPairKey($question['theme_a'], $question['theme_b']),
                    $usedPairKeys,
                    true
                );
            });

        if ($questions->isEmpty()) {
            $questions = collect($this->questionsByDifficulty($difficultyLabel));
        }

        $question = $questions->random();

        return [
            'question_title' => '具体・抽象トレーニング',
            'theme_a' => $question['theme_a'],
            'theme_b' => $question['theme_b'],
            'difficulty_label' => $difficultyLabel,
            'question_body' => "{$question['theme_a']}と{$question['theme_b']}は、抽象化してみるとどのような意味で一緒だと言えますか？",
            'model_answer' => $question['model_answer'],
            'alternative_answer' => $question['alternative_answer'],
            'answer_point' => $question['answer_point'],
            'normalized_pair_key' => $this->makeNormalizedPairKey($question['theme_a'], $question['theme_b']),
            'ai_provider' => 'laravel',
            'ai_model' => 'fixed-question',
            'ai_status' => 'fallback',
            'is_fallback' => true,
            'ai_attempts' => 0,
        ];
    }

    public function makeNormalizedPairKey(string $themeA, string $themeB): string
    {
        $themes = collect([$themeA, $themeB])
            ->map(fn (string $theme) => trim(mb_convert_kana($theme, 'asKV')))
            ->sort()
            ->values();

        return $themes->implode('|');
    }

    public function simpleScore(UserConceptTraining $training, string $answerBody): array
    {
        $length = mb_strlen($answerBody);

        $hasPattern = str_contains($answerBody, '一緒')
            || str_contains($answerBody, '共通')
            || str_contains($answerBody, '意味')
            || str_contains($answerBody, '見方')
            || str_contains($answerBody, '役割')
            || str_contains($answerBody, '目的');

        $commonPointScore = $hasPattern ? 21 : 16;
        $essenceScore = $length >= 40 ? 21 : 16;
        $viewpointScore = ($length >= 80 || str_contains($answerBody, '別')) ? 21 : 17;
        $explanationScore = $length >= 60 ? 21 : 16;

        $totalScore = min(100, $commonPointScore + $essenceScore + $viewpointScore + $explanationScore);

        return [
            'total_score' => $totalScore,
            'common_point_score' => $commonPointScore,
            'essence_score' => $essenceScore,
            'viewpoint_score' => $viewpointScore,
            'explanation_score' => $explanationScore,
            'good_point' => '2つのテーマに共通する意味を自分の言葉で考えられています。',
            'improvement_point' => '表面的な似ている点だけでなく、役割・目的・構造まで踏み込むとさらに良くなります。',
            'next_task' => '次回は「何のために存在しているか」という目的の視点でも考えてみましょう。',
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
                'theme_a' => '本棚',
                'theme_b' => 'クローゼット',
                'model_answer' => '本棚とクローゼットは抽象化してみると、物を整理して保管する場所という意味で一緒だ。',
                'alternative_answer' => '本棚とクローゼットは、必要なものを探しやすくする仕組みという見方でも一緒だ。',
                'answer_point' => '役割・機能・目的の共通点を考える',
            ],
            [
                'theme_a' => 'カフェ',
                'theme_b' => '公園',
                'model_answer' => 'カフェと公園は抽象化してみると、人が少し休んだり気分を切り替えたりする場所という意味で一緒だ。',
                'alternative_answer' => 'カフェと公園は、自分の時間を整えるための余白という見方でも一緒だ。',
                'answer_point' => '場所が与える役割を考える',
            ],
            [
                'theme_a' => '靴',
                'theme_b' => 'タイヤ',
                'model_answer' => '靴とタイヤは抽象化してみると、移動を支え、地面との摩擦から本体を守るものという意味で一緒だ。',
                'alternative_answer' => '靴とタイヤは、前に進むための接点という見方でも一緒だ。',
                'answer_point' => '移動を支える機能を見る',
            ],
            [
                'theme_a' => '冷蔵庫',
                'theme_b' => '図書館',
                'model_answer' => '冷蔵庫と図書館は抽象化してみると、大切なものを保存し、必要な時に取り出せる場所という意味で一緒だ。',
                'alternative_answer' => '冷蔵庫と図書館は、価値あるものを管理する倉庫という見方でも一緒だ。',
                'answer_point' => '保存と取り出しの共通点を見る',
            ],
            [
                'theme_a' => '傘',
                'theme_b' => '日焼け止め',
                'model_answer' => '傘と日焼け止めは抽象化してみると、外から受ける刺激やダメージをやわらげるものという意味で一緒だ。',
                'alternative_answer' => '傘と日焼け止めは、自分を守るための小さな防御という見方でも一緒だ。',
                'answer_point' => '守る対象と役割を見る',
            ],
        ];
    }

    private function intermediateQuestions(): array
    {
        return [
            [
                'theme_a' => '到着',
                'theme_b' => '出発',
                'model_answer' => '到着と出発は抽象化してみると、どちらも状態が切り替わる節目という意味で一緒だ。',
                'alternative_answer' => '到着と出発は、次の行動を始めるための区切りという見方でも一緒だ。',
                'answer_point' => '反対語を変化の節目として見る',
            ],
            [
                'theme_a' => '断る',
                'theme_b' => '引き受ける',
                'model_answer' => '断ると引き受けるは抽象化してみると、自分の時間や責任の使い方を選ぶ行為という意味で一緒だ。',
                'alternative_answer' => '断ると引き受けるは、自分の境界線を決める判断という見方でも一緒だ。',
                'answer_point' => '選択や責任の視点で見る',
            ],
            [
                'theme_a' => '節約',
                'theme_b' => '浪費',
                'model_answer' => '節約と浪費は抽象化してみると、どちらもお金や資源との付き合い方を表す行動という意味で一緒だ。',
                'alternative_answer' => '節約と浪費は、未来と今のどちらを優先するかの選択という見方でも一緒だ。',
                'answer_point' => '反対の行動を選択として見る',
            ],
            [
                'theme_a' => '攻め',
                'theme_b' => '守り',
                'model_answer' => '攻めと守りは抽象化してみると、目的を達成するために状況へ働きかける戦略という意味で一緒だ。',
                'alternative_answer' => '攻めと守りは、リスクとの向き合い方の違いという見方でも一緒だ。',
                'answer_point' => '戦略やリスクの視点で見る',
            ],
            [
                'theme_a' => '独学',
                'theme_b' => '集団学習',
                'model_answer' => '独学と集団学習は抽象化してみると、知識や力を身につけるための学び方という意味で一緒だ。',
                'alternative_answer' => '独学と集団学習は、自分に合う環境を選ぶ学習設計という見方でも一緒だ。',
                'answer_point' => '方法ではなく目的を見る',
            ],
        ];
    }

    private function advancedQuestions(): array
    {
        return [
            [
                'theme_a' => '未来',
                'theme_b' => '白紙',
                'model_answer' => '未来と白紙は抽象化してみると、まだ何も決まっておらず、これから描き込める可能性という意味で一緒だ。',
                'alternative_answer' => '未来と白紙は、不安でもあり自由でもある余白という見方でも一緒だ。',
                'answer_point' => '可能性や余白として見る',
            ],
            [
                'theme_a' => '筋肉',
                'theme_b' => '信用',
                'model_answer' => '筋肉と信用は抽象化してみると、短期間では作れず、日々の積み重ねで強くなるものという意味で一緒だ。',
                'alternative_answer' => '筋肉と信用は、使わないと弱くなり、鍛えるほど支えになる資産という見方でも一緒だ。',
                'answer_point' => '積み重ねで育つものとして見る',
            ],
            [
                'theme_a' => '自由',
                'theme_b' => '責任',
                'model_answer' => '自由と責任は抽象化してみると、自分で選び、その結果を受け止める力という意味で一緒だ。',
                'alternative_answer' => '自由と責任は、自分の人生を自分で扱うための両輪という見方でも一緒だ。',
                'answer_point' => '選択と結果の関係を見る',
            ],
            [
                'theme_a' => '努力',
                'theme_b' => '貯金',
                'model_answer' => '努力と貯金は抽象化してみると、すぐに大きな成果は出なくても、少しずつ未来の自分を助けるものという意味で一緒だ。',
                'alternative_answer' => '努力と貯金は、見えないところで積み上がる安心材料という見方でも一緒だ。',
                'answer_point' => '未来への蓄積として見る',
            ],
            [
                'theme_a' => '価値観',
                'theme_b' => 'レンズ',
                'model_answer' => '価値観とレンズは抽象化してみると、同じものでも見え方や受け取り方を変えるものという意味で一緒だ。',
                'alternative_answer' => '価値観とレンズは、世界の解釈を決めるフィルターという見方でも一緒だ。',
                'answer_point' => '見え方を変える働きを見る',
            ],
        ];
    }
}
