<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ApiActionLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleSsoController extends Controller
{
    /**
     * Googleアカウント選択画面へリダイレクトする。
     *
     * 別プロジェクトの成功パターンに合わせ、認証開始URLは /auth/redirect、
     * Googleから戻るURLは /auth/callback に統一する。
     */
    public function redirect(Request $request): RedirectResponse
    {
        $redirectUrl = $this->applyGoogleRedirectUrl($request);

        ApiActionLogger::info(
            'GoogleSsoController::redirect',
            'Googleアカウント選択画面を表示',
            [
                'ip' => $request->ip(),
                'redirect_url' => $redirectUrl,
            ]
        );

        if (! $this->hasGoogleConfig()) {
            ApiActionLogger::info(
                'GoogleSsoController::redirect',
                'Google SSO設定が未設定のため中止',
                [
                    'has_client_id' => filled(config('services.google.client_id')),
                    'has_client_secret' => filled(config('services.google.client_secret')),
                    'redirect' => config('services.google.redirect'),
                ]
            );

            return redirect()
                ->route('login')
                ->with('error', 'Googleログイン設定が未完了です。管理者にお問い合わせください。');
        }

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->with([
                'prompt' => 'select_account',
            ])
            ->redirect();
    }

    /**
     * Google OAuth認証後のコールバックを処理する。
     *
     * SSO認証の場合はGoogle側でメール所有確認済みとみなし、
     * email_verified_at を現在時刻で保存してメール確認を不要にする。
     */
    public function callback(Request $request): RedirectResponse
    {
        $redirectUrl = $this->applyGoogleRedirectUrl($request);

        ApiActionLogger::info(
            'GoogleSsoController::callback',
            'Google SSOコールバック処理開始',
            [
                'ip' => $request->ip(),
                'redirect_url' => $redirectUrl,
            ]
        );

        if (! $this->hasGoogleConfig()) {
            return redirect()
                ->route('login')
                ->with('error', 'Googleログイン設定が未完了です。管理者にお問い合わせください。');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);

            Log::warning('Google SSO callback failed.', [
                'message' => $e->getMessage(),
                'redirect_url' => $redirectUrl,
                'ip' => $request->ip(),
            ]);

            ApiActionLogger::info(
                'GoogleSsoController::callback',
                'Google SSOコールバック取得に失敗',
                [
                    'error_message' => $e->getMessage(),
                    'redirect_url' => $redirectUrl,
                    'ip' => $request->ip(),
                ]
            );

            return redirect()
                ->route('login')
                ->with('error', 'Googleログインに失敗しました。もう一度お試しください。');
        }

        $providerId = (string) $googleUser->getId();
        $email = Str::lower((string) $googleUser->getEmail());
        $name = $this->resolveDisplayName($googleUser->getName(), $googleUser->getNickname(), $email);
        $rawUser = is_array($googleUser->user ?? null) ? $googleUser->user : [];

        if ($providerId === '' || $email === '') {
            ApiActionLogger::info(
                'GoogleSsoController::callback',
                'Google SSOで必要なユーザー情報を取得できないため中止',
                [
                    'has_provider_id' => $providerId !== '',
                    'has_email' => $email !== '',
                    'ip' => $request->ip(),
                ]
            );

            return redirect()
                ->route('login')
                ->with('error', 'Googleアカウントからメールアドレスを取得できませんでした。別の方法でログインしてください。');
        }

        try {
            $user = DB::transaction(function () use ($providerId, $email, $name, $rawUser): User {
                $user = User::query()
                    ->where('provider', 'google')
                    ->where('provider_id', $providerId)
                    ->first();

                if (! $user) {
                    $user = User::query()
                        ->where('email', $email)
                        ->first();
                }

                $familyName = trim((string) Arr::get($rawUser, 'family_name', ''));
                $givenName = trim((string) Arr::get($rawUser, 'given_name', ''));

                if ($user) {
                    $payload = [
                        'email_verified_at' => $user->email_verified_at ?: now(),
                        'provider' => $user->provider ?: 'google',
                        'provider_id' => $user->provider_id ?: $providerId,
                    ];

                    if (blank($user->name)) {
                        $payload['name'] = $name;
                    }

                    // ご指定カラムのコメントに合わせる: first_name=姓、last_name=名
                    if (blank($user->first_name) && $familyName !== '') {
                        $payload['first_name'] = $familyName;
                    }

                    if (blank($user->last_name) && $givenName !== '') {
                        $payload['last_name'] = $givenName;
                    }

                    $user->forceFill($payload)->save();

                    return $user->fresh();
                }

                $user = new User();
                $user->forceFill([
                    'name' => $name,
                    'email' => $email,
                    'password' => null,
                    'role' => 1,
                    'status' => 1,
                    'provider' => 'google',
                    'provider_id' => $providerId,
                    // ご指定カラムのコメントに合わせる: first_name=姓、last_name=名
                    'first_name' => $familyName !== '' ? $familyName : null,
                    'last_name' => $givenName !== '' ? $givenName : null,
                    'email_verified_at' => now(),
                ])->save();

                return $user;
            });
        } catch (Throwable $e) {
            report($e);

            Log::error('Google SSO user persistence failed.', [
                'message' => $e->getMessage(),
                'provider_id' => $providerId,
                'email' => $email,
                'ip' => $request->ip(),
            ]);

            ApiActionLogger::info(
                'GoogleSsoController::callback',
                'Google SSOユーザー保存に失敗',
                [
                    'error_message' => $e->getMessage(),
                    'email' => $email,
                    'ip' => $request->ip(),
                ]
            );

            return redirect()
                ->route('login')
                ->with('error', 'Googleログインのユーザー登録処理に失敗しました。時間をおいて再度お試しください。');
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        ApiActionLogger::info(
            'GoogleSsoController::callback',
            'Google SSOログイン成功',
            [
                'user_id' => $user->id,
                'email' => $user->email,
                'provider' => $user->provider,
            ]
        );

        return redirect()->intended(route('mypage', absolute: false));
    }

    /**
     * Googleへ送る redirect_uri を /auth/callback に固定する。
     *
     * request()->getSchemeAndHttpHost() を使うことで、APP_URLの設定ミスよりも
     * 実際にブラウザで開いている http://localhost:8080 などを優先します。
     */
    private function applyGoogleRedirectUrl(Request $request): string
    {
        $redirectUrl = rtrim($request->getSchemeAndHttpHost(), '/') . '/auth/callback';

        config([
            'services.google.redirect' => $redirectUrl,
        ]);

        return $redirectUrl;
    }

    private function hasGoogleConfig(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }

    private function resolveDisplayName(?string $name, ?string $nickname, string $email): string
    {
        $resolved = trim((string) ($name ?: $nickname));

        if ($resolved !== '') {
            return $resolved;
        }

        return Str::before($email, '@') ?: 'Googleユーザー';
    }
}
