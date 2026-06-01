<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ApiActionLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * 管理者の未読通知をすべて既読にする
     */
    public function markAllAsRead(): JsonResponse
    {
        $admin = Auth::guard('admin')->user();

        ApiActionLogger::info(
            'Admin\NotificationController::markAllAsRead',
            '管理者通知をすべて既読にする処理開始',
            [
                'admin_id' => $admin?->id,
                'is_authenticated' => (bool) $admin,
            ]
        );

        if (! $admin) {
            ApiActionLogger::info(
                'Admin\NotificationController::markAllAsRead',
                '未認証のため管理者通知既読処理不可',
                [
                    'admin_id' => null,
                    'is_authenticated' => false,
                ]
            );

            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $unreadCount = $admin->unreadNotifications()->count();

        $admin->unreadNotifications()
            ->update(['read_at' => now()]);

        ApiActionLogger::info(
            'Admin\NotificationController::markAllAsRead',
            '管理者通知をすべて既読にしました',
            [
                'admin_id' => $admin->id,
                'unread_count_before' => $unreadCount,
                'unread_count_after' => 0,
            ]
        );

        return response()->json([
            'message' => '管理者通知を既読にしました。',
            'unread_count' => 0,
        ]);
    }
}
