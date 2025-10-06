<?php

use
    Illuminate\Support\Facades\Route,
    App\Http\Controllers\AdminController,
    App\Http\Controllers\AddressController,
    App\Http\Controllers\OrderController;


// If the url is /admin, then redirect to /admin/dashboard
Route::get('/', static fn() => to_route(ADMIN_DASHBOARD_ROUTE));

Route::controller(AdminController::class)->group(function () {
    basicRoute(DASHBOARD,           ADMIN_DASHBOARD_ROUTE);
    basicRoute(CATEGORIES_TABLE,    ADMIN_CATEGORIES_ROUTE);
    basicRoute(SUBCATEGORIES_TABLE, ADMIN_SUBCATEGORIES_ROUTE);
    basicRoute(PRODUCTS_TABLE,      ADMIN_PRODUCTS_ROUTE);
    basicRoute(USERS_TABLE,         ADMIN_USERS_ROUTE);
    whereInRoute(ORDERS_TABLE,  STATUS, array_values(ORDER_STATUS_ENUM),  ADMIN_ORDERS_ROUTE);
    whereInRoute(REVIEWS_TABLE, RATING, array_values(REVIEW_RATING_ENUM), ADMIN_REVIEWS_ROUTE);
});

Route::controller(AddressController::class)->group(fn() =>
    basicRoute(ADDRESSES_TABLE, ADMIN_USER_ADDRESSES_ROUTE, capitalizeSecond(USER_ADDRESSES))
);

Route::controller(OrderController::class)->group(fn() =>
    basicRoute(ORDER_MODEL, ADMIN_ORDER_DETAILS_ROUTE, capitalizeSecond(ORDER_DETAILS))
);
