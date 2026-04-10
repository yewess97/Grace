<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class ForgotPasswordController extends Controller
{
    /**
     * Auth Controller Constructor.
     *
     * @param AuthService $authService
     */
    final public function __construct(private readonly AuthService $authService){}

    /**
     * Display the forgot password form.
     *
     * @return RedirectResponse|Application|Factory|View
     * @throws Throwable
     */
    final public function index(): RedirectResponse|Application|Factory|View
    {
        $forgot_password_user_error = static fn(string $attributeName) => formError(FORGOT_PASSWORD, USER_MODEL, $attributeName);

        return auth()->check()
            ? to_route('home')
            : showView(FORGOT_PASSWORD_VIEW, compact(FORGOT_PASSWORD_USER_ERROR));
    }

    /**
     * Mailing the user to reset his/her password.
     *
     * @return JsonResponse
     * @throws ValidationException|ModelNotFoundException|RuntimeException
     */
    final public function forgotPassword(): JsonResponse
    {
        $forgot_password = $this->authService->forgotPasswordUser();

        return !$forgot_password
            ? responseError(FORGOT_PASSWORD.'_failed')
            : responseWithData([STATUS => 'sent_'.EMAIL]);
    }
}
