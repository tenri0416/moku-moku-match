<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\ApiActionLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        ApiActionLogger::info(
            'PasswordResetLinkController::create',
            'ユーザーパスワードリセットリンク画面にアクセス'
        );

        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        ApiActionLogger::info(
            'PasswordResetLinkController::store',
            'ユーザーパスワードリセットリンク送信処理開始',
            [
                'email' => $request->email,
            ]
        );

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        ApiActionLogger::info(
            'PasswordResetLinkController::store',
            'ユーザーパスワードリセットリンク送信',
            [
                'email' => $request->email,
                'status' => $status,
            ]
        );

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
