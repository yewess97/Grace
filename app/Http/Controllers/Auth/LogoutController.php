<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;

class LogoutController extends Controller
{
    /**
     * Auth Controller Constructor.
     *
     * @param AuthService $authService
     */
    final public function __construct(private readonly AuthService $authService){}

    /**
     * Logout the user.
     *
     * @return RedirectResponse
     */
    final public function logout(): RedirectResponse
    {
        $this->authService->logoutUser();

        return redirect(RouteServiceProvider::PRODUCTS_LIST);
    }
}
