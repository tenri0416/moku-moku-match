<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationStoreRequest;
use App\Models\Application;
use App\Models\Block;
use App\Models\WorkPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(WorkPost $workPost): View
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
        $unavailableReason = $this->getApplicationUnavailableReason($workPost);

        if ($unavailableReason) {
            return redirect()
                ->route('work-posts.show', $workPost)
                ->with('error', $unavailableReason);
        }

        return view('applications.create', compact('workPost'));
    }

    public function store(ApplicationStoreRequest $request, WorkPost $workPost): RedirectResponse
    {
        $unavailableReason = $this->getApplicationUnavailableReason($workPost);

        if ($unavailableReason) {
            return redirect()
                ->route('work-posts.show', $workPost)
                ->with('error', $unavailableReason);
        }

        Application::create([
            'work_post_id' => $workPost->id,
            'user_id' => auth()->id(),
            'message' => $request->validated('message'),
            'status' => Application::STATUS_PENDING,
        ]);

        return redirect()
            ->route('work-posts.show', $workPost)
            ->with('success', '参加申請を送信しました。');
    }

    public function approve(Application $application): RedirectResponse
    {
        $this->authorize('approve', $application);

        $application->update([
            'status' => Application::STATUS_APPROVED,
        ]);

        return back()->with('success', '参加申請を承認しました。');
    }

    public function reject(Application $application): RedirectResponse
    {
        $this->authorize('reject', $application);

        $application->update([
            'status' => Application::STATUS_REJECTED,
        ]);

        return back()->with('success', '参加申請を否認しました。');
    }

    /**
     * 参加申請できない理由を返す
     *
     * null の場合は申請可能。
     */
    private function getApplicationUnavailableReason(WorkPost $workPost): ?string
    {
        if (! auth()->user()->profile) {
            return '参加申請をするには、先にプロフィール登録が必要です。';
        }

        if ($workPost->user_id === auth()->id()) {
            return '自分が作成した募集には参加申請できません。';
        }

        if (! $workPost->isOpen()) {
            return 'この募集は現在、参加申請を受け付けていません。募集状態をご確認ください。';
        }

        $alreadyApplied = $workPost->applications()
            ->where('user_id', auth()->id())
            ->exists();

        if ($alreadyApplied) {
            return 'この募集にはすでに参加申請済みです。';
        }

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

        if ($blocked) {
            return 'ブロック関係にあるユーザーの募集には参加申請できません。';
        }

        return null;
    }
}
