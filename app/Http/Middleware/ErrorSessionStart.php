<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\Middleware\StartSession;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ErrorSessionStart
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|Response|RedirectResponse
     * @throws Exception
     */
    final public function handle(Request $request, Closure $next): JsonResponse|Response|RedirectResponse
    {
        $response = $next($request);

        if ($response->exception instanceof HttpExceptionInterface) {
            return app(StartSession::class)->handle($request, fn($req) => $next($req));
        }

        return $response;
    }
}
