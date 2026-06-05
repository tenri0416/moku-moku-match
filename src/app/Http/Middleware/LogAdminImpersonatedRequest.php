<?php

namespace App\Http\Middleware;

use App\Models\AdminImpersonationLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogAdminImpersonatedRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $impersonation = $request->session()->get('admin_impersonation', []);

        if (($impersonation['active'] ?? false) === true && $this->shouldLog($request)) {
            $logId = $impersonation['log_id'] ?? null;

            Log::info('管理者代理ログイン中の操作', [
                'admin_id' => $impersonation['admin_id'] ?? null,
                'user_id' => $impersonation['user_id'] ?? null,
                'admin_impersonation_log_id' => $logId,
                'method' => $request->method(),
                'path' => $request->path(),
                'route' => optional($request->route())->getName(),
                'status' => $response->getStatusCode(),
                'ip' => $request->ip(),
            ]);

            if ($logId) {
                AdminImpersonationLog::whereKey($logId)->update([
                    'last_activity_at' => now(),
                ]);
            }
        }

        return $response;
    }

    private function shouldLog(Request $request): bool
    {
        if ($request->is('build/*')
            || $request->is('css/*')
            || $request->is('js/*')
            || $request->is('images/*')
            || $request->is('storage/*')
            || $request->is('favicon.ico')) {
            return false;
        }

        if ($request->routeIs('impersonation.stop')) {
            return false;
        }

        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
    }
}
