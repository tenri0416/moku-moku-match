<?php

namespace App\Http\Middleware;

use App\Models\AccessLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LogAccess
{
    /**
     * アクセスログを保存する
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        /** @var Response $response */
        $response = $next($request);

        try {
            AccessLog::create([
                'user_id' => $request->user()?->id,
                'user_type' => $this->getUserType($request),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'path' => $request->path(),
                'route_name' => $request->route()?->getName(),
                'referer' => $request->headers->get('referer'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status_code' => $response->getStatusCode(),
                'duration_ms' => (int) ((microtime(true) - $startTime) * 1000),
                'request_data' => $this->filterRequestData($request),
            ]);
        } catch (Throwable $e) {
            // アクセスログ保存失敗で画面表示を止めない
            report($e);
        }

        return $response;
    }

    /**
     * ユーザー種別を取得する
     */
    private function getUserType(Request $request): string
    {
        $user = $request->user();

        if (!$user) {
            return 'guest';
        }

        if ((int) $user->role === 2) {
            return 'admin';
        }

        return 'user';
    }

    /**
     * 保存してよいリクエスト情報だけに絞る
     */
    private function filterRequestData(Request $request): array
    {
        return $request->except([
            'password',
            'password_confirmation',
            'current_password',
            '_token',
            'token',
            'api_token',
            'remember_token',
        ]);
    }
}
