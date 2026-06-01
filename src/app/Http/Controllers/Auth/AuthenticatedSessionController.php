<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use App\Support\ApiActionLogger;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {

        ApiActionLogger::info('
            AuthenticatedSessionController::create',
            'ユーザーログイン画面にアクセス',
            request()->all()
        );
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        ApiActionLogger::info(
            'AuthenticatedSessionController::store',
            'ユーザーログイン成功',
            $request->only(['email'])
        );

        return redirect()->intended(route('mypage', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        ApiActionLogger::info('ユーザーログアウト','AuthenticatedSessionController::store', ['user_id' => Auth::id()]);
        Auth::guard('web')->logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();
    
        return redirect('/');
    }
}
