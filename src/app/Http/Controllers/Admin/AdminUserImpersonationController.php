<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminImpersonationLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class AdminUserImpersonationController extends Controller
{
    public function start(Request $request, User $user): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();

        Log::info('管理者代理ログインの開始を試みています。', [
            'admin_id' => $admin?->getAuthIdentifier(),
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);
        abort_unless($admin, 403);

        if (($request->session()->get('admin_impersonation.active') ?? false) === true) {
            return back()->with('error', 'すでに代理ログイン中です。先に現在の代理ログインを終了してください。');
        }

        $adminId = $admin->getAuthIdentifier();
        $adminName = $admin->name ?? $admin->email ?? ('管理者#' . $adminId);
        $userName = $user->profile->display_name ?? $user->name ?? ('ユーザー#' . $user->id);

        $log = AdminImpersonationLog::create([
            'admin_id' => $adminId,
            'user_id' => $user->id,
            'started_at' => now(),
            'last_activity_at' => now(),
            'start_ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'status' => 'active',
        ]);

        $request->session()->put('admin_impersonation', [
            'active' => true,
            'log_id' => $log->id,
            'admin_id' => $adminId,
            'admin_name' => $adminName,
            'user_id' => $user->id,
            'user_name' => $userName,
            'started_at' => now()->toDateTimeString(),
            'return_url' => $this->adminUserShowUrl($user),
        ]);

        // admin guard は維持したまま、一般ユーザー用 web guard だけ対象ユーザーへ切り替えます。
        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        Log::info('管理者代理ログインを開始しました。', [
            'admin_id' => $adminId,
            'user_id' => $user->id,
            'admin_impersonation_log_id' => $log->id,
            'ip' => $request->ip(),
        ]);

        return redirect($this->userHomeUrl())
            ->with('status', $userName . 'さんとして代理ログインしました。');
    }

    private function adminUserShowUrl(User $user): string
    {
        if (Route::has('admin.users.show')) {
            return route('admin.users.show', $user);
        }

        return url('/admin/users/' . $user->id);
    }

    private function userHomeUrl(): string
    {
        foreach (['mypage', 'mypage.index', 'user.mypage', 'user.mypage.index', 'dashboard'] as $routeName) {
            if (Route::has($routeName)) {
                return route($routeName);
            }
        }

        return url('/');
    }
}
