<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\WorkPost;
use App\Notifications\MessageReceivedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * 自分に関係するメッセージ一覧を表示する
     *
     * messages テーブルから work_post_id を削除したため、
     * sender_id / receiver_id のみで会話相手ごとに一覧化する。
     */
    public function index(): View
    {
        $loginUserId = Auth::id();

        $allMessages = Message::query()
            ->with(['sender.profile', 'receiver.profile'])
            ->where(function ($query) use ($loginUserId) {
                $query->where('sender_id', $loginUserId)
                    ->orWhere('receiver_id', $loginUserId);
            })
            ->latest()
            ->get();

        $messages = $allMessages
            ->groupBy(function (Message $message) use ($loginUserId) {
                return $message->sender_id === $loginUserId
                    ? $message->receiver_id
                    : $message->sender_id;
            })
            ->map(function ($group) {
                return $group->first();
            })
            ->values();

        return view('messages.index', compact('messages'));
    }

    /**
     * 旧URL互換用
     *
     * 以前の /messages/{workPost}/{user} にアクセスされた場合も、
     * 募集なしDM画面へ流す。
     */
    public function show(WorkPost $workPost, User $user): View
    {
        return $this->showUser($user);
    }

    /**
     * 旧URL互換用
     *
     * 以前の /messages/{workPost}/{user} へPOSTされた場合も、
     * 募集なしDMとして保存する。
     */
    public function store(Request $request, WorkPost $workPost, User $user): RedirectResponse|JsonResponse
    {
        return $this->storeUser($request, $user);
    }

    /**
     * 旧URL互換用
     *
     * 以前の /messages/{workPost}/{user}/latest にアクセスされた場合も、
     * 募集なしDMの新着取得として扱う。
     */
    public function latest(Request $request, WorkPost $workPost, User $user): JsonResponse
    {
        return $this->latestUser($request, $user);
    }

    /**
     * 募集に紐づかないユーザー同士のメッセージ画面を表示する
     */
    public function showUser(User $user): View
    {
        $loginUser = Auth::user();

        abort_unless($loginUser, 403);

        // 自分自身とのメッセージ画面は開けないようにする
        abort_if($loginUser->id === $user->id, 404);

        $messages = Message::query()
            ->with(['sender.profile', 'receiver.profile'])
            ->where(function ($query) use ($loginUser, $user) {
                $query->where(function ($query) use ($loginUser, $user) {
                    $query->where('sender_id', $loginUser->id)
                        ->where('receiver_id', $user->id);
                })->orWhere(function ($query) use ($loginUser, $user) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', $loginUser->id);
                });
            })
            ->orderBy('created_at')
            ->get();

        // 相手から自分宛てに届いた未読メッセージを既読にする
        Message::query()
            ->where('sender_id', $user->id)
            ->where('receiver_id', $loginUser->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        $latestMessageId = $messages->last()?->id ?? 0;

        return view('messages.user-show', [
            'user' => $user,
            'messages' => $messages,
            'latestMessageId' => $latestMessageId,
        ]);
    }

    /**
     * 募集に紐づかないユーザー同士のメッセージを送信する
     */
    public function storeUser(Request $request, User $user): RedirectResponse|JsonResponse
    {
        abort_unless(Auth::check(), 403);

        // 自分自身には送れない
        abort_if(Auth::id() === $user->id, 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ], [
            'body.required' => 'メッセージを入力してください。',
            'body.max' => 'メッセージは2000文字以内で入力してください。',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'body' => $validated['body'],
        ]);

        $message->load(['sender.profile', 'receiver.profile']);

        if (class_exists(MessageReceivedNotification::class)) {
            $message->receiver->notify(
                new MessageReceivedNotification($message)
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
            ->route('messages.users.show', $user)
            ->with('success', 'メッセージを送信しました。');
    }

    /**
     * 募集に紐づかないユーザー同士の新着メッセージを取得する
     */
    public function latestUser(Request $request, User $user): JsonResponse
    {
        abort_unless(Auth::check(), 403);

        // 自分自身とのメッセージ取得は不可
        abort_if(Auth::id() === $user->id, 403);

        $afterId = (int) $request->query('after_id', 0);

        $messages = Message::query()
            ->with(['sender.profile'])
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
                ->update([
                    'read_at' => now(),
                ]);
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
     * 旧返信処理
     *
     * work_post_id を使わず、receiver_id 宛ての通常DMとして保存する。
     */
    public function reply(Request $request, WorkPost $workPost): RedirectResponse
    {
        abort_unless(Auth::check(), 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'receiver_id' => ['required', 'exists:users,id'],
        ], [
            'body.required' => 'メッセージを入力してください。',
            'body.max' => 'メッセージは2000文字以内で入力してください。',
            'receiver_id.required' => '送信先ユーザーが指定されていません。',
            'receiver_id.exists' => '送信先ユーザーが存在しません。',
        ]);

        abort_if((int) $validated['receiver_id'] === Auth::id(), 403);

        $receiver = User::findOrFail($validated['receiver_id']);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiver->id,
            'body' => $validated['body'],
        ]);

        if (class_exists(MessageReceivedNotification::class)) {
            $message->receiver->notify(
                new MessageReceivedNotification($message)
            );
        }

        return redirect()
            ->route('messages.users.show', $receiver)
            ->with('success', '返信しました。');
    }
}
