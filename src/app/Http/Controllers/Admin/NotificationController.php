<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        if (! $admin) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $admin->unreadNotifications()
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => '管理者通知を既読にしました。',
            'unread_count' => 0,
        ]);
    }
}
