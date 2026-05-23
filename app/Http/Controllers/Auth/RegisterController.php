<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\AuthService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
use Throwable;

class RegisterController extends Controller
{
    /**
     * Auth Controller Constructor.
     *
     * @param AuthService $authService
     */
    final public function __construct(private readonly AuthService $authService){}

    /**
     * Display the registration form.
     *
     * @return RedirectResponse|Application|Factory|View
     * @throws Throwable
     */
    final public function index(): RedirectResponse|Application|Factory|View
    {
        $auth_action = REGISTER;

        $register_user_error = static fn(string $attributeName) =>
            formError(REGISTER, USER_MODEL, $attributeName);

        return auth()->check()
            ? to_route(HOME)
            : showView(LOGIN_REGISTER_VIEW, compact(AUTH_ACTION, REGISTER_USER_ERROR));
    }

    /**
     * Register a new user.
     *
     * @return JsonResponse|Response
     * @throws ValidationException|CacheInvalidArgumentException
     */
    final public function register(): JsonResponse|Response
    {
        $this->authService->registerUser();

        return responseWithData([STATUS => AUTH_SUCCESS, REDIRECT_TO => RouteServiceProvider::PRODUCTS_LIST]);
    }
}
