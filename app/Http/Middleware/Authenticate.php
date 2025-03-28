<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request
     * @return RedirectResponse|null
     */
    final protected function redirectTo($request): RedirectResponse|null
    {
        return !$request->expectsJson()
            ? to_route(LOGIN)
            : null;
    }
}
