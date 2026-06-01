<?php

namespace App\Http\Controllers;

use App\Support\ApiActionLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * 通知一覧を表示する
     */
    public function index(): View
    {
        ApiActionLogger::info(
            'NotificationController::index',
            '通知一覧画面にアクセス',
            [
                'user_id' => Auth::id(),
            ]
        );

        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * 通知を既読にして対象ページへ移動する
     */
    public function show(string $id): RedirectResponse
    {
        ApiActionLogger::info(
            'NotificationController::show',
            '通知詳細へ遷移',
            [
                'user_id' => Auth::id(),
                'notification_id' => $id,
            ]
        );

        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        ApiActionLogger::info(
            'NotificationController::show',
            '通知を既読にしました',
            [
                'user_id' => Auth::id(),
                'notification_id' => $id,
                'redirect_url' => $notification->data['url'] ?? route('notifications.index'),
            ]
        );

        return redirect($notification->data['url'] ?? route('notifications.index'));
    }

    /**
     * すべての通知を既読にする
     */
    public function markAllAsRead(): RedirectResponse
    {
        ApiActionLogger::info(
            'NotificationController::markAllAsRead',
            'すべての通知を既読にする処理開始',
            [
                'user_id' => Auth::id(),
                'unread_count' => Auth::user()->unreadNotifications->count(),
            ]
        );

        Auth::user()->unreadNotifications->markAsRead();

        ApiActionLogger::info(
            'NotificationController::markAllAsRead',
            'すべての通知を既読にしました',
            [
                'user_id' => Auth::id(),
            ]
        );

        return redirect()
            ->route('notifications.index')
            ->with('success', 'すべての通知を既読にしました。');
    }
}
