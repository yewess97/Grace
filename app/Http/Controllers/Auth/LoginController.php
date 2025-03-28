<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class LoginController extends Controller
{
    /**
     * Auth Controller Constructor.
     *
     * @param AuthService $authService
     */
    final public function __construct(private readonly AuthService $authService){}

    /**
     * Display the login form.
     *
     * @return Application|Factory|View
     * @throws Throwable
     */
    final public function index(): Application|Factory|View
    {
        $auth_action      = LOGIN;
        $login_user_error = static fn(string $attributeName) => formError(LOGIN, USER_MODEL, $attributeName);

        return showView(LOGIN_REGISTER_VIEW, compact(AUTH_ACTION, LOGIN_USER_ERROR));
    }

    /**
     * Login the user.
     *
     * @return JsonResponse|Response
     * @throws ValidationException
     */
    final public function login(): JsonResponse|Response
    {
        return $this->authService->loginUser();
    }
}
