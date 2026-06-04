<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HeaderStatusController extends Controller
{
    /**
     * ヘッダー表示用の最新状態を取得する
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'notification_unread_count' => 0,
                'message_unread_count' => 0,
                'notifications' => [],
            ], 401);
        }

        // メッセージ通知は通知欄から除外する
        $notificationUnreadCount = $user->unreadNotifications()
            ->where(function ($query) {
                $query->whereNull('data->type')
                    ->orWhere('data->type', '!=', 'message');
            })
            ->count();

        $notifications = $user->unreadNotifications()
            ->where(function ($query) {
                $query->whereNull('data->type')
                    ->orWhere('data->type', '!=', 'message');
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data ?? [];

                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? $data['message'] ?? '通知があります',
                    'body' => $data['body'] ?? '',
                    'url' => $data['url'] ?? '#',
                    'created_at' => optional($notification->created_at)->diffForHumans(),
                ];
            })
            ->values();

        // メッセージ未読数は messages テーブルから取得
        $messageUnreadCount = Message::query()
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'notification_unread_count' => $notificationUnreadCount,
            'message_unread_count' => $messageUnreadCount,
            'notifications' => $notifications,
        ]);
    }
}
