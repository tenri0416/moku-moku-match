<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MessageStoreRequest;
use App\Models\Application;
use App\Models\Block;
use App\Models\Message;
use App\Models\User;
use App\Models\WorkPost;


class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::query()
            ->with(['workPost', 'sender.profile', 'receiver.profile'])
            ->where('sender_id', auth()->id())
            ->orWhere('receiver_id', auth()->id())
            ->latest()
            ->get();

        return view('messages.index', compact('messages'));
    }

    public function show(WorkPost $workPost, User $user)
    {
        $this->validateCanMessage($workPost, $user);

        $messages = Message::query()
            ->where('work_post_id', $workPost->id)
            ->where(function ($query) use ($user) {
                $query->where(function ($query) use ($user) {
                    $query->where('sender_id', auth()->id())
                        ->where('receiver_id', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', auth()->id());
                });
            })
            ->oldest()
            ->get();

        return view('messages.show', compact('workPost', 'user', 'messages'));
    }

     public function store(MessageStoreRequest $request, WorkPost $workPost, User $user)
    {
        $this->validateCanMessage($workPost, $user);

        Message::create([
            'work_post_id' => $workPost->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'body' => $request->validated('body'),
        ]);

        return back()->with('success', 'メッセージを送信しました。');
    }

    private function validateCanMessage(WorkPost $workPost, User $user): void
    {
        abort_if($user->id === auth()->id(), 403);

        $isOwner = $workPost->user_id === auth()->id();

        $approvedApplicationExists = Application::query()
            ->where('work_post_id', $workPost->id)
            ->where('status', Application::STATUS_APPROVED)
            ->where(function ($query) use ($user, $workPost, $isOwner) {
                if ($isOwner) {
                    $query->where('user_id', $user->id);
                } else {
                    $query->where('user_id', auth()->id())
                        ->whereHas('workPost', fn ($query) => $query->where('user_id', $user->id));
                }
            })
            ->exists();
            abort_unless($approvedApplicationExists, 403);

        $blocked = Block::query()
            ->where(function ($query) use ($user) {
                $query->where('blocker_id', auth()->id())
                    ->where('blocked_user_id', $user->id);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('blocker_id', $user->id)
                    ->where('blocked_user_id', auth()->id());
            })
            ->exists();

        abort_if($blocked, 403);
    }
}
