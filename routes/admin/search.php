<?php

use
    Illuminate\Support\Facades\Route,
    App\Http\Controllers\SearchController;


Route::controller(SearchController::class)->group(function () {
    searchRoute(SEARCH_CATEGORIES);
    searchRoute(SEARCH_SUBCATEGORIES);
    searchRoute(ADMIN_SEARCH_PRODUCTS);
    searchRoute(SEARCH_USERS,     '{type?}');
    searchRoute(SEARCH_ADDRESSES, '{'.capitalizeAllFromSecondWord(USER_ID).'}');
    searchRoute(SEARCH_ORDERS);
    searchRoute(SEARCH_REVIEWS,   '{'.RATING.'}');
    searchRoute(FILTER_DASHBOARD);
});
