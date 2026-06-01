<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Support\ApiActionLogger;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     */
    public function show(): View
    {
        ApiActionLogger::info('ユーザーログアウト','ConfirmablePasswordController::store', request()->all());
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            ApiActionLogger::info('ユーザーパスワード確認失敗', 'ConfirmablePasswordController::store',request()->all());
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());
        ApiActionLogger::info('ユーザーパスワード変更', 'ConfirmablePasswordController::store',request()->all());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
