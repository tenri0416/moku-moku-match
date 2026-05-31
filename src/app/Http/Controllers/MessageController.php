<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\WorkPost;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Notifications\MessageReceivedNotification;
use Illuminate\Http\JsonResponse;
use App\Models\User;


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
    public function show(WorkPost $workPost, User $user): View
    {
        $loginUser = Auth::user();

        // 自分自身とのメッセージ画面は開けないようにする
        abort_if($loginUser->id === $user->id, 404);

        // この募集に関係するメッセージで、
        // ログインユーザーと相手ユーザーのやり取りだけ取得
        $messages = Message::query()
            ->where('work_post_id', $workPost->id)
            ->where(function ($query) use ($loginUser, $user) {
                $query->where(function ($query) use ($loginUser, $user) {
                    $query->where('sender_id', $loginUser->id)
                        ->where('receiver_id', $user->id);
                })
                ->orWhere(function ($query) use ($loginUser, $user) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', $loginUser->id);
                });
            })
            ->orderBy('created_at')
            ->get();

        // 相手から自分宛てに届いた未読メッセージを既読にする
        Message::query()
            ->where('work_post_id', $workPost->id)
            ->where('sender_id', $user->id)
            ->where('receiver_id', $loginUser->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        $latestMessageId = $messages->last()?->id ?? 0;

        return view('messages.show', [
            'workPost' => $workPost,
            'user' => $user,
            'messages' => $messages,
            'latestMessageId' => $latestMessageId,
        ]);
    }

    /**
     * メッセージを送信する
     */
    public function store(Request $request, WorkPost $workPost, User $user)
    {
        $this->authorizeMessagePartner($workPost, $user);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'work_post_id' => $workPost->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'body' => $validated['body'],
        ]);

        // 既存の通知クラスがある場合
        if (class_exists(\App\Notifications\MessageReceivedNotification::class)) {
            $message->receiver->notify(
                new \App\Notifications\MessageReceivedNotification($message)
            );
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'sender_id' => $message->sender_id,
                    'sender_name' => Auth::user()->profile->display_name
                        ?? Auth::user()->name,
                    'is_mine' => true,
                    'created_at' => $message->created_at->format('Y/m/d H:i'),
                ],
            ]);
        }

        return redirect()
            ->route('messages.show', [$workPost, $user])
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
    /**
     * 新着メッセージを取得する
     */
    public function latest(Request $request, WorkPost $workPost, User $user): JsonResponse
    {
        $this->authorizeMessagePartner($workPost, $user);

        $afterId = (int) $request->query('after_id', 0);

        $messages = Message::query()
            ->with(['sender.profile'])
            ->where('work_post_id', $workPost->id)
            ->where('id', '>', $afterId)
            ->where(function ($query) use ($user) {
                $query->where(function ($query) use ($user) {
                    $query->where('sender_id', Auth::id())
                        ->where('receiver_id', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', Auth::id());
                });
            })
            ->oldest('id')
            ->get();

        $messageIds = $messages
            ->where('receiver_id', Auth::id())
            ->pluck('id');

        if ($messageIds->isNotEmpty()) {
            Message::query()
                ->whereIn('id', $messageIds)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return response()->json([
            'messages' => $messages->map(function (Message $message) {
                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->profile->display_name
                        ?? $message->sender->name,
                    'is_mine' => $message->sender_id === Auth::id(),
                    'created_at' => $message->created_at->format('Y/m/d H:i'),
                ];
            })->values(),
        ]);
    }

    /**
     * ログインユーザーが対象のメッセージ画面を見られるか確認する
     */
    private function authorizeMessagePartner(WorkPost $workPost, User $user): void
    {
        $isOwner = $workPost->user_id === Auth::id();
        $isPartner = $user->id === Auth::id();

        $hasMessageRelation = Message::query()
            ->where('work_post_id', $workPost->id)
            ->where(function ($query) use ($user) {
                $query->where(function ($query) use ($user) {
                    $query->where('sender_id', Auth::id())
                        ->where('receiver_id', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', Auth::id());
                });
            })
            ->exists();

        abort_unless($isOwner || $isPartner || $hasMessageRelation, 403);
    }
}
