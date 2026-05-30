<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\WorkPost;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Notifications\MessageReceivedNotification;

class MessageController extends Controller
{
    /**
     * 自分に関係するメッセージ一覧を表示する
     */
    public function index(): View
    {
        $messages = Message::query()
            ->with(['workPost', 'sender', 'receiver'])
            ->where(function ($query) {
                $query->where('sender_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            })
            ->latest()
            ->get()
            ->groupBy('work_post_id');

        return view('messages.index', compact('messages'));
    }

    /**
     * 募集投稿に紐づくメッセージ詳細を表示する
     */
    public function show(WorkPost $workPost): View
    {
        $this->authorizeMessageAccess($workPost);

        $messages = Message::query()
            ->with(['sender', 'receiver'])
            ->where('work_post_id', $workPost->id)
            ->where(function ($query) {
                $query->where('sender_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            })
            ->oldest()
            ->get();

        Message::query()
            ->where('work_post_id', $workPost->id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.show', compact('workPost', 'messages'));
    }

    /**
     * 募集投稿の投稿者へメッセージを送信する
     */
    public function store(Request $request, WorkPost $workPost): RedirectResponse
    {
        abort_if($workPost->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'work_post_id' => $workPost->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $workPost->user_id,
            'body' => $validated['body'],
        ]);

        $message->receiver->notify(new MessageReceivedNotification($message));

        return redirect()
            ->route('messages.show', $workPost)
            ->with('success', 'メッセージを送信しました。');
    }

    /**
     * メッセージ返信
     */
    public function reply(Request $request, WorkPost $workPost): RedirectResponse
    {
        $this->authorizeMessageAccess($workPost);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'receiver_id' => ['required', 'exists:users,id'],
        ]);

        abort_if((int) $validated['receiver_id'] === Auth::id(), 403);

        $message = Message::create([
            'work_post_id' => $workPost->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'body' => $validated['body'],
        ]);

        $message->receiver->notify(new MessageReceivedNotification($message));

        return redirect()
            ->route('messages.show', $workPost)
            ->with('success', '返信しました。');
    }

    /**
     * ログインユーザーが対象募集のメッセージを見られるか確認する
     */
    private function authorizeMessageAccess(WorkPost $workPost): void
    {
        $exists = Message::query()
            ->where('work_post_id', $workPost->id)
            ->where(function ($query) {
                $query->where('sender_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            })
            ->exists();

        abort_unless($exists || $workPost->user_id === Auth::id(), 403);
    }
}
