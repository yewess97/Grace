<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserActivity
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
            $expire_time = Carbon::now()->addMinutes(5);
            cache()->put('is_online_'.auth()->id(), true, $expire_time);

            User::whereId(auth()->id())->update([LAST_SEEN => Carbon::now()]);
        }

        return $next($request);
    }
}
