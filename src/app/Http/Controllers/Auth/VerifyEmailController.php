<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\ApiActionLogger;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            ApiActionLogger::info(
                'VerifyEmailController::__invoke',
                'ユーザーメールアドレスは既に確認済み',
                [
                    'user_id' => $request->user()->id,
                    'email' => $request->user()->email,
                ]
            );

            return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            ApiActionLogger::info(
                'VerifyEmailController::__invoke',
                'ユーザーメールアドレス確認成功',
                [
                    'user_id' => $request->user()->id,
                    'email' => $request->user()->email,
                ]
            );

            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
    }
}
