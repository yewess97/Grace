<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SecurityHeadersPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Closure|Response|RedirectResponse|JsonResponse
     */
    final public function handle(Request $request, Closure $next): Closure|Response|RedirectResponse|JsonResponse
    {
        $response = $next($request);

//        $response->header('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline';");
        $response->header('X-XSS-Protection', '1; mode=block');
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('X-Frame-Options', 'SAMEORIGIN');
        $response->header('Referrer-Policy', 'same-origin');

        return $response;
    }
}
