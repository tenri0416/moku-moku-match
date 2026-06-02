<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserAbstractionTraining;
use App\Models\UserChallengeTraining;
use App\Models\UserConcretizationTraining;
use App\Models\UserDiaryTraining;
use App\Models\UserSummaryTraining;
use App\Models\UserTrainingPointHistory;
use App\Models\UserVerbalizationTraining;
use App\Services\GoogleAiScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class TrainingController extends Controller
{
    public function index(Request $request): View
    {
        $userId = auth()->id();

        $trainings = collect()
            ->merge($this->mapTrainings(UserDiaryTraining::where('user_id', $userId)->get(), UserDiaryTraining::TYPE))
            ->merge($this->mapTrainings(UserChallengeTraining::where('user_id', $userId)->get(), UserChallengeTraining::TYPE))
            ->merge($this->mapTrainings(UserSummaryTraining::where('user_id', $userId)->get(), UserSummaryTraining::TYPE))
            ->merge($this->mapTrainings(UserVerbalizationTraining::where('user_id', $userId)->get(), UserVerbalizationTraining::TYPE))
            ->merge($this->mapTrainings(UserAbstractionTraining::where('user_id', $userId)->get(), UserAbstractionTraining::TYPE))
            ->merge($this->mapTrainings(UserConcretizationTraining::where('user_id', $userId)->get(), UserConcretizationTraining::TYPE))
            ->when($request->type, fn (Collection $items) => $items->where('type', $request->type))
            ->sortByDesc('training_date')
            ->values();

        $todayStatuses = [
            'diary' => UserDiaryTraining::where('user_id', $userId)->whereDate('training_date', today())->exists(),
            'challenge' => UserChallengeTraining::where('user_id', $userId)->whereDate('training_date', today())->exists(),
            'summary' => UserSummaryTraining::where('user_id', $userId)->whereDate('training_date', today())->whereNotNull('answer_body')->exists(),
            'verbalization' => UserVerbalizationTraining::where('user_id', $userId)->whereDate('training_date', today())->whereNotNull('answer_body')->exists(),
            'abstraction' => UserAbstractionTraining::where('user_id', $userId)->whereDate('training_date', today())->whereNotNull('answer_body')->exists(),
            'concretization' => UserConcretizationTraining::where('user_id', $userId)->whereDate('training_date', today())->whereNotNull('answer_body')->exists(),
        ];

        $myTotalPoints = UserTrainingPointHistory::where('user_id', $userId)->sum('points');
        $myTrainingDifficulty = $this->calculateTrainingDifficulty((int) $myTotalPoints);

        return view('trainings.index', compact('trainings', 'todayStatuses', 'myTotalPoints', 'myTrainingDifficulty'));
    }

    public function createDiary(): View|RedirectResponse
    {
        if ($this->alreadyDoneToday(UserDiaryTraining::class)) {
            return redirect()
                ->route('trainings.index')
                ->with('error', '本日の日記トレーニングは実施済みです。');
        }

        $myTotalPoints = $this->myTotalTrainingPoints();
        $myTrainingDifficulty = $this->calculateTrainingDifficulty($myTotalPoints);

        return view('trainings.diary-create', compact('myTotalPoints', 'myTrainingDifficulty'));
    }

    public function storeDiary(Request $request, GoogleAiScoringService $scoringService): RedirectResponse
    {
        $validated = $request->validate([
            'training_date' => ['required', 'date'],
            'diary_body' => ['required', 'string', 'max:5000'],
        ], [
            'training_date.required' => '日付を入力してください。',
            'diary_body.required' => '日記を入力してください。',
            'diary_body.max' => '日記は5000文字以内で入力してください。',
        ]);

        if ($this->alreadyDoneOnDate(UserDiaryTraining::class, $validated['training_date'])) {
            return back()
                ->withInput()
                ->with('error', 'この日の日記トレーニングはすでに実施済みです。');
        }

        try {
            $score = $scoringService->scoreDiary(
                diaryBody: $validated['diary_body'],
                difficulty: $this->currentTrainingDifficulty()
            );
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return DB::transaction(function () use ($validated, $score) {
            $points = $this->calculatePoints(UserDiaryTraining::TYPE, (int) $score['total_score']);

            $training = UserDiaryTraining::create([
                'user_id' => auth()->id(),
                'training_date' => $validated['training_date'],
                'diary_body' => $validated['diary_body'],
                'earned_points' => $points,
                ...$score,
            ]);

            $this->storePoint($training, UserDiaryTraining::TYPE, $points, $validated['training_date']);

            return redirect()
                ->route('trainings.show', ['type' => UserDiaryTraining::TYPE, 'id' => $training->id])
                ->with('success', '日記トレーニングを保存しました。');
        });
    }

    public function createChallenge(): View|RedirectResponse
    {
        if ($this->alreadyDoneToday(UserChallengeTraining::class)) {
            return redirect()
                ->route('trainings.index')
                ->with('error', '本日の今日のチャレンジは実施済みです。');
        }

        $myTotalPoints = $this->myTotalTrainingPoints();
        $myTrainingDifficulty = $this->calculateTrainingDifficulty($myTotalPoints);

        return view('trainings.challenge-create', compact('myTotalPoints', 'myTrainingDifficulty'));
    }

    public function storeChallenge(Request $request, GoogleAiScoringService $scoringService): RedirectResponse
    {
        $validated = $request->validate([
            'training_date' => ['required', 'date'],
            'challenged_thing' => ['required', 'string', 'max:3000'],
            'completed_thing' => ['required', 'string', 'max:3000'],
            'difficult_thing' => ['required', 'string', 'max:3000'],
            'next_improvement' => ['required', 'string', 'max:3000'],
        ], [
            'training_date.required' => '日付を入力してください。',
            'challenged_thing.required' => '今日チャレンジしたことを入力してください。',
            'completed_thing.required' => 'できたことを入力してください。',
            'difficult_thing.required' => '難しかったことを入力してください。',
            'next_improvement.required' => '次に改善したいことを入力してください。',
        ]);

        if ($this->alreadyDoneOnDate(UserChallengeTraining::class, $validated['training_date'])) {
            return back()
                ->withInput()
                ->with('error', 'この日の今日のチャレンジはすでに実施済みです。');
        }

        try {
            $score = $scoringService->scoreChallenge(
                data: $validated,
                difficulty: $this->currentTrainingDifficulty()
            );
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return DB::transaction(function () use ($validated, $score) {
            $points = $this->calculatePoints(UserChallengeTraining::TYPE, (int) $score['total_score']);

            $training = UserChallengeTraining::create([
                'user_id' => auth()->id(),
                'earned_points' => $points,
                ...$validated,
                ...$score,
            ]);

            $this->storePoint($training, UserChallengeTraining::TYPE, $points, $validated['training_date']);

            return redirect()
                ->route('trainings.show', ['type' => UserChallengeTraining::TYPE, 'id' => $training->id])
                ->with('success', '今日のチャレンジを保存しました。');
        });
    }

    public function createSummary(GoogleAiScoringService $scoringService): View|RedirectResponse
    {
        return $this->createAiTraining(UserSummaryTraining::TYPE, UserSummaryTraining::class, $scoringService);
    }

    public function storeSummary(Request $request, UserSummaryTraining $training, GoogleAiScoringService $scoringService): RedirectResponse
    {
        return $this->storeAiTraining($request, $training, UserSummaryTraining::TYPE, $scoringService);
    }

    public function createVerbalization(GoogleAiScoringService $scoringService): View|RedirectResponse
    {
        return $this->createAiTraining(UserVerbalizationTraining::TYPE, UserVerbalizationTraining::class, $scoringService);
    }

    public function storeVerbalization(Request $request, UserVerbalizationTraining $training, GoogleAiScoringService $scoringService): RedirectResponse
    {
        return $this->storeAiTraining($request, $training, UserVerbalizationTraining::TYPE, $scoringService);
    }

    public function createAbstraction(GoogleAiScoringService $scoringService): View|RedirectResponse
    {
        return $this->createAiTraining(UserAbstractionTraining::TYPE, UserAbstractionTraining::class, $scoringService);
    }

    public function storeAbstraction(Request $request, UserAbstractionTraining $training, GoogleAiScoringService $scoringService): RedirectResponse
    {
        return $this->storeAiTraining($request, $training, UserAbstractionTraining::TYPE, $scoringService);
    }

    public function createConcretization(GoogleAiScoringService $scoringService): View|RedirectResponse
    {
        return $this->createAiTraining(UserConcretizationTraining::TYPE, UserConcretizationTraining::class, $scoringService);
    }

    public function storeConcretization(Request $request, UserConcretizationTraining $training, GoogleAiScoringService $scoringService): RedirectResponse
    {
        return $this->storeAiTraining($request, $training, UserConcretizationTraining::TYPE, $scoringService);
    }

    public function show(string $type, int $id): View
    {
        $training = $this->findTraining($type, $id);

        abort_unless($training->user_id === auth()->id(), 403);

        return view('trainings.show', compact('training', 'type'));
    }

    public function ranking(): View
    {
        $monthlyRankings = UserTrainingPointHistory::query()
            ->select('user_id')
            ->selectRaw('SUM(points) as total_points')
            ->selectRaw('COUNT(*) as training_count')
            ->whereBetween('earned_on', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->with('user.profile')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->limit(20)
            ->get();

        $totalRankings = UserTrainingPointHistory::query()
            ->select('user_id')
            ->selectRaw('SUM(points) as total_points')
            ->selectRaw('COUNT(*) as training_count')
            ->with('user.profile')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->limit(20)
            ->get();

        return view('trainings.ranking', compact('monthlyRankings', 'totalRankings'));
    }

    private function createAiTraining(string $type, string $modelClass, GoogleAiScoringService $scoringService): View|RedirectResponse
    {
        $today = now()->toDateString();

        $training = $modelClass::query()
            ->where('user_id', auth()->id())
            ->whereDate('training_date', $today)
            ->first();

        if ($training && filled($training->answer_body)) {
            return redirect()
                ->route('trainings.show', ['type' => $type, 'id' => $training->id])
                ->with('error', '本日の' . $training->typeLabel() . 'は実施済みです。');
        }

        if (! $training) {
            try {
                $question = $scoringService->generateAiTrainingQuestion(
                    type: $type,
                    difficulty: $this->currentTrainingDifficulty()
                );
            } catch (Throwable $e) {
                report($e);

                return redirect()
                    ->route('trainings.index')
                    ->with('error', $e->getMessage());
            }

            $training = $modelClass::create([
                'user_id' => auth()->id(),
                'training_date' => $today,
                'question_title' => $question['question_title'],
                'question_body' => $question['question_body'],
            ]);
        }

        return view('trainings.ai-create', [
            'training' => $training,
            'type' => $type,
            'typeLabel' => $training->typeLabel(),
            'scoreLabels' => $training->scoreLabels(),
            'storeRoute' => route($this->storeRouteNameByType($type), $training),
            'myTotalPoints' => $this->myTotalTrainingPoints(),
            'myTrainingDifficulty' => $this->currentTrainingDifficulty(),
        ]);
    }

    private function storeAiTraining(
        Request $request,
        mixed $training,
        string $type,
        GoogleAiScoringService $scoringService
    ): RedirectResponse {
        abort_unless($training->user_id === auth()->id(), 403);

        if (filled($training->answer_body)) {
            return redirect()
                ->route('trainings.show', ['type' => $type, 'id' => $training->id])
                ->with('error', '本日の' . $training->typeLabel() . 'は実施済みです。');
        }

        $validated = $request->validate([
            'answer_body' => ['required', 'string', 'max:5000'],
        ], [
            'answer_body.required' => '回答を入力してください。',
            'answer_body.max' => '回答は5000文字以内で入力してください。',
        ]);

        try {
            $score = $scoringService->scoreAiTraining(
                type: $type,
                questionTitle: $training->question_title,
                questionBody: $training->question_body,
                answerBody: $validated['answer_body'],
                difficulty: $this->currentTrainingDifficulty()
            );
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return DB::transaction(function () use ($training, $validated, $score, $type) {
            $points = $this->calculatePoints($type, (int) $score['total_score']);

            $training->update([
                'answer_body' => $validated['answer_body'],
                'earned_points' => $points,
                ...$score,
            ]);

            $this->storePoint($training->fresh(), $type, $points, $training->training_date->toDateString());

            return redirect()
                ->route('trainings.show', ['type' => $type, 'id' => $training->id])
                ->with('success', $training->typeLabel() . 'を保存しました。');
        });
    }


    /**
     * 現在ログイン中ユーザーの総獲得ポイントを取得する
     */
    private function myTotalTrainingPoints(): int
    {
        return (int) UserTrainingPointHistory::where('user_id', auth()->id())->sum('points');
    }

    /**
     * 現在ログイン中ユーザーのトレーニング難易度を取得する
     */
    private function currentTrainingDifficulty(): int|string
    {
        return $this->calculateTrainingDifficulty($this->myTotalTrainingPoints());
    }

    /**
     * 総獲得ポイントに応じてトレーニング難易度を計算する
     */
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


    /**
     * 採点結果に応じて獲得ポイントを計算する
     */
    private function calculatePoints(string $type, int $totalScore): int
{
    return match (true) {
        $totalScore === 100 => 10,
        $totalScore >= 90 => 9,
        $totalScore >= 80 => 8,
        $totalScore >= 70 => 7,
        $totalScore >= 60 => 6,
        default => 1,
    };
}

    private function storePoint(mixed $training, string $type, int $points, string $earnedOn): void
    {
        UserTrainingPointHistory::create([
            'user_id' => auth()->id(),
            'training_type' => $type,
            'training_id' => $training->id,
            'point_type' => 'training',
            'points' => $points,
            'earned_on' => $earnedOn,
            'note' => $training->typeLabel() . '実施',
        ]);
    }

    private function alreadyDoneToday(string $modelClass): bool
    {
        return $this->alreadyDoneOnDate($modelClass, now()->toDateString());
    }

    private function alreadyDoneOnDate(string $modelClass, string $date): bool
    {
        return $modelClass::query()
            ->where('user_id', auth()->id())
            ->whereDate('training_date', $date)
            ->exists();
    }

    private function mapTrainings(Collection $trainings, string $type): Collection
    {
        return $trainings->map(function ($training) use ($type) {
            return [
                'id' => $training->id,
                'type' => $type,
                'type_label' => $training->typeLabel(),
                'training_date' => $training->training_date,
                'title' => $training->question_title
                    ?? $training->diary_body
                    ?? $training->challenged_thing
                    ?? '-',
                'total_score' => $training->total_score,
                'earned_points' => $training->earned_points,
                'is_answered' => $training->isAnswered(),
            ];
        });
    }

    private function findTraining(string $type, int $id): mixed
    {
        return match ($type) {
            'diary' => UserDiaryTraining::findOrFail($id),
            'challenge' => UserChallengeTraining::findOrFail($id),
            'summary' => UserSummaryTraining::findOrFail($id),
            'verbalization' => UserVerbalizationTraining::findOrFail($id),
            'abstraction' => UserAbstractionTraining::findOrFail($id),
            'concretization' => UserConcretizationTraining::findOrFail($id),
            default => abort(404),
        };
    }

    private function storeRouteNameByType(string $type): string
    {
        return match ($type) {
            'summary' => 'trainings.summary.store',
            'verbalization' => 'trainings.verbalization.store',
            'abstraction' => 'trainings.abstraction.store',
            'concretization' => 'trainings.concretization.store',
            default => throw new RuntimeException('不正なトレーニング種別です。'),
        };
    }
}
