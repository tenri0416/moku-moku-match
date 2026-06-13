<?php

namespace App\Services\Trainings;

use App\Models\User;
use App\Models\VocabularyPrintTest;
use App\Models\VocabularyPrintTestQuestion;
use App\Models\VocabularyWord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class VocabularyPrintTestBuilderService
{
    public const TYPE_MEANING = '意味問題';
    public const TYPE_EXAMPLE = '例文問題';
    public const TYPE_USAGE = '使い方説明問題';
    public const TYPE_SYNONYM = '類義語問題';
    public const TYPE_ANTONYM = '反対語問題';
    public const TYPE_CHOICE = '選択問題';
    public const TYPE_DIFFERENCE = '言葉の違い説明問題';
    public const TYPE_READING = '読み問題';
    public const TYPE_KANJI = '漢字書き取り問題';

    public const EXISTING_TYPES = [
        self::TYPE_MEANING,
        self::TYPE_EXAMPLE,
        self::TYPE_USAGE,
    ];

    public const AI_TYPES = [
        self::TYPE_SYNONYM,
        self::TYPE_ANTONYM,
        self::TYPE_CHOICE,
        self::TYPE_DIFFERENCE,
        self::TYPE_READING,
        self::TYPE_KANJI,
    ];

    public const ALL_TYPES = [
        self::TYPE_MEANING,
        self::TYPE_EXAMPLE,
        self::TYPE_USAGE,
        self::TYPE_SYNONYM,
        self::TYPE_ANTONYM,
        self::TYPE_CHOICE,
        self::TYPE_DIFFERENCE,
        self::TYPE_READING,
        self::TYPE_KANJI,
    ];

    public function __construct(
        private readonly VocabularyPrintQuestionAiService $aiService,
    ) {
    }

    public function create(User $user, array $input): VocabularyPrintTest
    {
        $questionCount = (int) $input['question_count'];
        $questionTypes = array_values($input['question_types']);
        $timeLimitMinutes = (int) $input['time_limit_minutes'];

        $words = $this->selectWords($user, $input, $questionCount);

        if ($words->count() < $questionCount) {
            throw new RuntimeException("出題できる単語が不足しています。現在出題可能な単語は{$words->count()}件です。");
        }

        $assignedTypes = $this->assignQuestionTypes($questionCount, $questionTypes);
        $points = $this->distributePoints($questionCount);

        return DB::transaction(function () use ($user, $input, $questionCount, $timeLimitMinutes, $questionTypes, $words, $assignedTypes, $points) {
            $printTest = VocabularyPrintTest::create([
                'user_id' => $user->id,
                'title' => 'ボキャブラリー印刷テスト',
                'question_count' => $questionCount,
                'time_limit_minutes' => $timeLimitMinutes,
                'target_filter' => $input['target_filter'],
                'category' => $input['category'] ?? null,
                'importance' => $input['importance'] ?? null,
                'question_types_json' => $questionTypes,
                'total_score' => 100,
                'status' => 'generating',
                'generated_at' => now(),
            ]);

            foreach ($words->take($questionCount)->values() as $index => $word) {
                $questionNumber = $index + 1;
                $questionType = $assignedTypes[$index];

                $question = $this->makeQuestion($word, $questionType);

                VocabularyPrintTestQuestion::create([
                    'vocabulary_print_test_id' => $printTest->id,
                    'vocabulary_word_id' => $word->id,
                    'question_number' => $questionNumber,
                    'question_type' => $questionType,
                    'question_body' => $question['question_body'],
                    'point' => $points[$index],
                    'answer_text' => $question['answer_text'],
                    'explanation_text' => $question['explanation_text'] ?? null,
                    'choices_json' => $question['choices_json'] ?? null,
                    'correct_choice' => $question['correct_choice'] ?? null,
                    'scoring_rule_json' => $question['scoring_rule_json'],
                ]);
            }

            $printTest->update([
                'status' => 'completed',
                'error_message' => null,
            ]);

            return $printTest->fresh('questions.vocabularyWord');
        });
    }

    private function selectWords(User $user, array $input, int $questionCount): Collection
    {
        $query = VocabularyWord::query()
            ->where('user_id', $user->id);

        match ($input['target_filter']) {
            'review_target' => $query->where('is_review_target', true),
            'weak' => $query->where('review_status', VocabularyWord::STATUS_WEAK),
            'not_reviewed' => $query->where('review_status', VocabularyWord::STATUS_NOT_REVIEWED),
            'all' => null,
            default => $query->where('is_review_target', true),
        };

        if (! empty($input['category'])) {
            $query->where('category', $input['category']);
        }

        if (! empty($input['importance'])) {
            $query->where('importance', (int) $input['importance']);
        }

        return $query
            ->orderByRaw(
                'CASE review_status WHEN ? THEN 0 WHEN ? THEN 1 WHEN ? THEN 2 ELSE 3 END',
                [
                    VocabularyWord::STATUS_WEAK,
                    VocabularyWord::STATUS_NOT_REVIEWED,
                    VocabularyWord::STATUS_REVIEWING,
                ]
            )
            ->orderByDesc('importance')
            ->orderBy('last_reviewed_at')
            ->inRandomOrder()
            ->limit(max($questionCount, 50))
            ->get();
    }

    private function assignQuestionTypes(int $questionCount, array $selectedTypes): array
    {
        $selectedTypes = array_values(array_intersect($selectedTypes, self::ALL_TYPES));

        if ($selectedTypes === []) {
            $selectedTypes = [
                self::TYPE_MEANING,
                self::TYPE_EXAMPLE,
                self::TYPE_USAGE,
            ];
        }

        if ($questionCount === 5) {
            return $this->roundRobinRandom($selectedTypes, $questionCount);
        }

        $existingTypes = array_values(array_intersect($selectedTypes, self::EXISTING_TYPES));
        $aiTypes = array_values(array_intersect($selectedTypes, self::AI_TYPES));

        if ($existingTypes !== [] && $aiTypes !== []) {
            $existingCount = (int) ceil($questionCount * 0.6);
            $aiCount = $questionCount - $existingCount;

            return array_merge(
                $this->roundRobin($existingTypes, $existingCount),
                $this->roundRobin($aiTypes, $aiCount),
            );
        }

        return $this->roundRobin($selectedTypes, $questionCount);
    }

    private function roundRobin(array $types, int $count): array
    {
        $result = [];

        for ($i = 0; $i < $count; $i++) {
            $result[] = $types[$i % count($types)];
        }

        return $result;
    }

    private function roundRobinRandom(array $types, int $count): array
    {
        $result = [];

        for ($i = 0; $i < $count; $i++) {
            $result[] = $types[array_rand($types)];
        }

        return $result;
    }

    private function distributePoints(int $questionCount): array
    {
        $base = intdiv(100, $questionCount);
        $remainder = 100 % $questionCount;

        $points = [];

        for ($i = 0; $i < $questionCount; $i++) {
            $points[] = $base + ($i < $remainder ? 1 : 0);
        }

        return $points;
    }

    private function makeQuestion(VocabularyWord $word, string $questionType): array
    {
        if (in_array($questionType, self::AI_TYPES, true)) {
            return $this->aiService->generate($word, $questionType);
        }

        return match ($questionType) {
            self::TYPE_EXAMPLE => [
                'question_body' => "「{$word->word}」を使って、自然な例文を作ってください。",
                'answer_text' => $word->example_sentence,
                'explanation_text' => "登録された意味：{$word->meaning}",
                'choices_json' => null,
                'correct_choice' => null,
                'scoring_rule_json' => [
                    ['label' => '言葉の使い方が正しい', 'point' => 4],
                    ['label' => '文として自然', 'point' => 3],
                    ['label' => '意味が伝わる', 'point' => 2],
                    ['label' => '誤字脱字が少ない', 'point' => 1],
                ],
            ],
            self::TYPE_USAGE => [
                'question_body' => "「{$word->word}」は、どのような場面で使える言葉か説明してください。",
                'answer_text' => $word->meaning,
                'explanation_text' => "例文：{$word->example_sentence}",
                'choices_json' => null,
                'correct_choice' => null,
                'scoring_rule_json' => [
                    ['label' => '使える場面が合っている', 'point' => 4],
                    ['label' => '理由を説明できている', 'point' => 3],
                    ['label' => '具体例がある', 'point' => 2],
                    ['label' => '文章が分かりやすい', 'point' => 1],
                ],
            ],
            default => [
                'question_body' => "「{$word->word}」の意味を、自分の言葉で説明してください。",
                'answer_text' => $word->meaning,
                'explanation_text' => "例文：{$word->example_sentence}",
                'choices_json' => null,
                'correct_choice' => null,
                'scoring_rule_json' => [
                    ['label' => '意味が大きく合っている', 'point' => 4],
                    ['label' => '自分の言葉で説明できている', 'point' => 3],
                    ['label' => '具体的な説明がある', 'point' => 2],
                    ['label' => '文章が読みやすい', 'point' => 1],
                ],
            ],
        };
    }
}
