<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|Response|RedirectResponse
     */
    final public function handle(Request $request, Closure $next): JsonResponse|Response|RedirectResponse
    {
        if (auth()->check()) {
            if (auth()->user()?->isAdmin || auth()->user()?->isMonitor) {
                return $next($request);
            }

            abort(HttpResponse::HTTP_FORBIDDEN);
        }

        abort(HttpResponse::HTTP_UNAUTHORIZED);
    }
}
