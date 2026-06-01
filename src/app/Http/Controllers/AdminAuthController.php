<?php

namespace App\Http\Controllers;

use App\Mail\AdminLoginCodeMail;
use App\Models\Admin;
use App\Models\AdminLoginCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminAuthController extends Controller
{
    /**
     * 管理者ログイン画面を表示する
     */
    public function index()
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
     * 管理者ログイン処理
     *
     * メールアドレス・パスワードが正しい場合でも、
     * ここではまだ管理者ログイン完了にしない。
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $admin = Admin::where('email', $credentials['email'])->first();

        // 管理者が1人もいない、または複数いる場合はログインできないようにする
        if (Admin::count() !== 1) {
            Log::warning('管理者数が不正です', [
                'admin_count' => Admin::count(),
            ]);

            abort(403, '管理者設定が不正です。');
        }

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            Log::info('管理者ログイン失敗', [
                'email' => $request->input('email'),
            ]);

        
            return back()->withErrors([
                'email' => 'メールアドレスまたはパスワードが正しくありません。',
            ])->onlyInput('email');
        }

        // 既存の未使用コードを無効化
        AdminLoginCode::where('admin_id', $admin->id)
            ->whereNull('used_at')
            ->update([
                'used_at' => now(),
            ]);

        // 6桁コード生成
        $code = (string) random_int(100000, 999999);

        AdminLoginCode::create([
            'admin_id' => $admin->id,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
        ]);

        // 2段階認証待ちの管理者IDをセッションに保存
        $request->session()->put('admin_2fa_pending_id', $admin->id);

        // 認証コードを管理者メールに送信
        Mail::to($admin->email)->send(new AdminLoginCodeMail($code));

        Log::info('管理者ログイン認証コード送信', [
            'admin_id' => $admin->id,
            'email' => $admin->email,
        ]);

        return redirect()->route('admin.login.verify')
            ->with('status', '認証コードをメールに送信しました。10分以内に入力してください。');
    }

    /**
     * 認証コード入力画面を表示する
     */
    public function showVerify(Request $request)
    {
        if (!$request->session()->has('admin_2fa_pending_id')) {
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
     * 認証コード確認処理
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $adminId = $request->session()->get('admin_2fa_pending_id');

        if (!$adminId) {
            Log::info('管理者2段階認証コード確認処理にアクセスしたが、セッションにpending_idがない', [
                'session_data' => $request->session()->all(),
            ]);
            return redirect()->route('admin.login');
        }

        $admin = Admin::find($adminId);

        if (!$admin) {
            $request->session()->forget('admin_2fa_pending_id');

            return redirect()->route('admin.login');
        }

        $loginCode = AdminLoginCode::where('admin_id', $admin->id)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$loginCode) {
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

        if (!Hash::check($request->input('code'), $loginCode->code_hash)) {
            return back()->withErrors([
                'code' => '認証コードが正しくありません。',
            ]);
        }

        $loginCode->update([
            'used_at' => now(),
        ]);

        // ここで初めて管理者ログイン完了にする
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
     * 管理者ログアウト
     */
    public function logout(Request $request)
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
}
