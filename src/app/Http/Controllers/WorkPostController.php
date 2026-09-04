<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkPostStoreRequest;
use App\Http\Requests\WorkPostUpdateRequest;
use App\Models\Prefecture;
use App\Models\WorkPost;
use App\Support\ApiActionLogger;
use Illuminate\Http\Request;

class WorkPostController extends Controller
{
    /**
     * 募集作成画面を表示する。
     * タイトルと募集内容の入力の手間を解消するために、既に一度募集作成済みの場合は前回の募集内容を引き継ぐ。
     */
    public function create()
    {
        if (! auth()->user()->profile) {
            ApiActionLogger::info(
                'WorkPostController::create',
                'プロフィール未登録のため募集作成画面へアクセス不可',
                [
                    'user_id' => auth()->id(),
                ]
            );
            return redirect()->route('profile.edit')->with('error', '募集を作成する前にプロフィールを登録してください。');
        }

        $prefectures = Prefecture::orderBy('id')->get();

        // 前回の募集内容を引き継ぐ
        $previousWorkPost = auth()->user()->workPosts()->latest()->first();

        return view('work-posts.create', compact('prefectures', 'previousWorkPost'));
    }

    public function store(WorkPostStoreRequest $request)
    {
        if (! auth()->user()->profile) {
            ApiActionLogger::info(
                'WorkPostController::store',
                'プロフィール未登録のため募集作成不可',
                [
                    'user_id' => auth()->id(),
                ]
            );

            return redirect()->route('profile.edit')->with('error', '募集を作成する前にプロフィールを登録してください。');
        }

        $workPost = WorkPost::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
            'status' => WorkPost::STATUS_OPEN,
        ]);

        return redirect()->route('work-posts.show', $workPost)->with('success', '募集を作成しました。');
    }

    public function show(WorkPost $workPost)
    {
        abort_if($workPost->status === WorkPost::STATUS_PRIVATE, 404);

        $workPost->load(['user.profile.prefecture', 'prefecture']);

        $hasApplied = auth()->check()
            ? $workPost->applications()->where('user_id', auth()->id())->exists()
            : false;

        return view('work-posts.show', compact('workPost', 'hasApplied'));
    }

    public function edit(WorkPost $workPost)
    {
        $this->authorize('update', $workPost);
        $prefectures = Prefecture::orderBy('id')->get();
        return view('work-posts.edit', compact('workPost', 'prefectures'));
    }

    public function update(WorkPostUpdateRequest $request, WorkPost $workPost)
    {

        $this->authorize('update', $workPost);
        $workPost->update($request->validated());

        return redirect()->route('work-posts.show', $workPost)->with('success', '募集を更新しました。');
    }

    public function close(WorkPost $workPost)
    {
        $this->authorize('close', $workPost);

        $workPost->update([
            'status' => WorkPost::STATUS_CLOSED,
        ]);

        return redirect()->route('work-posts.show', $workPost)->with('success', '募集を終了しました。');
    }
}
