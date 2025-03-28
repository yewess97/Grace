<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

class ResetPasswordController extends Controller
{
    /**
     * Auth Controller Constructor.
     *
     * @param AuthService $authService
     */
    final public function __construct(private readonly AuthService $authService){}

    /**
     * Display the reset password form.
     *
     * @return Application|Factory|View
     * @throws Throwable
     */
    final public function index(): Application|Factory|View
    {
        $token                     = request()?->input(TOKEN);
        $email                     = request()?->input(EMAIL);
        $reset_password_user_error = static fn(string $attributeName) => formError(RESET_PASSWORD, USER_MODEL, $attributeName);

        return showView(RESET_PASSWORD_VIEW, compact(RESET_PASSWORD_USER_ERROR, TOKEN, EMAIL));
    }

    /**
     * Reset user's password.
     *
     * @return JsonResponse
     * @throws ValidationException|InvalidArgumentException|ModelNotFoundException
     */
    final public function resetPassword(): JsonResponse
    {
        $reset_password = $this->authService->resetPasswordUser();

        return !$reset_password
            ? responseError(RESET_PASSWORD.'_failed')
            : responseSuccess(RESET_PASSWORD.'_success');
    }
}
