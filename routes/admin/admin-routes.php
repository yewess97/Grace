<?php

use App\Http\Controllers\CategoryController,
    App\Http\Controllers\OrderController,
    App\Http\Controllers\ProductController,
    App\Http\Controllers\SubcategoryController,
    App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is the registration of the admin routes for the Grace application.
| These routes are loaded by the RouteServiceProvider within a group,
| which contains the "web" middleware group, and "auth, admin" route middlewares.
*/


/**
 * Admin Views Routes
 */
require __DIR__. '/main-views.php';


/**
 * Category Routes
 */
generalControllerRoutes(CategoryController::class, CATEGORY_MODEL);


/**
 * Subcategory Routes
 */
generalControllerRoutes(SubcategoryController::class, SUBCATEGORY_MODEL);


/**
 * Product Routes
 */
generalControllerRoutes(ProductController::class, PRODUCT_MODEL);


/**
 * User Routes
 */
generalControllerRoutes(UserController::class, USER_MODEL);


/**
 * Order Routes
 */
generalControllerRoutes(OrderController::class, ORDER_MODEL);


/**
 * Notification Routes
 */
Route::controller(NotificationController::class)->group(function () {
    Route::post('/mark-as-read', 'markAsRead')->name('mark_as_read');
    Route::post('/mark-all-as-read', 'markAllAsRead')->name('mark_all_as_read');
});


/**
 * Search Routes
 */
require __DIR__. '/search.php';
