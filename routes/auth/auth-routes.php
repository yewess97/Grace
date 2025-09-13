<?php

use App\Http\Controllers\AddressController,
    App\Http\Controllers\Auth\LogoutController,
    App\Http\Controllers\CartController,
    App\Http\Controllers\CheckoutController,
    App\Http\Controllers\OrderController,
    App\Http\Controllers\ReviewController,
    App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Here is the registration of the auth routes for the Grace application.
| These routes are loaded by the RouteServiceProvider within a group,
| which contains the "web" middleware group, and "auth" route middleware.
*/


/**
 * Logout Route
 */
Route::post('/'.LOGOUT, [LogoutController::class, LOGOUT])->name(LOGOUT);


/**
 * Cart Routes
 */
generalControllerRoutes(CartController::class, CART_MODEL);


/**
 * Checkout Route
 */
Route::get('/'.CHECKOUT, [CheckoutController::class, 'index'])->name(CHECKOUT);


/**
 * User Addresses Routes
 */
Route::controller(AddressController::class)->group(function () {
    basicRoute(ADDRESSES_TABLE, USER_ADDRESSES, capitalizeAllFromSecondWord(USER_ADDRESSES));
    Route::match(['post', 'put'], '/'.kebabAll(CREATE_UPDATE_ADDRESS).'/{operation}', STORE_OR_UPDATE)->name(CREATE_UPDATE_ADDRESS);
});

/**
 * Addresses Routes
 */
generalControllerRoutes(AddressController::class, ADDRESS_MODEL);


/**
 * Orders Routes
 */
Route::controller(OrderController::class)->group(function () {
    Route::get('/'.ORDER_MODEL, capitalizeAllFromSecondWord(ORDER_DETAILS))->name(ORDER_DETAILS);
    Route::match(['get', 'post'], '/'.kebabAll(CREATE_ORDER), 'store')->name(CREATE_ORDER);
});


/**
 * User Profile Route
 */
Route::get('/'.PROFILE, [UserController::class, PROFILE])->name(PROFILE);


/**
 * Reviews Routes
 */
generalControllerRoutes(ReviewController::class, REVIEW_MODEL);
