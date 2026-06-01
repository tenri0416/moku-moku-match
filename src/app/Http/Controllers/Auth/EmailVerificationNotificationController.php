<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\ApiActionLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            ApiActionLogger::info(
                'EmailVerificationNotificationController::store',
                'ユーザーメールアドレスは既に確認済み',
                [
                    'user_id' => $request->user()->id,
                    'email' => $request->user()->email,
                ]
            );

            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        ApiActionLogger::info(
            'EmailVerificationNotificationController::store',
            'ユーザーメールアドレス確認メール送信',
            [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
            ]
        );

        return back()->with('status', 'verification-link-sent');
    }
}
