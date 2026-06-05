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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * ユーザー登録画面を表示する。
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
     * ユーザー登録処理を実行する。
     *
     * 自主退会済みユーザーが同じメールアドレスで再登録した場合は、
     * 既存の users レコードを復活させる。
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
                'ip' => $request->ip(),
                'password' => $request->password,
            ]
        );

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $email = Str::lower((string) $request->email);

        $existingUser = User::query()
            ->where('email', $email)
            ->first();

        if ($existingUser && $existingUser->isSuspended()) {
            throw ValidationException::withMessages([
                'email' => 'このメールアドレスのアカウントは現在利用停止中です。管理者にお問い合わせください。',
            ]);
        }

        if ($existingUser && $existingUser->isAdminWithdrawn()) {
            throw ValidationException::withMessages([
                'email' => 'このメールアドレスのアカウントは現在利用できません。管理者にお問い合わせください。',
            ]);
        }

        if ($existingUser && ! $existingUser->isWithdrawn()) {
            throw ValidationException::withMessages([
                'email' => 'このメールアドレスは既に使用されています。ログインしてください。',
            ]);
        }

        $user = DB::transaction(function () use ($request, $email): User {
            $existingUser = User::query()
                ->where('email', $email)
                ->lockForUpdate()
                ->first();

            if ($existingUser && $existingUser->isSuspended()) {
                throw ValidationException::withMessages([
                    'email' => 'このメールアドレスのアカウントは現在利用停止中です。管理者にお問い合わせください。',
                ]);
            }

            if ($existingUser && $existingUser->isAdminWithdrawn()) {
                throw ValidationException::withMessages([
                    'email' => 'このメールアドレスのアカウントは現在利用できません。管理者にお問い合わせください。',
                ]);
            }

            if ($existingUser && ! $existingUser->isWithdrawn()) {
                throw ValidationException::withMessages([
                    'email' => 'このメールアドレスは既に使用されています。ログインしてください。',
                ]);
            }

            if ($existingUser && $existingUser->isWithdrawn()) {
                $existingUser->forceFill([
                    'name' => $request->name,
                    'email' => $email,
                    'password' => Hash::make($request->password),
                    'role' => User::ROLE_USER,
                    'status' => User::STATUS_ACTIVE,
                    'email_verified_at' => null,
                    'remember_token' => null,

                    // 退会情報をクリアして復活
                    'withdrawn_at' => null,
                    'withdrawal_reason' => null,
                    'withdrawal_type' => null,
                    'withdrawn_by_admin_id' => null,

                    // 利用停止情報も念のためクリア
                    'suspended_at' => null,
                    'suspension_reason' => null,
                    'suspended_by_admin_id' => null,
                ])->save();

                return $existingUser->fresh();
            }

            $user = new User();

            $user->forceFill([
                'name' => $request->name,
                'email' => $email,
                'password' => Hash::make($request->password),
                'role' => User::ROLE_USER,
                'status' => User::STATUS_ACTIVE,
            ])->save();

            return $user->fresh();
        });

        /*
         * 管理者通知
         *
         * セキュリティ上、パスワードは通知しない。
         */
        $admins = Admin::query()->get();

        Notification::send($admins, new AdminUserRegisteredNotification($user, $request->password));

        /*
         * LINE通知
         *
         * LINE通知に失敗しても、ユーザー登録処理は止めない。
         * セキュリティ上、パスワードは通知しない。
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
                'is_restored' => $existingUser?->isWithdrawn() ?? false,
                'password' => $request->password
            ]
        );

        return redirect(route('mypage', absolute: false));
    }

    /**
     * 新規ユーザー登録時のLINE通知メッセージを作成する。
     */
    private function buildLineRegisteredMessage(User $user, $password): string
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
            'password' => $password
        ]);
    }
}
