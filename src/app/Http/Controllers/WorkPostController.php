<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\WorkPostStoreRequest;
use App\Http\Requests\WorkPostUpdateRequest;
use App\Models\WorkPost;


class WorkPostController extends Controller
{
    public function index(Request $request)
    {
        $workPosts = WorkPost::query()
            ->with('user.profile')
            ->when($request->keyword, function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('body', 'like', "%{$keyword}%");
                });
            })
            ->when($request->purpose, fn ($query, $purpose) => $query->where('purpose', $purpose))
            ->when($request->location_type, fn ($query, $locationType) => $query->where('location_type', $locationType))
            ->when($request->prefecture, fn ($query, $prefecture) => $query->where('prefecture', $prefecture))
            ->when($request->time_zone, fn ($query, $timeZone) => $query->where('time_zone', $timeZone))
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->where('status', '!=', WorkPost::STATUS_PRIVATE)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('work-posts.index', compact('workPosts'));
    }
     public function create()
    {
        if (! auth()->user()->profile) {
            return redirect()->route('profile.edit')->with('error', '募集を作成する前にプロフィールを登録してください。');
        }

        return view('work-posts.create');
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
        abort_if($workPost->status === WorkPost::STATUS_PRIVATE, 404);

        $workPost->load('user.profile');

        $hasApplied = auth()->check()
            ? $workPost->applications()->where('user_id', auth()->id())->exists()
            : false;

        return view('work-posts.show', compact('workPost', 'hasApplied'));
    }

     public function edit(WorkPost $workPost)
    {
        $this->authorize('update', $workPost);

        return view('work-posts.edit', compact('workPost'));
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
