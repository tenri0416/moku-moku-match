<?php

namespace App\Http\Controllers;

use App\Models\UserConceptTraining;
use App\Services\Trainings\ConceptAiTrainingService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use App\Models\UserTrainingPointHistory;


class ConceptTrainingController extends Controller
{
    public function __construct(
        private readonly ConceptAiTrainingService $conceptAiTrainingService,
    ) {}

    public function create(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        $today = CarbonImmutable::today();

        $training = UserConceptTraining::query()
            ->where('user_id', $user->id)
            ->whereDate('training_date', $today)
            ->first();

        if ($training && $training->answer_body) {
            return redirect()->route('trainings.concept.show', $training);
        }

        if (! $training) {
            $difficultyLabel = $this->resolveDifficultyLabel($user->id);
            $usedPairKeys = $this->usedPairKeys($user->id);

            $question = $this->conceptAiTrainingService->generateQuestion($difficultyLabel, $usedPairKeys);

            $training = UserConceptTraining::create([
                'user_id' => $user->id,
                'training_date' => $today,
                ...$question,
            ]);
        }

        return view('trainings.concept.create', [
            'training' => $training,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $today = CarbonImmutable::today();

        $validated = $request->validate([
            'training_id' => ['required', 'integer', 'exists:user_concept_trainings,id'],
            'answer_body' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'answer_body.required' => '回答を入力してください。',
            'answer_body.min' => '回答は10文字以上で入力してください。',
            'answer_body.max' => '回答は1000文字以内で入力してください。',
        ]);

        $training = UserConceptTraining::query()
            ->where('id', $validated['training_id'])
            ->where('user_id', $user->id)
            ->whereDate('training_date', $today)
            ->firstOrFail();

        if ($training->answer_body) {
            return redirect()->route('trainings.concept.show', $training)
                ->with('info', '本日の具体・抽象トレーニングはすでに回答済みです。');
        }

        $score = $this->conceptAiTrainingService->score($training, $validated['answer_body']);

        $earnedPoints = $this->calculateEarnedPoints((int) $score['total_score']);

        $training->update([
            'answer_body' => $validated['answer_body'],
            'total_score' => $score['total_score'],
            'common_point_score' => $score['common_point_score'],
            'essence_score' => $score['essence_score'],
            'viewpoint_score' => $score['viewpoint_score'],
            'explanation_score' => $score['explanation_score'],
            'good_point' => $score['good_point'],
            'improvement_point' => $score['improvement_point'],
            'next_task' => $score['next_task'],
            'earned_points' => $earnedPoints,
            'ai_provider' => $score['ai_provider'] ?? $training->ai_provider,
            'ai_model' => $score['ai_model'] ?? $training->ai_model,
            'ai_status' => $score['ai_status'] ?? $training->ai_status,
            'ai_error_message' => $score['ai_error_message'] ?? null,
            'is_fallback' => $score['is_fallback'] ?? $training->is_fallback,
            'ai_attempts' => $score['ai_attempts'] ?? $training->ai_attempts,
        ]);

        $this->savePointHistory($user->id, $training->id, $earnedPoints, (int) $score['total_score']);

        return redirect()->route('trainings.concept.show', $training)
            ->with('success', '具体・抽象トレーニングの回答を保存しました。');
    }

    public function show(UserConceptTraining $training): View
    {
        abort_unless($training->user_id === Auth::id(), 403);

        return view('trainings.concept.show', [
            'training' => $training,
            ...$this->trainingCommonViewData(),
        ]);
    }

    private function resolveDifficultyLabel(int $userId): string
    {
        $totalPoints = $this->totalTrainingPoints($userId);

        return match (true) {
            $totalPoints >= 800 => '上級',
            $totalPoints >= 300 => '中級',
            default => '初級',
        };
    }

    private function totalTrainingPoints(int $userId): int
    {
        if (! Schema::hasTable('user_training_point_histories')) {
            return 0;
        }

        $columns = Schema::getColumnListing('user_training_point_histories');

        $pointColumn = collect(['earned_points', 'point', 'points'])
            ->first(fn(string $column) => in_array($column, $columns, true));

        if (! $pointColumn) {
            return 0;
        }

        return (int) DB::table('user_training_point_histories')
            ->where('user_id', $userId)
            ->sum($pointColumn);
    }

    private function usedPairKeys(int $userId): array
    {
        return UserConceptTraining::query()
            ->where('user_id', $userId)
            ->pluck('normalized_pair_key')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function calculateEarnedPoints(int $totalScore): int
    {
        return match (true) {
            $totalScore >= 100 => 10,
            $totalScore >= 90 => 9,
            $totalScore >= 80 => 8,
            $totalScore >= 70 => 7,
            $totalScore >= 60 => 6,
            default => 1,
        };
    }

    private function savePointHistory(int $userId, int $trainingId, int $earnedPoints, int $totalScore): void
    {
        if (! Schema::hasTable('user_training_point_histories')) {
            return;
        }

        $columns = Schema::getColumnListing('user_training_point_histories');

        $data = [];

        if (in_array('user_id', $columns, true)) {
            $data['user_id'] = $userId;
        }

        if (in_array('training_type', $columns, true)) {
            $data['training_type'] = 'concept';
        }

        if (in_array('training_id', $columns, true)) {
            $data['training_id'] = $trainingId;
        }

        if (in_array('target_training_id', $columns, true)) {
            $data['target_training_id'] = $trainingId;
        }

        if (in_array('point_type', $columns, true)) {
            // 既存の集計処理が point_type = training を見ているため、必ず training にする
            $data['point_type'] = 'training';
        }

        if (in_array('points', $columns, true)) {
            $data['points'] = $earnedPoints;
        }

        if (in_array('earned_points', $columns, true)) {
            $data['earned_points'] = $earnedPoints;
        }

        if (in_array('point', $columns, true)) {
            $data['point'] = $earnedPoints;
        }

        if (in_array('earned_on', $columns, true)) {
            $data['earned_on'] = now()->toDateString();
        }

        if (in_array('earned_date', $columns, true)) {
            $data['earned_date'] = now()->toDateString();
        }

        if (in_array('score', $columns, true)) {
            $data['score'] = $totalScore;
        }

        if (in_array('note', $columns, true)) {
            $data['note'] = "具体・抽象トレーニング実施";
        }

        if (in_array('memo', $columns, true)) {
            $data['memo'] = "具体・抽象トレーニング {$totalScore}点";
        }

        if (in_array('created_at', $columns, true)) {
            $data['created_at'] = now();
        }

        if (in_array('updated_at', $columns, true)) {
            $data['updated_at'] = now();
        }

        if ($data !== []) {
            DB::table('user_training_point_histories')->insert($data);
        }
    }
    private function trainingCommonViewData(): array
    {
        $userId = Auth::id();

        $myTotalPoints = $this->myTotalTrainingPoints();
        $myTrainingDifficulty = $this->calculateTrainingDifficulty((int) $myTotalPoints);

        return [
            'myTotalPoints' => $myTotalPoints,
            'myTrainingDifficulty' => $myTrainingDifficulty,
            'continuousDays' => $this->calculateContinuousDays($userId),
            'monthlyRank' => $this->calculateMyMonthlyRank($userId),
            'nextGoalRemainingPoints' => $this->calculateNextGoalRemainingPoints((int) $myTotalPoints),
            'trainingProgressPercent' => $this->calculateTrainingProgressPercent((int) $myTotalPoints),
        ];
    }

    private function myTotalTrainingPoints(): int
    {
        return (int) UserTrainingPointHistory::query()
            ->where('user_id', Auth::id())
            ->sum('points');
    }

    private function calculateContinuousDays(int $userId): int
    {
        $earnedDates = UserTrainingPointHistory::query()
            ->where('user_id', $userId)
            ->where('point_type', 'training')
            ->select('earned_on')
            ->distinct()
            ->orderByDesc('earned_on')
            ->pluck('earned_on')
            ->map(fn($date) => \Carbon\Carbon::parse($date)->toDateString())
            ->flip();

        if ($earnedDates->isEmpty()) {
            return 0;
        }

        $continuousDays = 0;
        $targetDate = today();

        while ($earnedDates->has($targetDate->toDateString())) {
            $continuousDays++;
            $targetDate = $targetDate->copy()->subDay();
        }

        return $continuousDays;
    }

    private function calculateMyMonthlyRank(int $userId): ?int
    {
        $rankings = UserTrainingPointHistory::query()
            ->select('user_id')
            ->selectRaw('SUM(points) as total_points')
            ->where('point_type', 'training')
            ->whereBetween('earned_on', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->get()
            ->values();

        $index = $rankings->search(fn($ranking) => (int) $ranking->user_id === (int) $userId);

        return $index === false ? null : $index + 1;
    }

    private function calculateTrainingDifficulty(int $totalPoints): int|string
    {
        return match (true) {
            $totalPoints < 100 => 0,
            $totalPoints < 200 => 1,
            $totalPoints < 300 => 2,
            $totalPoints < 400 => 3,
            $totalPoints < 500 => 4,
            $totalPoints < 600 => 5,
            $totalPoints < 800 => 6,
            $totalPoints < 1000 => 7,
            $totalPoints < 1100 => 8,
            $totalPoints < 1200 => 9,
            $totalPoints < 1300 => 10,
            default => 'Max',
        };
    }

    private function calculateNextGoalRemainingPoints(int $totalPoints): int
    {
        $nextGoal = $this->nextTrainingGoalPoint($totalPoints);

        if ($nextGoal === null) {
            return 0;
        }

        return max(0, $nextGoal - $totalPoints);
    }

    private function calculateTrainingProgressPercent(int $totalPoints): int
    {
        $currentBase = $this->currentTrainingBasePoint($totalPoints);
        $nextGoal = $this->nextTrainingGoalPoint($totalPoints);

        if ($nextGoal === null) {
            return 100;
        }

        $range = $nextGoal - $currentBase;

        if ($range <= 0) {
            return 0;
        }

        $progress = (($totalPoints - $currentBase) / $range) * 100;

        return (int) max(0, min(100, round($progress)));
    }

    private function currentTrainingBasePoint(int $totalPoints): int
    {
        return match (true) {
            $totalPoints < 100 => 0,
            $totalPoints < 200 => 100,
            $totalPoints < 300 => 200,
            $totalPoints < 400 => 300,
            $totalPoints < 500 => 400,
            $totalPoints < 600 => 500,
            $totalPoints < 800 => 600,
            $totalPoints < 1000 => 800,
            $totalPoints < 1100 => 1000,
            $totalPoints < 1200 => 1100,
            $totalPoints < 1300 => 1200,
            default => 1300,
        };
    }

    private function nextTrainingGoalPoint(int $totalPoints): ?int
    {
        return match (true) {
            $totalPoints < 100 => 100,
            $totalPoints < 200 => 200,
            $totalPoints < 300 => 300,
            $totalPoints < 400 => 400,
            $totalPoints < 500 => 500,
            $totalPoints < 600 => 600,
            $totalPoints < 800 => 800,
            $totalPoints < 1000 => 1000,
            $totalPoints < 1100 => 1100,
            $totalPoints < 1200 => 1200,
            $totalPoints < 1300 => 1300,
            default => null,
        };
    }
}
