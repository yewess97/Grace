<?php

use
    Illuminate\Support\Facades\Route,
    App\Http\Controllers\SearchController;


Route::controller(SearchController::class)->group(function () {
    searchRoute(SEARCH_CATEGORIES);
    searchRoute(SEARCH_SUBCATEGORIES);
    searchRoute(ADMIN_SEARCH_PRODUCTS);
    searchRoute(SEARCH_USERS);
    searchRoute(SEARCH_ADDRESSES, '{'. capitalizeSecond(USER_ID).'}');
    searchRoute(SEARCH_ORDERS);
    searchRoute(SEARCH_REVIEWS);
    searchRoute(FILTER_DASHBOARD);
});
