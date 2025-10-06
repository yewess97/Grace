<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    final public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse|Response|RedirectResponse
     * @throws Throwable
     */
    final public function render($request, Throwable $e): JsonResponse|Response|RedirectResponse
    {
        if ($e instanceof HttpExceptionInterface && !($request->ajax() || $request->is('/api.*'))) {
            $status = $e->getStatusCode();

            return response(view(ERROR_COMPONENT, [
                TITLE           => $this->getErrorPageTitle($status),
                'error_status'  => $status,
                'error_title'   => $this->getErrorTitle($status),
                'error_message' => $this->getErrorMessage($status),
            ]), $status);
        }

        return parent::render($request, $e);
    }

    /**
     * Get the error page title based on the status code.
     *
     * @param int $status
     * @return string
     */
    private function getErrorPageTitle(int $status): string
    {
        return match ($status) {
            400 => '400 Bad Request',
            401 => '401 Unauthorized',
            402 => '402 Payment Required',
            403 => '403 Forbidden',
            404 => '404 Not Found',
            419 => '419 Expired',
            429 => '429 Too Many Requests',
            500 => '500 Internal Server Error',
            503 => '503 Service Unavailable',
        };
    }

    /**
     * Get the error title based on the status code.
     *
     * @param int $status
     * @return string
     */
    private function getErrorTitle(int $status): string
    {
        return match ($status) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Page Not Found',
            419 => 'Page Expired',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
        };
    }

    /**
     * Get the error message based on the status code.
     *
     * @param int $status
     * @return string
     */
    private function getErrorMessage(int $status): string
    {
        return match ($status) {
            400 => "We're sorry but the request you made is invalid. Please check the URL and try again!",
            401 => "We're sorry but you don't have permission to access this page. Please register or login first and try again!",
            402 => "We're sorry but you need to pay to access this page. Please check your payment status and try again!",
            403 => "We're sorry but you are forbidden from accessing this page. Please check your permissions and try again!",
            404 => "We're sorry but the page you are looking for could not be found. Please check the URL and try again!",
            419 => "We're sorry but the page you are looking for has expired. Please refresh the page and try again!",
            429 => "We're sorry but you have made too many requests. Please wait a while and try again!",
            500 => "We're sorry but something went wrong. Please try again later!",
            503 => "We're sorry but the service you requested is currently unavailable. Please try again later!",
        };
    }
}
