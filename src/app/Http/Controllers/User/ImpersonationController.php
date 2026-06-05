<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AdminImpersonationLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class ImpersonationController extends Controller
{
    public function stop(Request $request): RedirectResponse
    {
        $impersonation = $request->session()->get('admin_impersonation', []);

        if (($impersonation['active'] ?? false) !== true) {
            return redirect($this->adminHomeUrl())
                ->with('status', '代理ログイン中ではありません。');
        }

        $logId = $impersonation['log_id'] ?? null;
        $returnUrl = $impersonation['return_url'] ?? $this->adminHomeUrl();

        if ($logId) {
            AdminImpersonationLog::whereKey($logId)->update([
                'ended_at' => now(),
                'last_activity_at' => now(),
                'end_ip' => $request->ip(),
                'status' => 'ended',
            ]);
        }

        Log::info('管理者代理ログインを終了しました。', [
            'admin_id' => $impersonation['admin_id'] ?? null,
            'user_id' => $impersonation['user_id'] ?? null,
            'admin_impersonation_log_id' => $logId,
            'ip' => $request->ip(),
        ]);

        // ここでは session invalidate はしません。
        // web guard だけログアウトし、admin guard のログイン状態は維持します。
        Auth::guard('web')->logout();

        $request->session()->forget('admin_impersonation');
        $request->session()->regenerateToken();

        return redirect($returnUrl)
            ->with('status', '代理ログインを終了し、管理者画面へ戻りました。');
    }

    private function adminHomeUrl(): string
    {
        foreach (['admin.dashboard', 'admin.home', 'admin.users.index'] as $routeName) {
            if (Route::has($routeName)) {
                return route($routeName);
            }
        }

        return url('/admin');
    }
}
