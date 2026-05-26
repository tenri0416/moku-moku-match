<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         if (! $request->user() || ! $request->user()->isAdmin()) {
            // dd('AdminMiddleware: Access denied. User is not an admin.');
            abort(403);
        }
        return $next($request);
    }
}
