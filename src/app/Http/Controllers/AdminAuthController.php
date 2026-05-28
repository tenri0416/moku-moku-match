<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;



class AdminAuthController extends Controller
{
    public function index()
    {
        Log::info('管理者ログインページにアクセス');
        return view('admin.auth.login');
    }

        /**
     * 管理者ログイン処理
     */
    public function login(Request $request)
    {
        Log::info('管理者ログイン試行', ['email' => $request->input('email')]);
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            Log::info('管理者ログイン成功', [
            'email' => $request->input('email'),
            'admin_check' => Auth::guard('admin')->check(),
            'admin_id' => Auth::guard('admin')->id(),
            'web_check' => Auth::guard('web')->check(),
            'web_id' => Auth::guard('web')->id(),
            ]);


            return redirect()->route('admin.dashboard');
        }


        Log::info('管理者ログイン失敗', ['email' => $request->input('email')]);
        return back()->withErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません。',
        ])->onlyInput('email');
    }

    /**
     * 管理者ログアウト処理
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
