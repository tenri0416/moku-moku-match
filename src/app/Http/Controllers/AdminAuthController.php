<?php

namespace App\Http\Controllers;

use App\Mail\AdminLoginCodeMail;
use App\Models\Admin;
use App\Models\AdminLoginCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AdminAuthController extends Controller
{
    private const GOOGLE_PROVIDER = 'google';

    /**
     * 管理者ログイン画面を表示する。
     */
    public function index(): RedirectResponse|View
    {
        if (Auth::guard('admin')->check()) {
            Log::info('管理者は既にログインしています', [
                'admin_id' => Auth::guard('admin')->id(),
            ]);

            return redirect()->route('admin.dashboard');
        }

        Log::info('管理者ログイン画面にアクセス');

        return view('admin.auth.login');
    }

    /**
     * 管理者ログイン処理。
     *
     * メールアドレス・パスワードが正しい場合でも、ここではまだ管理者ログイン完了にしない。
     * 通常ログインの場合は、従来どおりメール認証コードによる2段階認証を行う。
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $admin = Admin::where('email', $credentials['email'])->first();


        if (! $admin || ! Hash::check($credentials['password'], $admin->password)) {
            Log::info('管理者ログイン失敗', [
                'email' => $request->input('email'),
            ]);

            return back()->withErrors([
                'email' => 'メールアドレスまたはパスワードが正しくありません。',
            ])->onlyInput('email');
        }

        // 既存の未使用コードを無効化。
        AdminLoginCode::where('admin_id', $admin->id)
            ->whereNull('used_at')
            ->update([
                'used_at' => now(),
            ]);

        // 6桁コード生成。
        $code = (string) random_int(100000, 999999);

        AdminLoginCode::create([
            'admin_id' => $admin->id,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
        ]);

        // 2段階認証待ちの管理者IDをセッションに保存。
        $request->session()->put('admin_2fa_pending_id', $admin->id);

        // 認証コードを管理者メールに送信。
        Mail::to($admin->email)->send(new AdminLoginCodeMail($code));

        Log::info('管理者ログイン認証コード送信', [
            'admin_id' => $admin->id,
            'email' => $admin->email,
        ]);

        return redirect()->route('admin.login.verify')
            ->with('status', '認証コードをメールに送信しました。10分以内に入力してください。');
    }

    /**
     * 管理者Google SSO開始。
     *
     * 通常ユーザー用SSOとは別の /admin/auth/callback を使う。
     */
    public function googleRedirect(Request $request): RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        $callbackUrl = $this->adminGoogleCallbackUrl($request);

        Log::info('管理者Google SSO開始', [
            'callback_url' => $callbackUrl,
        ]);

        return Socialite::driver(self::GOOGLE_PROVIDER)
            ->redirectUrl($callbackUrl)
            ->with([
                // 個人用・管理者用を同じGoogleアカウントで使う場合に、アカウント選択画面を出しやすくする。
                'prompt' => 'select_account',
            ])
            ->redirect();
    }

    /**
     * 管理者Google SSOコールバック。
     *
     * Google認証が成功した場合は、メール認証コードによる2段階認証を省略して admin guard でログインする。
     * ただし、GOOGLE_ADMIN_ALLOWED_EMAILS に含まれるメールアドレス、かつ admins テーブルに存在する管理者だけ許可する。
     */
    public function googleCallback(Request $request): RedirectResponse
    {
        try {
            $callbackUrl = $this->adminGoogleCallbackUrl($request);

            $googleUser = Socialite::driver(self::GOOGLE_PROVIDER)
                ->redirectUrl($callbackUrl)
                ->user();

            $googleEmail = strtolower(trim((string) $googleUser->getEmail()));
            $googleProviderId = (string) $googleUser->getId();

            if ($googleEmail === '') {
                Log::warning('管理者Google SSO失敗：Googleメールアドレスを取得できませんでした');

                return redirect()->route('admin.login')
                    ->withErrors([
                        'email' => 'Googleアカウントからメールアドレスを取得できませんでした。',
                    ]);
            }

            if (! $this->isAllowedAdminGoogleEmail($googleEmail)) {
                Log::warning('管理者Google SSO拒否：許可されていないメールアドレス', [
                    'google_email' => $googleEmail,
                ]);

                return redirect()->route('admin.login')
                    ->withErrors([
                        'email' => 'このGoogleアカウントは管理者ログインに許可されていません。',
                    ]);
            }


            $admin = Admin::query()
                ->whereRaw('LOWER(email) = ?', [$googleEmail])
                ->first();

            if (! $admin) {
                Log::warning('管理者Google SSO拒否：adminsテーブルに対象メールがありません', [
                    'google_email' => $googleEmail,
                ]);

                return redirect()->route('admin.login')
                    ->withErrors([
                        'email' => 'このGoogleアカウントに対応する管理者が登録されていません。',
                    ]);
            }

            $update = [];

            if (Schema::hasColumn('admins', 'provider')) {
                $update['provider'] = self::GOOGLE_PROVIDER;
            }

            if (Schema::hasColumn('admins', 'provider_id')) {
                $update['provider_id'] = $googleProviderId;
            }

            if (Schema::hasColumn('admins', 'email_verified_at') && $admin->email_verified_at === null) {
                $update['email_verified_at'] = now();
            }

            if ($update !== []) {
                $admin->forceFill($update)->save();
            }

            // SSOログインでは2段階認証を省略するため、既存の未使用コードとpendingセッションを無効化する。
            AdminLoginCode::where('admin_id', $admin->id)
                ->whereNull('used_at')
                ->update([
                    'used_at' => now(),
                ]);

            $request->session()->forget('admin_2fa_pending_id');

            Auth::guard('admin')->login($admin);
            $request->session()->regenerate();

            Log::info('管理者Google SSOログイン成功', [
                'admin_id' => $admin->id,
                'email' => $admin->email,
                'google_email' => $googleEmail,
            ]);

            return redirect()->intended(route('admin.dashboard'));
        } catch (Throwable $e) {
            report($e);

            Log::error('管理者Google SSOログイン失敗', [
                'message' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            return redirect()->route('admin.login')
                ->withErrors([
                    'email' => 'Googleログインに失敗しました。時間をおいて再度お試しください。',
                ]);
        }
    }

    /**
     * 認証コード入力画面を表示する。
     */
    public function showVerify(Request $request): RedirectResponse|View
    {
        if (! $request->session()->has('admin_2fa_pending_id')) {
            Log::info('管理者2段階認証コード入力画面にアクセスしたが、セッションにpending_idがない', [
                'session_data' => $request->session()->all(),
            ]);

            return redirect()->route('admin.login');
        }

        Log::info('管理者2段階認証コード入力画面にアクセス', [
            'admin_id' => $request->session()->get('admin_2fa_pending_id'),
        ]);

        return view('admin.auth.verify');
    }

    /**
     * 認証コード確認処理。
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $adminId = $request->session()->get('admin_2fa_pending_id');

        if (! $adminId) {
            Log::info('管理者2段階認証コード確認処理にアクセスしたが、セッションにpending_idがない', [
                'session_data' => $request->session()->all(),
            ]);

            return redirect()->route('admin.login');
        }

        $admin = Admin::find($adminId);

        if (! $admin) {
            $request->session()->forget('admin_2fa_pending_id');

            return redirect()->route('admin.login');
        }

        $loginCode = AdminLoginCode::where('admin_id', $admin->id)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (! $loginCode) {
            $request->session()->forget('admin_2fa_pending_id');

            return redirect()->route('admin.login')
                ->withErrors([
                    'code' => '認証コードが見つかりません。再度ログインしてください。',
                ]);
        }

        if ($loginCode->expires_at->isPast()) {
            $loginCode->update([
                'used_at' => now(),
            ]);

            $request->session()->forget('admin_2fa_pending_id');

            return redirect()->route('admin.login')
                ->withErrors([
                    'email' => '認証コードの有効期限が切れました。再度ログインしてください。',
                ]);
        }

        if (! Hash::check($request->input('code'), $loginCode->code_hash)) {
            return back()->withErrors([
                'code' => '認証コードが正しくありません。',
            ]);
        }

        $loginCode->update([
            'used_at' => now(),
        ]);

        // ここで初めて管理者ログイン完了にする。
        Auth::guard('admin')->login($admin);

        $request->session()->forget('admin_2fa_pending_id');
        $request->session()->regenerate();

        Log::info('管理者2段階認証成功', [
            'admin_id' => $admin->id,
            'email' => $admin->email,
        ]);

        return redirect()->route('admin.dashboard');
    }

    /**
     * 管理者ログアウト。
     */
    public function logout(Request $request): RedirectResponse
    {
        Log::info('管理者ログアウト', [
            'admin_id' => Auth::guard('admin')->id(),
        ]);

        Auth::guard('admin')->logout();

        $request->session()->forget('admin_2fa_pending_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * 管理者Google SSO用コールバックURLを返す。
     *
     * GOOGLE_ADMIN_REDIRECT_URI が設定されていればそれを優先する。
     * 未設定の場合は、現在アクセスしている host から /admin/auth/callback を作る。
     */
    private function adminGoogleCallbackUrl(Request $request): string
    {
        $configured = trim((string) config('services.google.admin_redirect', ''));

        if ($configured !== '') {
            return $configured;
        }

        return rtrim($request->getSchemeAndHttpHost(), '/') . route('admin.auth.callback', [], false);
    }

    /**
     * 管理者Google SSOを許可するメールアドレスか確認する。
     */
    /**
     * 管理者Google SSOを許可するメールアドレスか確認する。
     *
     * GOOGLE_ADMIN_ALLOWED_EMAILS に加えて、
     * services.php の reading_reflection.allowed_emails も管理者Googleログイン許可リストとして使用する。
     */
    /**
     * 管理者Google SSOを許可するメールアドレスか確認する。
     *
     * services.php は変更せず、
     * google.admin_allowed_emails と reading_reflection.allowed_emails の両方を使用する。
     */
    private function isAllowedAdminGoogleEmail(string $email): bool
    {
        $adminAllowedEmails = config('services.google.admin_allowed_emails', []);
        $readingReflectionAllowedEmails = config('services.reading_reflection.allowed_emails', []);

        if (is_string($adminAllowedEmails)) {
            $adminAllowedEmails = explode(',', $adminAllowedEmails);
        }

        if (is_string($readingReflectionAllowedEmails)) {
            $readingReflectionAllowedEmails = explode(',', $readingReflectionAllowedEmails);
        }

        $allowedEmails = collect($adminAllowedEmails)
            ->merge($readingReflectionAllowedEmails)
            ->map(fn($allowedEmail) => strtolower(trim((string) $allowedEmail)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($allowedEmails === []) {
            Log::critical('管理者Google SSOの許可メールが未設定です。', [
                'services_google_admin_allowed_emails' => config('services.google.admin_allowed_emails', []),
                'services_reading_reflection_allowed_emails' => config('services.reading_reflection.allowed_emails', []),
            ]);

            return false;
        }

        return in_array(strtolower(trim($email)), $allowedEmails, true);
    }
}
