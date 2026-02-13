<?php

use App\Http\Controllers\AddressController,
    App\Http\Controllers\Auth\LogoutController,
    App\Http\Controllers\CartController,
    App\Http\Controllers\CheckoutController,
    App\Http\Controllers\OrderController,
    App\Http\Controllers\ReviewController,
    App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;

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
 * Wishlist Routes
 */
generalControllerRoutes(WishlistController::class, WISHLIST_MODEL);
Route::post('/'.kebabAll(CREATE_WISHLIST), [WishlistController::class, 'store'])->name(CREATE_WISHLIST);


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
Route::controller(AddressController::class)->group(fn() =>
    basicRoute(ADDRESSES_TABLE, USER_ADDRESSES, capitalizeSecond(USER_ADDRESSES))
);

/**
 * Addresses Routes
 */
generalControllerRoutes(AddressController::class, ADDRESS_MODEL);


/**
 * Orders Routes
 */
Route::controller(OrderController::class)->group(function () {
    basicRoute(ORDER_MODEL, ORDER_DETAILS, capitalizeSecond(ORDER_DETAILS));
    Route::match(['get', 'post'], '/'.kebabAll(CREATE_ORDER), 'store')->name(CREATE_ORDER);
});


/**
 * User Profile Route
 */
Route::controller(UserController::class)->group(fn() =>
    basicRoute(PROFILE, PROFILE)
);


/**
 * Reviews Routes
 */
generalControllerRoutes(ReviewController::class, REVIEW_MODEL);
