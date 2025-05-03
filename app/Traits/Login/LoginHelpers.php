<?php

namespace App\Traits\Login;

use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

trait LoginHelpers
{
    /**
     * Attempt to log the user into the application.
     *
     * @param array $credentialsAttributes
     * @return bool
     */
    private function attemptLogin(array $credentialsAttributes): bool
    {
        return $this->guard()->attempt(
            $credentialsAttributes, request()?->input('remember')
        );
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param string $email
     * @return JsonResponse
     */
    private function sendLoginResponse(string $email): JsonResponse
    {
        request()?->session()->regenerate();

        $this->clearLoginAttempts($email);

        $response_with_redirect_to = static fn($redirection) => responseSuccess(AUTH_SUCCESS, ['redirect_to' => $redirection]);

        return auth()->user()?->isAdmin
            ? $response_with_redirect_to(route(ADMIN_DASHBOARD_ROUTE))
            : $response_with_redirect_to(RouteServiceProvider::PRODUCTS_LIST);
    }

    /**
     * Get the failed login response instance.
     *
     * @return Response
     * @throws ValidationException
     */
    private function sendFailedLoginResponse(): Response
    {
        throw ValidationException::withMessages([
            LOGIN_USER.'_'.INVALID_CREDENTIALS => trans(AUTH_FAILED),
        ])->status(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return StatefulGuard
     */
    private function guard(): StatefulGuard
    {
        return Auth::guard();
    }
}
