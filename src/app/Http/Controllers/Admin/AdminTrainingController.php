<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminTraining;
use App\Services\GoogleAiScoringService;
use App\Support\ApiActionLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;
use RuntimeException;

class AdminTrainingController extends Controller
{
    public function index(Request $request): View
    {
        ApiActionLogger::info(
            'Admin\AdminTrainingController::index',
            '管理者トレーニング一覧画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
                'type' => $request->type,
            ]
        );

        $trainings = AdminTraining::query()
            ->where('admin_id', auth('admin')->id())
            ->when($request->type, fn($query, $type) => $query->where('type', $type))
            ->latest('training_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.trainings.index', compact('trainings'));
    }

    public function createDiary(): View
    {
        ApiActionLogger::info(
            'Admin\AdminTrainingController::createDiary',
            '管理者日記トレーニング作成画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
            ]
        );

        return view('admin.trainings.diary-create');
    }

    public function storeDiary(Request $request, GoogleAiScoringService $scoringService): RedirectResponse
    {
        ApiActionLogger::info(
            'Admin\AdminTrainingController::storeDiary',
            '管理者日記トレーニング保存処理開始',
            [
                'admin_id' => auth('admin')->id(),
                'training_date' => $request->training_date,
            ]
        );

        $validated = $request->validate([
            'training_date' => ['required', 'date'],
            'diary_body' => ['required', 'string', 'max:5000'],
        ], [
            'training_date.required' => '日付を入力してください。',
            'diary_body.required' => '日記を入力してください。',
        ]);

        try {
            $score = $scoringService->scoreDiary($validated['diary_body']);
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'AI採点に失敗しました。APIキーや通信状況を確認してください。');
        }

        $training = AdminTraining::create([
            'admin_id' => auth('admin')->id(),
            'type' => AdminTraining::TYPE_DIARY,
            'training_date' => $validated['training_date'],
            'diary_body' => $validated['diary_body'],
            ...$score,
        ]);

        ApiActionLogger::info(
            'Admin\AdminTrainingController::storeDiary',
            '管理者日記トレーニング保存成功',
            [
                'admin_id' => auth('admin')->id(),
                'admin_training_id' => $training->id,
                'total_score' => $training->total_score,
            ]
        );

        return redirect()
            ->route('admin.trainings.show', $training)
            ->with('success', '日記トレーニングを保存しました。');
    }

    public function createChallenge(): View
    {
        ApiActionLogger::info(
            'Admin\AdminTrainingController::createChallenge',
            '管理者今日のチャレンジ作成画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
            ]
        );

        return view('admin.trainings.challenge-create');
    }

    public function storeChallenge(Request $request, GoogleAiScoringService $scoringService): RedirectResponse
    {
        ApiActionLogger::info(
            'Admin\AdminTrainingController::storeChallenge',
            '管理者今日のチャレンジ保存処理開始',
            [
                'admin_id' => auth('admin')->id(),
                'training_date' => $request->training_date,
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

        try {
            $score = $scoringService->scoreChallenge($validated);
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'AI採点に失敗しました。APIキーや通信状況を確認してください。');
        }

        $training = AdminTraining::create([
            'admin_id' => auth('admin')->id(),
            'type' => AdminTraining::TYPE_CHALLENGE,
            ...$validated,
            ...$score,
        ]);

        ApiActionLogger::info(
            'Admin\AdminTrainingController::storeChallenge',
            '管理者今日のチャレンジ保存成功',
            [
                'admin_id' => auth('admin')->id(),
                'admin_training_id' => $training->id,
                'total_score' => $training->total_score,
            ]
        );

        return redirect()
            ->route('admin.trainings.show', $training)
            ->with('success', '今日のチャレンジを保存しました。');
    }

    public function show(AdminTraining $training): View
    {
        abort_unless($training->admin_id === auth('admin')->id(), 403);

        ApiActionLogger::info(
            'Admin\AdminTrainingController::show',
            '管理者トレーニング詳細画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
                'admin_training_id' => $training->id,
                'type' => $training->type,
            ]
        );

        return view('admin.trainings.show', compact('training'));
    }

    public function createSummary(GoogleAiScoringService $scoringService)
    {
        return $this->createAiTraining(AdminTraining::TYPE_SUMMARY, $scoringService);
    }

    public function storeSummary(Request $request, AdminTraining $training, GoogleAiScoringService $scoringService)
    {
        return $this->storeAiTraining($request, $training, AdminTraining::TYPE_SUMMARY, $scoringService);
    }

    public function createVerbalization(GoogleAiScoringService $scoringService)
    {
        return $this->createAiTraining(AdminTraining::TYPE_VERBALIZATION, $scoringService);
    }

    public function storeVerbalization(Request $request, AdminTraining $training, GoogleAiScoringService $scoringService)
    {
        return $this->storeAiTraining($request, $training, AdminTraining::TYPE_VERBALIZATION, $scoringService);
    }

    public function createAbstraction(GoogleAiScoringService $scoringService)
    {
        return $this->createAiTraining(AdminTraining::TYPE_ABSTRACTION, $scoringService);
    }

    public function storeAbstraction(Request $request, AdminTraining $training, GoogleAiScoringService $scoringService)
    {
        return $this->storeAiTraining($request, $training, AdminTraining::TYPE_ABSTRACTION, $scoringService);
    }

    public function createConcretization(GoogleAiScoringService $scoringService)
    {
        return $this->createAiTraining(AdminTraining::TYPE_CONCRETIZATION, $scoringService);
    }

    public function storeConcretization(Request $request, AdminTraining $training, GoogleAiScoringService $scoringService)
    {
        return $this->storeAiTraining($request, $training, AdminTraining::TYPE_CONCRETIZATION, $scoringService);
    }

    /**
     * AI出題型トレーニング作成画面を表示する
     */
    private function createAiTraining(string $type, GoogleAiScoringService $scoringService)
    {
        ApiActionLogger::info(
            'Admin\AdminTrainingController::createAiTraining',
            '管理者AI出題型トレーニング作成画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
                'type' => $type,
            ]
        );

        $today = now()->toDateString();

        $training = AdminTraining::query()
            ->where('admin_id', auth('admin')->id())
            ->where('type', $type)
            ->whereDate('training_date', $today)
            ->first();

        if ($training && filled($training->answer_body)) {
            return redirect()
                ->route('admin.trainings.show', $training)
                ->with('error', '本日の' . $training->typeLabel() . 'は実施済みです。');
        }

        if (! $training) {
            try {
                $question = $scoringService->generateAiTrainingQuestion($type);
            } catch (Throwable $e) {
                report($e);

                return redirect()
    ->route('admin.trainings.index')
    ->with('error', $e->getMessage());
            }

            $training = AdminTraining::create([
                'admin_id' => auth('admin')->id(),
                'type' => $type,
                'training_date' => $today,
                'question_title' => $question['question_title'],
                'question_body' => $question['question_body'],
            ]);
        }

        return view('admin.trainings.ai-create', [
            'training' => $training,
            'typeLabel' => $training->typeLabel(),
            'scoreLabels' => $training->scoreLabels(),
            'storeRoute' => route($this->storeRouteNameByType($type), $training),
        ]);
    }

    /**
     * AI出題型トレーニングの回答を保存し、AI採点する
     */
    private function storeAiTraining(
        Request $request,
        AdminTraining $training,
        string $type,
        GoogleAiScoringService $scoringService
    ) {
        abort_unless($training->admin_id === auth('admin')->id(), 403);
        abort_unless($training->type === $type, 404);

        if (filled($training->answer_body)) {
            return redirect()
                ->route('admin.trainings.show', $training)
                ->with('error', '本日の' . $training->typeLabel() . 'は実施済みです。');
        }

        ApiActionLogger::info(
            'Admin\AdminTrainingController::storeAiTraining',
            '管理者AI出題型トレーニング回答保存処理開始',
            [
                'admin_id' => auth('admin')->id(),
                'admin_training_id' => $training->id,
                'type' => $type,
            ]
        );

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
                answerBody: $validated['answer_body']
            );
        } catch (Throwable $e) {
            report($e);

            return redirect()
    ->route('admin.trainings.index')
    ->with('error', $e->getMessage());
        }

        $training->update([
            'answer_body' => $validated['answer_body'],
            ...$score,
        ]);

        ApiActionLogger::info(
            'Admin\AdminTrainingController::storeAiTraining',
            '管理者AI出題型トレーニング回答保存・採点成功',
            [
                'admin_id' => auth('admin')->id(),
                'admin_training_id' => $training->id,
                'type' => $type,
                'total_score' => $training->fresh()->total_score,
            ]
        );

        return redirect()
            ->route('admin.trainings.show', $training)
            ->with('success', $training->typeLabel() . 'を保存しました。');
    }

    /**
     * 種類ごとの保存ルート名を取得する
     */
    private function storeRouteNameByType(string $type): string
    {
        return match ($type) {
            AdminTraining::TYPE_SUMMARY => 'admin.trainings.summary.store',
            AdminTraining::TYPE_VERBALIZATION => 'admin.trainings.verbalization.store',
            AdminTraining::TYPE_ABSTRACTION => 'admin.trainings.abstraction.store',
            AdminTraining::TYPE_CONCRETIZATION => 'admin.trainings.concretization.store',
            default => throw new RuntimeException('不正なトレーニング種別です。'),
        };
    }
}
