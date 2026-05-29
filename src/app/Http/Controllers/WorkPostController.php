<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkPostStoreRequest;
use App\Http\Requests\WorkPostUpdateRequest;
use App\Models\Prefecture;
use App\Models\WorkPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WorkPostController extends Controller
{
    public function index(Request $request)
    {
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
        if (! auth()->user()->profile) {
            return redirect()->route('profile.edit')->with('error', '募集を作成する前にプロフィールを登録してください。');
        }

        $prefectures = Prefecture::orderBy('id')->get();

        return view('work-posts.create', compact('prefectures'));
    }

    public function store(WorkPostStoreRequest $request)
    {
        if (! auth()->user()->profile) {
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
        Log::info('募集の詳細画面にアクセスされました。', [
            'work_post_id' => $workPost->id,
            'user_id' => auth()->id(),
        ]);

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
