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
            ->when($request->type, fn ($query, $type) => $query->where('type', $type))
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
}
