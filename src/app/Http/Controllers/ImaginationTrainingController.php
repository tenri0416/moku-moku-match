<?php

namespace App\Http\Controllers;

use App\Models\UserImaginationTraining;
use App\Models\UserTrainingPointHistory;
use App\Services\Trainings\ImaginationAiTrainingService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ImaginationTrainingController extends Controller
{
    public function __construct(
        private readonly ImaginationAiTrainingService $imaginationAiTrainingService,
    ) {
    }

    public function create(): View|RedirectResponse
    {
        $user = Auth::user();
        $today = CarbonImmutable::today();

        $training = UserImaginationTraining::query()
            ->where('user_id', $user->id)
            ->whereDate('training_date', $today)
            ->first();

        if ($training && filled($training->answer_body)) {
            return redirect()->route('trainings.imagination.show', $training);
        }

        if (! $training) {
            $difficultyLabel = $this->resolveDifficultyLabel($user->id);
            $usedKeys = $this->usedQuestionKeys($user->id);

            $question = $this->imaginationAiTrainingService->generateQuestion($difficultyLabel, $usedKeys);

            $training = UserImaginationTraining::create([
                'user_id' => $user->id,
                'training_date' => $today->toDateString(),
                ...$question,
            ]);
        }

        return view('trainings.imagination.create', [
            'training' => $training,
            ...$this->trainingCommonViewData(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $today = CarbonImmutable::today();

        $validated = $request->validate([
            'training_id' => ['required', 'integer', 'exists:user_imagination_trainings,id'],
            'answer_body' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'answer_body.required' => '回答を入力してください。',
            'answer_body.min' => '回答は10文字以上で入力してください。',
            'answer_body.max' => '回答は1000文字以内で入力してください。',
        ]);

        $training = UserImaginationTraining::query()
            ->where('id', $validated['training_id'])
            ->where('user_id', $user->id)
            ->whereDate('training_date', $today)
            ->firstOrFail();

        if (filled($training->answer_body)) {
            return redirect()
                ->route('trainings.imagination.show', $training)
                ->with('info', '本日の想像力トレーニングはすでに回答済みです。');
        }

        return DB::transaction(function () use ($training, $validated, $user) {
            $score = $this->imaginationAiTrainingService->score($training, $validated['answer_body']);
            $earnedPoints = $this->calculateEarnedPoints((int) $score['total_score']);

            $training->update([
                'answer_body' => $validated['answer_body'],
                'total_score' => $score['total_score'],
                'imagination_score' => $score['imagination_score'],
                'reason_score' => $score['reason_score'],
                'perspective_score' => $score['perspective_score'],
                'expression_score' => $score['expression_score'],
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

            $this->savePointHistory(
                userId: $user->id,
                trainingId: $training->id,
                earnedPoints: $earnedPoints,
                totalScore: (int) $score['total_score'],
                earnedOn: $training->training_date->toDateString()
            );

            return redirect()
                ->route('trainings.imagination.show', $training)
                ->with('success', '想像力トレーニングの回答を保存しました。')
                ->with('show_score_modal', true);
        });
    }

    public function show(UserImaginationTraining $training): View
    {
        abort_unless($training->user_id === Auth::id(), 403);

        return view('trainings.imagination.show', [
            'training' => $training,
            ...$this->trainingCommonViewData(),
        ]);
    }

    private function resolveDifficultyLabel(int $userId): string
    {
        $totalPoints = (int) UserTrainingPointHistory::query()
            ->where('user_id', $userId)
            ->sum('points');

        return match (true) {
            $totalPoints >= 800 => '上級',
            $totalPoints >= 300 => '中級',
            default => '初級',
        };
    }

    private function usedQuestionKeys(int $userId): array
    {
        return UserImaginationTraining::query()
            ->where('user_id', $userId)
            ->pluck('normalized_question_key')
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

    private function savePointHistory(int $userId, int $trainingId, int $earnedPoints, int $totalScore, string $earnedOn): void
    {
        if (! Schema::hasTable('user_training_point_histories')) {
            return;
        }

        UserTrainingPointHistory::create([
            'user_id' => $userId,
            'training_type' => 'imagination',
            'training_id' => $trainingId,
            'point_type' => 'training',
            'points' => $earnedPoints,
            'earned_on' => $earnedOn,
            'note' => "想像力トレーニング実施",
        ]);
    }

    private function trainingCommonViewData(): array
    {
        $userId = Auth::id();

        $myTotalPoints = (int) UserTrainingPointHistory::query()
            ->where('user_id', $userId)
            ->sum('points');

        return [
            'myTotalPoints' => $myTotalPoints,
            'continuousDays' => $this->calculateContinuousDays($userId),
            'monthlyRank' => $this->calculateMyMonthlyRank($userId),
        ];
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
            ->map(fn ($date) => \Carbon\Carbon::parse($date)->toDateString())
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

        $index = $rankings->search(fn ($ranking) => (int) $ranking->user_id === (int) $userId);

        return $index === false ? null : $index + 1;
    }
}
