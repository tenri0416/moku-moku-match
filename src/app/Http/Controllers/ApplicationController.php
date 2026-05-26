<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ApplicationStoreRequest;
use App\Models\Application;
use App\Models\Block;
use App\Models\WorkPost;

class ApplicationController extends Controller
{
    public function index(WorkPost $workPost)
    {
        abort_unless($workPost->user_id === auth()->id(), 403);

        $applications = $workPost->applications()
            ->with('user.profile')
            ->latest()
            ->get();

        return view('applications.index', compact('workPost', 'applications'));
    }

    public function create(WorkPost $workPost)
    {
        $this->validateApplicationAvailable($workPost);

        return view('applications.create', compact('workPost'));
    }

     public function store(ApplicationStoreRequest $request, WorkPost $workPost)
    {
        $this->validateApplicationAvailable($workPost);

        Application::create([
            'work_post_id' => $workPost->id,
            'user_id' => auth()->id(),
            'message' => $request->validated('message'),
            'status' => Application::STATUS_PENDING,
        ]);

        return redirect()->route('work-posts.show', $workPost)->with('success', '参加申請を送信しました。');
    }

    public function approve(Application $application)
    {
        $this->authorize('approve', $application);

        $application->update([
            'status' => Application::STATUS_APPROVED,
        ]);

        return back()->with('success', '参加申請を承認しました。');
    }

    public function reject(Application $application)
    {
        $this->authorize('reject', $application);

        $application->update([
            'status' => Application::STATUS_REJECTED,
        ]);

        return back()->with('success', '参加申請を否認しました。');
    }

     private function validateApplicationAvailable(WorkPost $workPost): void
    {
        abort_unless(auth()->user()->profile, 403, 'プロフィール登録が必要です。');
        abort_if($workPost->user_id === auth()->id(), 403, '自分の募集には申請できません。');
        abort_unless($workPost->isOpen(), 403, 'この募集は申請できません。');

        $alreadyApplied = $workPost->applications()->where('user_id', auth()->id())->exists();
        abort_if($alreadyApplied, 403, 'すでに申請済みです。');

        $blocked = Block::query()
            ->where(function ($query) use ($workPost) {
                $query->where('blocker_id', auth()->id())
                    ->where('blocked_user_id', $workPost->user_id);
            })
            ->orWhere(function ($query) use ($workPost) {
                $query->where('blocker_id', $workPost->user_id)
                    ->where('blocked_user_id', auth()->id());
            })
            ->exists();

            abort_if($blocked, 403, 'ブロック関係のため申請できません。');
    }


}
