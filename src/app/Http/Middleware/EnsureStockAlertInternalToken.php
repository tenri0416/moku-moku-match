<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStockAlertInternalToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $configured = (string) config('stock_alert.internal_token');
        $provided = (string) ($request->bearerToken() ?? '');

        if ($configured === '' || $provided === '' || !hash_equals($configured, $provided)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
