<?php

use App\Http\Controllers\Auth\ForgotPasswordController,
    App\Http\Controllers\Auth\LoginController,
    App\Http\Controllers\Auth\RegisterController,
    App\Http\Controllers\Auth\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
|
| Here is the registration of the guest routes for the Grace application.
| These routes are loaded by the RouteServiceProvider within a group,
| which contains the "web" middleware group, and "guest" route middleware.
*/


/**
 * Register Routes
 */
guestControllerRoutes(RegisterController::class, REGISTER);


/**
 * Login Routes
 */
guestControllerRoutes(LoginController::class, LOGIN);


/**
 * Forgot Password Routes
 */
guestControllerRoutes(ForgotPasswordController::class, FORGOT_PASSWORD);


/**
 * Reset Password Routes
 */
guestControllerRoutes(ResetPasswordController::class, RESET_PASSWORD);
