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
        if (auth()->check() && auth()->user()->isAdmin) {
            return $next($request);
        }

        return to_route('home')->with('authError', 'You are not authorized to access this page.')->setStatusCode(HttpResponse::HTTP_UNAUTHORIZED);
    }
}
