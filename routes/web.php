<?php

use Illuminate\Support\Facades\Route,
    App\Http\Controllers\CategoryController,
    App\Http\Controllers\SubcategoryController,
    App\Http\Controllers\ProductController,
    App\Http\Controllers\HomeController,
    App\Http\Controllers\ReviewController,
    App\Http\Controllers\SearchController,
    App\Http\Controllers\PaymentAboutContactController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is the registration of the web routes for the Grace application.
| These routes are loaded by the RouteServiceProvider within a group,
| which contains the "web" middleware group.
*/


/**
 * Home Route
 */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', static fn() => to_route('home'));


/**
 * Categories Route
 */
Route::get('/'.CATEGORY_MODEL.'/{slug}', [CategoryController::class, 'index'])->name(CATEGORY_MODEL);


/**
 * Subcategories Route
 */
Route::get('/collection/{slug}', [SubcategoryController::class, 'index'])->name(SUBCATEGORY_MODEL);


/**
 * Products Routes
 */
Route::controller(ProductController::class)->prefix('/'.PRODUCTS_TABLE)->group(function () {
    Route::get('/', 'index')->name(PRODUCTS_LIST);
    Route::get('/{slug}', 'show')->name(PRODUCT_DETAILS);
});


/**
 * Reviews Route
 */
Route::get('/'.REVIEWS_TABLE.'/{'.capitalizeAllFromSecondWord(PRODUCT_ID).'}', [ReviewController::class, 'index'])->name(REVIEWS_TABLE);


/**
 * Payment & About-Us & Contact-Us Routes
 */
Route::controller(PaymentAboutContactController::class)->group(function () {
    Route::get('/'.PAYMENT, PAYMENT)->name(PAYMENT);
    Route::get('/'.kebabAll(ABOUT_US), capitalizeAllFromSecondWord(ABOUT_US))->name(ABOUT_US);
    Route::match(['get', 'post'], '/'.kebabAll(CONTACT_US), capitalizeAllFromSecondWord(CONTACT_US))->name(CONTACT_US);
});


/**
 * Search Routes
 */
Route::controller(SearchController::class)->group(function () {
    searchRoute(SEARCH_PRODUCTS, true);
    searchRoute(FILTER_PRODUCTS, true);
});
