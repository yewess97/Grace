<?php

use
    Illuminate\Support\Facades\Route,
    App\Http\Controllers\SearchController;


Route::controller(SearchController::class)->group(function () {
    searchRoute(SEARCH_CATEGORIES);
    searchRoute(SEARCH_SUBCATEGORIES);
    searchRoute(SEARCH_USERS,          true,  '{type?}');
    searchRoute(SEARCH_ADDRESSES,      false, '{'.capitalizeAllFromSecondWord(USER_ID).'}');
    searchRoute(SEARCH_ORDERS,         true,  '{'.STATUS.'}/{type?}');
    searchRoute(SEARCH_REVIEWS,        false, '{'.RATING.'}');
    searchRoute(FILTER_DASHBOARD,      true);
});
