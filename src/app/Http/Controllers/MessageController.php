<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\WorkPost;
use App\Notifications\MessageReceivedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * 自分に関係するメッセージ一覧を表示する
     *
     * 要件定義書に合わせて、messages は募集に紐づけず、
     * sender_id / receiver_id のみで会話相手ごとに一覧化する。
     */
    public function index(): View
    {
        $loginUser = Auth::user();

        abort_unless($loginUser, 403);

        $loginUserId = $loginUser->id;

        $allMessages = Message::query()
            ->with(['sender.profile', 'receiver.profile'])
            ->where(function ($query) use ($loginUserId) {
                $query->where('sender_id', $loginUserId)
                    ->orWhere('receiver_id', $loginUserId);
            })
            ->latest()
            ->get();

        $latestMessages = $allMessages
            ->groupBy(function (Message $message) use ($loginUserId) {
                return (int) $message->sender_id === (int) $loginUserId
                    ? $message->receiver_id
                    : $message->sender_id;
            })
            ->map(function (Collection $group) {
                return $group->first();
            })
            ->values();

        $unreadCountsByPartner = Message::query()
            ->selectRaw('sender_id, COUNT(*) as unread_count')
            ->where('receiver_id', $loginUserId)
            ->whereNull('read_at')
            ->groupBy('sender_id')
            ->pluck('unread_count', 'sender_id');

        $messageItems = $latestMessages
            ->map(function (Message $message) use ($loginUserId, $unreadCountsByPartner) {
                $partner = (int) $message->sender_id === (int) $loginUserId
                    ? $message->receiver
                    : $message->sender;

                if (! $partner) {
                    return null;
                }

                $profile = $partner->profile;

                $avatarPath = $profile?->avatar_path;
                $avatarUrl = $avatarPath
                    ? asset('storage/' . ltrim($avatarPath, '/'))
                    : asset('images/default-avatar.png');

                $displayName = $profile?->display_name
                    ?? $partner->name
                    ?? 'ユーザー';

                $jobType = $profile?->job_type
                    ?? '職種未設定';

                $isMine = (int) $message->sender_id === (int) $loginUserId;

                return [
                    'message' => $message,
                    'partner' => $partner,
                    'profile' => $profile,
                    'display_name' => $displayName,
                    'job_type' => $jobType,
                    'avatar_url' => $avatarUrl,
                    'unread_count' => (int) ($unreadCountsByPartner[$partner->id] ?? 0),
                    'is_mine' => $isMine,
                    'last_body' => Str::limit($message->body, 80),
                    'pc_time' => optional($message->created_at)->format('Y/m/d H:i'),
                    'sp_time' => $this->formatSpMessageTime($message),
                ];
            })
            ->filter()
            ->values();

        $conversationCount = $messageItems->count();
        $totalUnreadCount = $messageItems->sum('unread_count');
        $replyRate = $this->calculateReplyRate($loginUserId);

        return view('messages.index', compact(
            'messageItems',
            'conversationCount',
            'totalUnreadCount',
            'replyRate'
        ));
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

        // 自分自身とのメッセージ画面は開けない
        abort_if((int) $loginUser->id === (int) $user->id, 404);

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
        $this->markMessageNotificationsAsRead($loginUser, $user);

        $latestMessageId = $messages->last()?->id ?? 0;

        $partnerProfile = $user->profile;

        $avatarPath = $partnerProfile?->avatar_path;
        $partnerAvatarUrl = $avatarPath
            ? asset('storage/' . ltrim($avatarPath, '/'))
            : asset('images/default-avatar.png');

        $partnerDisplayName = $partnerProfile?->display_name
            ?? $user->name
            ?? 'ユーザー';

        $partnerJobType = $partnerProfile?->job_type
            ?? '職種未設定';

        $totalMessagesCount = $messages->count();

        $receivedMessagesCount = $messages
            ->where('sender_id', $user->id)
            ->where('receiver_id', $loginUser->id)
            ->count();

        $sentMessagesCount = $messages
            ->where('sender_id', $loginUser->id)
            ->where('receiver_id', $user->id)
            ->count();

        $lastMessageAt = optional($messages->last()?->created_at)->format('Y/m/d H:i');

        return view('messages.user-show', [
            'user' => $user,
            'messages' => $messages,
            'latestMessageId' => $latestMessageId,
            'partnerProfile' => $partnerProfile,
            'partnerAvatarUrl' => $partnerAvatarUrl,
            'partnerDisplayName' => $partnerDisplayName,
            'partnerJobType' => $partnerJobType,
            'totalMessagesCount' => $totalMessagesCount,
            'receivedMessagesCount' => $receivedMessagesCount,
            'sentMessagesCount' => $sentMessagesCount,
            'lastMessageAt' => $lastMessageAt,
        ]);
    }

    /**
     * 募集に紐づかないユーザー同士のメッセージを送信する
     */
    public function storeUser(Request $request, User $user): RedirectResponse|JsonResponse
    {
        abort_unless(Auth::check(), 403);

        // 自分自身には送れない
        abort_if((int) Auth::id() === (int) $user->id, 403);

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
            $senderProfile = $message->sender?->profile;

            return response()->json([
                'message' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $senderProfile?->display_name
                        ?? $message->sender?->name
                        ?? 'ユーザー',
                    'is_mine' => true,
                    'created_at' => $message->created_at->format('Y/m/d H:i'),
                    'created_time' => $message->created_at->format('H:i'),
                    'read_label' => $message->read_at ? '既読' : '未読',
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
        $loginUser = $request->user();

        if (! $loginUser) {
            return response()->json([
                'messages' => [],
                'error' => 'ログインが必要です。',
            ], 401);
        }

        // 自分自身とのメッセージ取得は不可
        if ((int) $loginUser->id === (int) $user->id) {
            return response()->json([
                'messages' => [],
                'error' => '自分自身とのメッセージは取得できません。',
            ], 403);
        }

        $afterId = max(0, (int) $request->query('after_id', 0));

        $messages = Message::query()
            ->with(['sender.profile'])
            ->where('id', '>', $afterId)
            ->where(function ($query) use ($loginUser, $user) {
                $query->where(function ($query) use ($loginUser, $user) {
                    $query->where('sender_id', $loginUser->id)
                        ->where('receiver_id', $user->id);
                })->orWhere(function ($query) use ($loginUser, $user) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', $loginUser->id);
                });
            })
            ->oldest('id')
            ->get();

        $receivedMessageIds = $messages
            ->where('receiver_id', $loginUser->id)
            ->pluck('id');

        if ($receivedMessageIds->isNotEmpty()) {
            Message::query()
                ->whereIn('id', $receivedMessageIds)
                ->whereNull('read_at')
                ->update([
                    'read_at' => now(),
                ]);
            // 開いている会話の新着通知も既読にする
        $this->markMessageNotificationsAsRead($loginUser, $user);
        }

        return response()->json([
            'messages' => $messages->map(function (Message $message) use ($loginUser) {
                $sender = $message->sender;
                $senderProfile = $sender?->profile;

                $senderAvatarPath = $senderProfile?->avatar_path;

                $senderAvatarUrl = $senderAvatarPath
                    ? asset('storage/' . ltrim($senderAvatarPath, '/'))
                    : asset('images/default-avatar.png');

                return [
                    'id' => $message->id,
                    'body' => $message->body ?? '',
                    'sender_id' => $message->sender_id,
                    'sender_name' => $senderProfile?->display_name
                        ?? $sender?->name
                        ?? 'ユーザー',
                    'sender_avatar_url' => $senderAvatarUrl,
                    'is_mine' => (int) $message->sender_id === (int) $loginUser->id,
                    'created_at' => optional($message->created_at)->format('Y/m/d H:i') ?? '',
                    'created_time' => optional($message->created_at)->format('H:i') ?? '',
                    'read_label' => $message->read_at ? '既読' : '未読',
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

        abort_if((int) $validated['receiver_id'] === (int) Auth::id(), 403);

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

    /**
     * スマホ表示用の時刻表記
     */
    private function formatSpMessageTime(Message $message): string
    {
        if (! $message->created_at) {
            return '';
        }

        if ($message->created_at->isToday()) {
            return $message->created_at->format('H:i');
        }

        if ($message->created_at->isYesterday()) {
            return '昨日';
        }

        return $message->created_at->format('n/j');
    }

    /**
     * 返信率を実データから算出する
     *
     * 自分がメッセージを送った会話のうち、
     * 相手から1件以上返信がある会話の割合。
     */
    private function calculateReplyRate(int $loginUserId): int
    {
        $sentPartnerIds = Message::query()
            ->where('sender_id', $loginUserId)
            ->distinct()
            ->pluck('receiver_id');

        $sentConversationCount = $sentPartnerIds->count();

        if ($sentConversationCount === 0) {
            return 0;
        }

        $repliedConversationCount = Message::query()
            ->where('receiver_id', $loginUserId)
            ->whereIn('sender_id', $sentPartnerIds)
            ->distinct()
            ->count('sender_id');

        return (int) round(($repliedConversationCount / $sentConversationCount) * 100);
    }

    /**
     * メッセージ一覧の最新状態を取得する
     */
    public function latestIndex(Request $request): JsonResponse
    {
        $loginUser = $request->user();

        if (! $loginUser) {
            return response()->json([
                'items' => [],
                'total_unread_count' => 0,
                'error' => 'ログインが必要です。',
            ], 401);
        }

        $loginUserId = $loginUser->id;

        $allMessages = Message::query()
            ->with(['sender.profile', 'receiver.profile'])
            ->where(function ($query) use ($loginUserId) {
                $query->where('sender_id', $loginUserId)
                    ->orWhere('receiver_id', $loginUserId);
            })
            ->latest('id')
            ->get();

        $latestMessages = $allMessages
            ->groupBy(function (Message $message) use ($loginUserId) {
                return (int) $message->sender_id === (int) $loginUserId
                    ? $message->receiver_id
                    : $message->sender_id;
            })
            ->map(function ($group) {
                return $group->first();
            })
            ->values();

        $unreadCountsByPartner = Message::query()
            ->selectRaw('sender_id, COUNT(*) as unread_count')
            ->where('receiver_id', $loginUserId)
            ->whereNull('read_at')
            ->groupBy('sender_id')
            ->pluck('unread_count', 'sender_id');

        $items = $latestMessages
            ->map(function (Message $message) use ($loginUserId, $unreadCountsByPartner) {
                $partner = (int) $message->sender_id === (int) $loginUserId
                    ? $message->receiver
                    : $message->sender;

                if (! $partner) {
                    return null;
                }

                $profile = $partner->profile;

                $avatarPath = $profile?->avatar_path;
                $avatarUrl = $avatarPath
                    ? asset('storage/' . ltrim($avatarPath, '/'))
                    : asset('images/default-avatar.png');

                $displayName = $profile?->display_name
                    ?? $partner->name
                    ?? 'ユーザー';

                $jobType = $profile?->job_type
                    ?? '職種未設定';

                $isMine = (int) $message->sender_id === (int) $loginUserId;

                return [
                    'partner_id' => $partner->id,
                    'show_url' => route('messages.users.show', $partner),
                    'latest_message_id' => $message->id,
                    'display_name' => $displayName,
                    'job_type' => $jobType,
                    'avatar_url' => $avatarUrl,
                    'unread_count' => (int) ($unreadCountsByPartner[$partner->id] ?? 0),
                    'is_mine' => $isMine,
                    'last_body' => \Illuminate\Support\Str::limit($message->body, 80),
                    'pc_time' => optional($message->created_at)->format('Y/m/d H:i') ?? '',
                    'sp_time' => optional($message->created_at)->format('H:i') ?? '',
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'items' => $items,
            'total_unread_count' => $items->sum('unread_count'),
            'latest_message_id' => $latestMessages->max('id') ?? 0,
        ]);
    }


    /**
     * 対象ユーザーとのメッセージ通知を既読にする
     */
    private function markMessageNotificationsAsRead(User $loginUser, User $partnerUser): void
    {
        $loginUser->unreadNotifications()
            ->where(function ($query) use ($partnerUser) {
                $query
                    ->where('data->type', 'message')
                    ->where('data->sender_id', $partnerUser->id);
            })
            ->update([
                'read_at' => now(),
            ]);
    }
}
