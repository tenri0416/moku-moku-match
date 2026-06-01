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
     * 保存しないIPアドレス
     */
    private array $excludedIpAddresses = [
        '61.86.185.218',
    ];

    /**
     * 保存しないホスト
     *
     * IP直アクセスなど
     */
    private array $excludedHosts = [
        '160.251.253.225',
    ];

    /**
     * 保存しないUser-Agentのキーワード
     *
     * bot / crawler / scan 系
     */
    private array $excludedUserAgentKeywords = [
        'bot',
        'crawler',
        'spider',
        'go-http-client',
        'zgrab',
        'censysinspect',
        'dataprovider.com',
        'chrome privacy preserving prefetch proxy',
    ];

    /**
     * 保存しないpathのキーワード
     *
     * 脆弱性探索・不要ファイル・静的ファイルなど
     */
    private array $excludedPathKeywords = [
        'wp-includes',
        'wp-admin',
        'wp-content',
        'wordpress',
        '.env',
        '.git',
        'xmlrpc.php',
        'security.txt',
        'ads.txt',
        'humans.txt',
        'llms.txt',
        'traffic-advice',
        'favicon.ico',
        'robots.txt',
    ];

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
            if (! $this->shouldSaveAccessLog($request, $response)) {
                return $response;
            }

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
     * アクセスログを保存するか判定する
     */
    private function shouldSaveAccessLog(Request $request, Response $response): bool
    {
        // 指定IPは保存しない
        if (in_array($request->ip(), $this->excludedIpAddresses, true)) {
            return false;
        }

        // IP直アクセスは保存しない
        if (in_array($request->getHost(), $this->excludedHosts, true)) {
            return false;
        }

        // 管理画面は保存しない
        if ($request->is('admin') || $request->is('admin/*')) {
            return false;
        }

        // bot / crawler / scan 系User-Agentは保存しない
        if ($this->isExcludedUserAgent($request)) {
            return false;
        }

        // 脆弱性探索っぽいpathは保存しない
        if ($this->isExcludedPath($request)) {
            return false;
        }

        // 画像・CSS・JSなどは保存しない
        if ($this->isStaticAsset($request)) {
            return false;
        }

        // 404は保存しない
        if ($response->getStatusCode() === 404) {
            return false;
        }

        // メッセージなどの自動取得APIは保存しない
        if (str_contains($request->path(), '/latest')) {
            return false;
        }

        // ログイン済みユーザーの場合
        if ($request->user()) {
            // 同じユーザー・同じpathは2回目以降保存しない
            return ! AccessLog::query()
                ->where('user_id', $request->user()->id)
                ->where('path', $request->path())
                ->exists();
        }

        // 未ログインの場合は、保存したい公開ページだけ保存する
        if (! $this->isGuestSaveTarget($request)) {
            return false;
        }

        // ゲストの場合、同じIP・同じpath・同じUser-Agentは2回目以降保存しない
        return ! AccessLog::query()
            ->whereNull('user_id')
            ->where('ip_address', $request->ip())
            ->where('path', $request->path())
            ->where('user_agent', $request->userAgent())
            ->exists();
    }

    /**
     * 未ログインでも保存したい公開ページか判定する
     */
    private function isGuestSaveTarget(Request $request): bool
    {
        return $request->path() === '/'
            || $request->routeIs('home')
            || $request->routeIs('articles.index')
            || $request->routeIs('articles.show')
            || $request->routeIs('articles.short-show')
            || $request->routeIs('articles.category')
            || $request->routeIs('articles.tag')
            || $request->is('articles')
            || $request->is('articles/*');
    }

    /**
     * 除外対象のUser-Agentか判定する
     */
    private function isExcludedUserAgent(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        foreach ($this->excludedUserAgentKeywords as $keyword) {
            if (str_contains($userAgent, strtolower($keyword))) {
                return true;
            }
        }

        return false;
    }

    /**
     * 除外対象のpathか判定する
     */
    private function isExcludedPath(Request $request): bool
    {
        $path = strtolower($request->path());

        foreach ($this->excludedPathKeywords as $keyword) {
            if (str_contains($path, strtolower($keyword))) {
                return true;
            }
        }

        return false;
    }

    /**
     * 静的ファイルか判定する
     */
    private function isStaticAsset(Request $request): bool
    {
        $path = strtolower($request->path());

        return $request->is('images/*')
            || $request->is('css/*')
            || $request->is('js/*')
            || $request->is('build/*')
            || $request->is('storage/*')
            || str_ends_with($path, '.png')
            || str_ends_with($path, '.jpg')
            || str_ends_with($path, '.jpeg')
            || str_ends_with($path, '.gif')
            || str_ends_with($path, '.svg')
            || str_ends_with($path, '.webp')
            || str_ends_with($path, '.css')
            || str_ends_with($path, '.js')
            || str_ends_with($path, '.ico')
            || str_ends_with($path, '.map')
            || str_ends_with($path, '.woff')
            || str_ends_with($path, '.woff2');
    }

    /**
     * ユーザー種別を取得する
     */
    private function getUserType(Request $request): string
    {
        $user = $request->user();

        if (! $user) {
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
