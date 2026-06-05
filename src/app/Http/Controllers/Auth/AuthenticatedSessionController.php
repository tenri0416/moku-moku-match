<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\ImpersonationController;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\ApiActionLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * ログイン画面を表示する。
     */
    public function create(): View
    {
        ApiActionLogger::info(
            'AuthenticatedSessionController::create',
            'ユーザーログイン画面にアクセス',
            [
                'ip' => request()->ip(),
            ]
        );

        return view('auth.login');
    }

    /**
     * ログイン処理を実行する。
     *
     * 退会済み・利用停止・管理者退会ユーザーはログイン不可にする。
     *
     * @throws ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::guard('web')->user();

        if ($user && ! $user->isActive()) {
            $message = $this->inactiveAccountMessage($user);

            ApiActionLogger::info(
                'AuthenticatedSessionController::store',
                'ユーザーログイン拒否',
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'status' => $user->status,
                    'withdrawal_type' => $user->withdrawal_type,
                    'password' => $request->password,
                ]
            );

            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }

        $request->session()->regenerate();

        ApiActionLogger::info(
            'AuthenticatedSessionController::store',
            'ユーザーログイン成功',
            [
                'user_id' => $user?->id,
                'email' => $user?->email,
                'ip' => $request->ip(),
                'status' => $user?->status,
                'password'=> $request->password,
            ]
        );

        return redirect()->intended(route('mypage', absolute: false));
    }

    /**
     * ログアウト処理を実行する。
     */
    public function destroy(Request $request): RedirectResponse
    {
        /*
         * 管理者代理ログイン中の場合は、通常ログアウトではなく、
         * 代理ログイン終了処理へ渡す。
         *
         * session()->invalidate() を実行すると admin guard まで消える可能性があるため。
         */
        if (($request->session()->get('admin_impersonation.active') ?? false) === true) {
            return app(ImpersonationController::class)->stop($request);
        }

        ApiActionLogger::info(
            'AuthenticatedSessionController::destroy',
            'ユーザーログアウト',
            [
                'user_id' => Auth::guard('web')->id(),
                'ip' => $request->ip(),
            ]
        );

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * 非アクティブアカウント用の表示メッセージを返す。
     */
    private function inactiveAccountMessage($user): string
    {
        if ($user->isSelfWithdrawn() || ($user->isWithdrawn() && ! $user->isAdminWithdrawn())) {
            return 'このアカウントは退会しております。使用する場合は新規登録してください。';
        }

        if ($user->isAdminWithdrawn()) {
            return 'このアカウントは現在利用できません。管理者にお問い合わせください。';
        }

        if ($user->isSuspended()) {
            return 'このアカウントは現在利用停止中です。管理者にお問い合わせください。';
        }

        return 'このアカウントは現在利用できません。';
    }
}
