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
    public function index(Request $request)
    {
        ApiActionLogger::info(
            'WorkPostController::index',
            '募集一覧画面にアクセス',
            [
                'user_id' => auth()->id(),
                'keyword' => $request->keyword,
                'purpose' => $request->purpose,
                'location_type' => $request->location_type,
                'prefecture_id' => $request->prefecture_id,
                'time_zone' => $request->time_zone,
                'status' => $request->status,
            ]
        );

        $workPosts = WorkPost::query()
            ->with(['user.profile.prefecture', 'prefecture'])
            ->when($request->keyword, function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('body', 'like', "%{$keyword}%");
                });
            })
            ->when($request->purpose, fn ($query, $purpose) => $query->where('purpose', $purpose))
            ->when($request->location_type, fn ($query, $locationType) => $query->where('location_type', $locationType))
            ->when($request->prefecture_id, fn ($query, $prefectureId) => $query->where('prefecture_id', $prefectureId))
            ->when($request->time_zone, fn ($query, $timeZone) => $query->where('time_zone', $timeZone))
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->where('status', '!=', WorkPost::STATUS_PRIVATE)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $prefectures = Prefecture::orderBy('id')->get();

        return view('work-posts.index', compact('workPosts', 'prefectures'));
    }

    public function create()
    {
        ApiActionLogger::info(
            'WorkPostController::create',
            '募集作成画面にアクセス',
            [
                'user_id' => auth()->id(),
            ]
        );

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

        return view('work-posts.create', compact('prefectures'));
    }

    public function store(WorkPostStoreRequest $request)
    {
        ApiActionLogger::info(
            'WorkPostController::store',
            '募集作成処理開始',
            [
                'user_id' => auth()->id(),
                'title' => $request->title,
                'purpose' => $request->purpose,
                'location_type' => $request->location_type,
                'prefecture_id' => $request->prefecture_id,
                'time_zone' => $request->time_zone,
            ]
        );

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

        ApiActionLogger::info(
            'WorkPostController::store',
            '募集作成成功',
            [
                'user_id' => auth()->id(),
                'work_post_id' => $workPost->id,
                'status' => $workPost->status,
            ]
        );

        return redirect()->route('work-posts.show', $workPost)->with('success', '募集を作成しました。');
    }

    public function show(WorkPost $workPost)
    {
        ApiActionLogger::info(
            'WorkPostController::show',
            '募集詳細画面にアクセス',
            [
                'user_id' => auth()->id(),
                'work_post_id' => $workPost->id,
                'work_post_owner_id' => $workPost->user_id,
                'status' => $workPost->status,
            ]
        );

        abort_if($workPost->status === WorkPost::STATUS_PRIVATE, 404);

        $workPost->load(['user.profile.prefecture', 'prefecture']);

        $hasApplied = auth()->check()
            ? $workPost->applications()->where('user_id', auth()->id())->exists()
            : false;

        return view('work-posts.show', compact('workPost', 'hasApplied'));
    }

    public function edit(WorkPost $workPost)
    {
        ApiActionLogger::info(
            'WorkPostController::edit',
            '募集編集画面にアクセス',
            [
                'user_id' => auth()->id(),
                'work_post_id' => $workPost->id,
                'work_post_owner_id' => $workPost->user_id,
            ]
        );

        $this->authorize('update', $workPost);

        $prefectures = Prefecture::orderBy('id')->get();

        return view('work-posts.edit', compact('workPost', 'prefectures'));
    }

    public function update(WorkPostUpdateRequest $request, WorkPost $workPost)
    {
        ApiActionLogger::info(
            'WorkPostController::update',
            '募集更新処理開始',
            [
                'user_id' => auth()->id(),
                'work_post_id' => $workPost->id,
                'title' => $request->title,
                'purpose' => $request->purpose,
                'location_type' => $request->location_type,
                'prefecture_id' => $request->prefecture_id,
                'time_zone' => $request->time_zone,
            ]
        );

        $this->authorize('update', $workPost);

        $workPost->update($request->validated());

        ApiActionLogger::info(
            'WorkPostController::update',
            '募集更新成功',
            [
                'user_id' => auth()->id(),
                'work_post_id' => $workPost->id,
            ]
        );

        return redirect()->route('work-posts.show', $workPost)->with('success', '募集を更新しました。');
    }

    public function close(WorkPost $workPost)
    {
        ApiActionLogger::info(
            'WorkPostController::close',
            '募集終了処理開始',
            [
                'user_id' => auth()->id(),
                'work_post_id' => $workPost->id,
                'current_status' => $workPost->status,
            ]
        );

        $this->authorize('close', $workPost);

        $workPost->update([
            'status' => WorkPost::STATUS_CLOSED,
        ]);

        ApiActionLogger::info(
            'WorkPostController::close',
            '募集終了成功',
            [
                'user_id' => auth()->id(),
                'work_post_id' => $workPost->id,
                'status' => WorkPost::STATUS_CLOSED,
            ]
        );

        return redirect()->route('work-posts.show', $workPost)->with('success', '募集を終了しました。');
    }
}
