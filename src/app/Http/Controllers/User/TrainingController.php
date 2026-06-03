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
use App\Services\Trainings\Ai\TrainingAiScoringService;
use App\Support\ApiActionLogger;
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
        ApiActionLogger::info(
            methodName: 'TrainingController::index',
            message: 'トレーニング一覧ページにアクセス',
            params: [
                'user_id' => auth()->id(),
                'type' => $request->type,
            ]
        );

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
            'diary' => UserDiaryTraining::where('user_id', $userId)
                ->whereDate('training_date', today())
                ->exists(),

            'challenge' => UserChallengeTraining::where('user_id', $userId)
                ->whereDate('training_date', today())
                ->exists(),

            'summary' => UserSummaryTraining::where('user_id', $userId)
                ->whereDate('training_date', today())
                ->whereNotNull('answer_body')
                ->exists(),

            'verbalization' => UserVerbalizationTraining::where('user_id', $userId)
                ->whereDate('training_date', today())
                ->whereNotNull('answer_body')
                ->exists(),

            'abstraction' => UserAbstractionTraining::where('user_id', $userId)
                ->whereDate('training_date', today())
                ->whereNotNull('answer_body')
                ->exists(),

            'concretization' => UserConcretizationTraining::where('user_id', $userId)
                ->whereDate('training_date', today())
                ->whereNotNull('answer_body')
                ->exists(),
        ];

        $myTotalPoints = UserTrainingPointHistory::where('user_id', $userId)->sum('points');
        $myTrainingDifficulty = $this->calculateTrainingDifficulty((int) $myTotalPoints);

        return view('trainings.index', compact(
            'trainings',
            'todayStatuses',
            'myTotalPoints',
            'myTrainingDifficulty'
        ));
    }

    public function createDiary(): View|RedirectResponse
    {
        ApiActionLogger::info(
            methodName: 'TrainingController::createDiary',
            message: '日記トレーニング作成ページにアクセス',
            params: [
                'user_id' => auth()->id(),
            ]
        );

        if ($this->alreadyDoneToday(UserDiaryTraining::class)) {
            return redirect()
                ->route('trainings.index')
                ->with('error', '本日の日記トレーニングは実施済みです。');
        }

        $myTotalPoints = $this->myTotalTrainingPoints();
        $myTrainingDifficulty = $this->calculateTrainingDifficulty($myTotalPoints);

        return view('trainings.diary-create', compact('myTotalPoints', 'myTrainingDifficulty'));
    }

    public function storeDiary(Request $request, TrainingAiScoringService $scoringService): RedirectResponse
    {
        ApiActionLogger::info(
            methodName: 'TrainingController::storeDiary',
            message: '日記トレーニング保存処理を開始',
            params: [
                'user_id' => auth()->id(),
                'training_date' => $request->input('training_date'),
                'diary_body_length' => mb_strlen((string) $request->input('diary_body')),
            ]
        );

        $validated = $request->validate([
            'training_date' => ['required', 'date'],
            'diary_body' => ['required', 'string', 'max:5000'],
        ], [
            'training_date.required' => '日付を入力してください。',
            'diary_body.required' => '日記を入力してください。',
            'diary_body.max' => '日記は5000文字以内で入力してください。',
        ]);

        if ($this->alreadyDoneOnDate(UserDiaryTraining::class, $validated['training_date'])) {
            ApiActionLogger::info(
                methodName: 'TrainingController::storeDiary',
                message: '日記トレーニングは指定日に実施済みのため保存中止',
                params: [
                    'user_id' => auth()->id(),
                    'training_date' => $validated['training_date'],
                ]
            );

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

            ApiActionLogger::info(
                methodName: 'TrainingController::storeDiary',
                message: '日記トレーニングAI採点に失敗',
                params: [
                    'user_id' => auth()->id(),
                    'training_date' => $validated['training_date'],
                    'error_message' => $e->getMessage(),
                ]
            );

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

            ApiActionLogger::info(
                methodName: 'TrainingController::storeDiary',
                message: '日記トレーニングを保存しました',
                params: [
                    'user_id' => auth()->id(),
                    'training_id' => $training->id,
                    'training_type' => UserDiaryTraining::TYPE,
                    'training_date' => $validated['training_date'],
                    'total_score' => $score['total_score'] ?? null,
                    'earned_points' => $points,
                    'ai_provider' => $score['ai_provider'] ?? null,
                    'ai_model' => $score['ai_model'] ?? null,
                    'is_fallback' => $score['is_fallback'] ?? null,
                    'ai_attempts' => $score['ai_attempts'] ?? null,
                ]
            );

            return redirect()
                ->route('trainings.show', ['type' => UserDiaryTraining::TYPE, 'id' => $training->id])
                ->with('success', '日記トレーニングを保存しました。');
        });
    }

    public function createChallenge(): View|RedirectResponse
    {
        ApiActionLogger::info(
            methodName: 'TrainingController::createChallenge',
            message: '今日のチャレンジ作成ページにアクセス',
            params: [
                'user_id' => auth()->id(),
            ]
        );

        if ($this->alreadyDoneToday(UserChallengeTraining::class)) {
            return redirect()
                ->route('trainings.index')
                ->with('error', '本日の今日のチャレンジは実施済みです。');
        }

        $myTotalPoints = $this->myTotalTrainingPoints();
        $myTrainingDifficulty = $this->calculateTrainingDifficulty($myTotalPoints);

        return view('trainings.challenge-create', compact('myTotalPoints', 'myTrainingDifficulty'));
    }

    public function storeChallenge(Request $request, TrainingAiScoringService $scoringService): RedirectResponse
    {
        ApiActionLogger::info(
            methodName: 'TrainingController::storeChallenge',
            message: '今日のチャレンジ保存処理を開始',
            params: [
                'user_id' => auth()->id(),
                'training_date' => $request->input('training_date'),
                'challenged_thing_length' => mb_strlen((string) $request->input('challenged_thing')),
                'completed_thing_length' => mb_strlen((string) $request->input('completed_thing')),
                'difficult_thing_length' => mb_strlen((string) $request->input('difficult_thing')),
                'next_improvement_length' => mb_strlen((string) $request->input('next_improvement')),
            ]
        );

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
            ApiActionLogger::info(
                methodName: 'TrainingController::storeChallenge',
                message: '今日のチャレンジは指定日に実施済みのため保存中止',
                params: [
                    'user_id' => auth()->id(),
                    'training_date' => $validated['training_date'],
                ]
            );

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

            ApiActionLogger::info(
                methodName: 'TrainingController::storeChallenge',
                message: '今日のチャレンジAI採点に失敗',
                params: [
                    'user_id' => auth()->id(),
                    'training_date' => $validated['training_date'],
                    'error_message' => $e->getMessage(),
                ]
            );

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

            ApiActionLogger::info(
                methodName: 'TrainingController::storeChallenge',
                message: '今日のチャレンジを保存しました',
                params: [
                    'user_id' => auth()->id(),
                    'training_id' => $training->id,
                    'training_type' => UserChallengeTraining::TYPE,
                    'training_date' => $validated['training_date'],
                    'total_score' => $score['total_score'] ?? null,
                    'earned_points' => $points,
                    'ai_provider' => $score['ai_provider'] ?? null,
                    'ai_model' => $score['ai_model'] ?? null,
                    'is_fallback' => $score['is_fallback'] ?? null,
                    'ai_attempts' => $score['ai_attempts'] ?? null,
                ]
            );

            return redirect()
                ->route('trainings.show', ['type' => UserChallengeTraining::TYPE, 'id' => $training->id])
                ->with('success', '今日のチャレンジを保存しました。');
        });
    }

    public function createSummary(TrainingAiScoringService $scoringService): View|RedirectResponse
    {
        ApiActionLogger::info(
            methodName: 'TrainingController::createSummary',
            message: '要約力トレーニング作成ページにアクセス',
            params: [
                'user_id' => auth()->id(),
                'training_type' => UserSummaryTraining::TYPE,
            ]
        );

        return $this->createAiTraining(UserSummaryTraining::TYPE, UserSummaryTraining::class, $scoringService);
    }

    public function storeSummary(
        Request $request,
        UserSummaryTraining $training,
        TrainingAiScoringService $scoringService
    ): RedirectResponse {
        ApiActionLogger::info(
            methodName: 'TrainingController::storeSummary',
            message: '要約力トレーニング回答保存処理を開始',
            params: [
                'user_id' => auth()->id(),
                'training_id' => $training->id,
                'training_type' => UserSummaryTraining::TYPE,
                'answer_body_length' => mb_strlen((string) $request->input('answer_body')),
            ]
        );

        return $this->storeAiTraining($request, $training, UserSummaryTraining::TYPE, $scoringService);
    }

    public function createVerbalization(TrainingAiScoringService $scoringService): View|RedirectResponse
    {
        ApiActionLogger::info(
            methodName: 'TrainingController::createVerbalization',
            message: '言語化力トレーニング作成ページにアクセス',
            params: [
                'user_id' => auth()->id(),
                'training_type' => UserVerbalizationTraining::TYPE,
            ]
        );

        return $this->createAiTraining(UserVerbalizationTraining::TYPE, UserVerbalizationTraining::class, $scoringService);
    }

    public function storeVerbalization(
        Request $request,
        UserVerbalizationTraining $training,
        TrainingAiScoringService $scoringService
    ): RedirectResponse {
        ApiActionLogger::info(
            methodName: 'TrainingController::storeVerbalization',
            message: '言語化力トレーニング回答保存処理を開始',
            params: [
                'user_id' => auth()->id(),
                'training_id' => $training->id,
                'training_type' => UserVerbalizationTraining::TYPE,
                'answer_body_length' => mb_strlen((string) $request->input('answer_body')),
            ]
        );

        return $this->storeAiTraining($request, $training, UserVerbalizationTraining::TYPE, $scoringService);
    }

    public function createAbstraction(TrainingAiScoringService $scoringService): View|RedirectResponse
    {
        ApiActionLogger::info(
            methodName: 'TrainingController::createAbstraction',
            message: '抽象化力トレーニング作成ページにアクセス',
            params: [
                'user_id' => auth()->id(),
                'training_type' => UserAbstractionTraining::TYPE,
            ]
        );

        return $this->createAiTraining(UserAbstractionTraining::TYPE, UserAbstractionTraining::class, $scoringService);
    }

    public function storeAbstraction(
        Request $request,
        UserAbstractionTraining $training,
        TrainingAiScoringService $scoringService
    ): RedirectResponse {
        ApiActionLogger::info(
            methodName: 'TrainingController::storeAbstraction',
            message: '抽象化力トレーニング回答保存処理を開始',
            params: [
                'user_id' => auth()->id(),
                'training_id' => $training->id,
                'training_type' => UserAbstractionTraining::TYPE,
                'answer_body_length' => mb_strlen((string) $request->input('answer_body')),
            ]
        );

        return $this->storeAiTraining($request, $training, UserAbstractionTraining::TYPE, $scoringService);
    }

    public function createConcretization(TrainingAiScoringService $scoringService): View|RedirectResponse
    {
        ApiActionLogger::info(
            methodName: 'TrainingController::createConcretization',
            message: '具体化力トレーニング作成ページにアクセス',
            params: [
                'user_id' => auth()->id(),
                'training_type' => UserConcretizationTraining::TYPE,
            ]
        );

        return $this->createAiTraining(UserConcretizationTraining::TYPE, UserConcretizationTraining::class, $scoringService);
    }

    public function storeConcretization(
        Request $request,
        UserConcretizationTraining $training,
        TrainingAiScoringService $scoringService
    ): RedirectResponse {
        ApiActionLogger::info(
            methodName: 'TrainingController::storeConcretization',
            message: '具体化力トレーニング回答保存処理を開始',
            params: [
                'user_id' => auth()->id(),
                'training_id' => $training->id,
                'training_type' => UserConcretizationTraining::TYPE,
                'answer_body_length' => mb_strlen((string) $request->input('answer_body')),
            ]
        );

        return $this->storeAiTraining($request, $training, UserConcretizationTraining::TYPE, $scoringService);
    }

    public function show(string $type, int $id): View
    {
        ApiActionLogger::info(
            methodName: 'TrainingController::show',
            message: 'トレーニング詳細ページにアクセス',
            params: [
                'user_id' => auth()->id(),
                'training_type' => $type,
                'training_id' => $id,
            ]
        );

        $training = $this->findTraining($type, $id);

        abort_unless($training->user_id === auth()->id(), 403);

        return view('trainings.show', compact('training', 'type'));
    }

    public function ranking(): View
    {
        ApiActionLogger::info(
            methodName: 'TrainingController::ranking',
            message: 'トレーニングランキングページにアクセス',
            params: [
                'user_id' => auth()->id(),
            ]
        );

        $monthlyRankings = UserTrainingPointHistory::query()
            ->select('user_id')
            ->selectRaw('SUM(points) as total_points')
            ->selectRaw('COUNT(*) as training_count')
            ->whereBetween('earned_on', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])
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

    private function createAiTraining(
        string $type,
        string $modelClass,
        TrainingAiScoringService $scoringService
    ): View|RedirectResponse {
        $today = now()->toDateString();

        $training = $modelClass::query()
            ->where('user_id', auth()->id())
            ->whereDate('training_date', $today)
            ->first();

        if ($training && filled($training->answer_body)) {
            ApiActionLogger::info(
                methodName: 'TrainingController::createAiTraining',
                message: 'AI出題型トレーニングは本日回答済みのため詳細へリダイレクト',
                params: [
                    'user_id' => auth()->id(),
                    'training_type' => $type,
                    'training_id' => $training->id,
                ]
            );

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

                ApiActionLogger::info(
                    methodName: 'TrainingController::createAiTraining',
                    message: 'AI出題型トレーニングの問題生成に失敗',
                    params: [
                        'user_id' => auth()->id(),
                        'training_type' => $type,
                        'error_message' => $e->getMessage(),
                    ]
                );

                return redirect()
                    ->route('trainings.index')
                    ->with('error', $e->getMessage());
            }

            $training = $modelClass::create([
                'user_id' => auth()->id(),
                'training_date' => $today,
                'question_title' => $question['question_title'],
                'question_body' => $question['question_body'],

                // AI履歴
                'ai_provider' => $question['ai_provider'] ?? null,
                'ai_model' => $question['ai_model'] ?? null,
                'ai_status' => $question['ai_status'] ?? null,
                'ai_error_message' => $question['ai_error_message'] ?? null,
                'is_fallback' => $question['is_fallback'] ?? false,
                'ai_attempts' => $question['ai_attempts'] ?? 1,
            ]);

            ApiActionLogger::info(
                methodName: 'TrainingController::createAiTraining',
                message: 'AI出題型トレーニングの問題を作成しました',
                params: [
                    'user_id' => auth()->id(),
                    'training_type' => $type,
                    'training_id' => $training->id,
                    'training_date' => $today,
                    'ai_provider' => $question['ai_provider'] ?? null,
                    'ai_model' => $question['ai_model'] ?? null,
                    'is_fallback' => $question['is_fallback'] ?? null,
                    'ai_attempts' => $question['ai_attempts'] ?? null,
                ]
            );
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
        TrainingAiScoringService $scoringService
    ): RedirectResponse {
        abort_unless($training->user_id === auth()->id(), 403);

        if (filled($training->answer_body)) {
            ApiActionLogger::info(
                methodName: 'TrainingController::storeAiTraining',
                message: 'AI出題型トレーニングは回答済みのため保存中止',
                params: [
                    'user_id' => auth()->id(),
                    'training_type' => $type,
                    'training_id' => $training->id,
                ]
            );

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

            ApiActionLogger::info(
                methodName: 'TrainingController::storeAiTraining',
                message: 'AI出題型トレーニングの採点に失敗',
                params: [
                    'user_id' => auth()->id(),
                    'training_type' => $type,
                    'training_id' => $training->id,
                    'error_message' => $e->getMessage(),
                ]
            );

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

            ApiActionLogger::info(
                methodName: 'TrainingController::storeAiTraining',
                message: 'AI出題型トレーニングを保存しました',
                params: [
                    'user_id' => auth()->id(),
                    'training_type' => $type,
                    'training_id' => $training->id,
                    'training_date' => $training->training_date->toDateString(),
                    'total_score' => $score['total_score'] ?? null,
                    'earned_points' => $points,
                    'ai_provider' => $score['ai_provider'] ?? null,
                    'ai_model' => $score['ai_model'] ?? null,
                    'is_fallback' => $score['is_fallback'] ?? null,
                    'ai_attempts' => $score['ai_attempts'] ?? null,
                ]
            );

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
