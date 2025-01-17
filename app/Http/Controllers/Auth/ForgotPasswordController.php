<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Mail\SentMessage;
use Illuminate\Validation\ValidationException;
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
     * @return Application|Factory|View
     * @throws Throwable
     */
    final public function index(): Application|Factory|View
    {
        $forgot_password_user_error = static fn(string $attributeName) => formError(FORGOT_PASSWORD, USER_MODEL, $attributeName);

        return showView(FORGOT_PASSWORD_VIEW, compact(FORGOT_PASSWORD_USER_ERROR));
    }

    /**
     * Mailing the user to reset his/her password.
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    final public function forgotPassword(): JsonResponse
    {
        $forgot_password = $this->authService->forgotPasswordUser();

        if (!$forgot_password instanceof SentMessage) {
            return responseError("failed_send_".EMAIL);
        }

        return responseSuccess("sent_".EMAIL);
    }
}
