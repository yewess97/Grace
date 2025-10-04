<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Subcategory;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdminController extends Controller
{
    /**
     * Admin Dashboard Constructor.
     *
     * @return void
     */
    final public function __construct(private readonly DashboardService $dashboardService, private readonly array $id_name = [ID, NAME]){}

    /**
     * Dashboard.
     *
     * @return Application|Factory|View|JsonResponse|string
     * @throws ValidationException|Throwable
     */
    final public function dashboard(): Application|Factory|View|JsonResponse|string
    {
        return $this->dashboardService->dashboardData();
    }

    /**
     * Categories.
     *
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function categories(): Application|Factory|View|JsonResponse
    {
        // Use the cache()->remember() with generic typing (LengthAwarePaginator).
        // Here, we specify the return type for the closure.
        $categories = cache()->remember(CATEGORIES_PAGINATION_CACHE_KEY, 3600, fn():
            LengthAwarePaginator => Category::when(conditionRequest(), static fn($query) => $query->onlyTrashed())
                ->fastPaginate(16)
        );

        $add_category_error    = static fn(string $attributeName) => formError(ADD, CATEGORY_MODEL, $attributeName);
        $update_category_error = static fn(string $attributeName) => formError(UPDATE, CATEGORY_MODEL, $attributeName);

        return request()?->ajax()
            ? ajaxPaginationResponse($categories, ADMIN_CATEGORIES_PAGINATION, CATEGORIES_TABLE)
            : view(ADMIN_CATEGORIES_VIEW, compact(CATEGORIES_TABLE, ADD_CATEGORY_ERROR, UPDATE_CATEGORY_ERROR));
    }

    /**
     * Subcategories.
     *
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function subcategories(): Application|Factory|View|JsonResponse
    {
        // Use the cache()->remember() with generic typing (LengthAwarePaginator).
        // Here, we specify the return type for the closure.
        $subcategories = cache()->remember(SUBCATEGORIES_PAGINATION_CACHE_KEY, 3600, fn():
            LengthAwarePaginator => Subcategory::with($this->relatedCategories())
                ->when(conditionRequest(), static fn($query) => $query->onlyTrashed())
                ->fastPaginate(16)
        );

        $categories = cache()->remember(CATEGORIES_TABLE.'_for'.SUBCATEGORIES_TABLE, 1800, fn() =>
            Category::get($this->id_name)
        );

        $add_subcategory_error    = static fn(string $attributeName) => formError(ADD,    SUBCATEGORY_MODEL, $attributeName);
        $update_subcategory_error = static fn(string $attributeName) => formError(UPDATE, SUBCATEGORY_MODEL, $attributeName);

        return request()?->ajax()
            ? ajaxPaginationResponse($subcategories, ADMIN_SUBCATEGORIES_PAGINATION, SUBCATEGORIES_TABLE)
            : view(ADMIN_SUBCATEGORIES_VIEW, compact(SUBCATEGORIES_TABLE, CATEGORIES_TABLE, ADD_SUBCATEGORY_ERROR, UPDATE_SUBCATEGORY_ERROR));
    }

    /**
     * Products.
     *
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function products(): Application|Factory|View|JsonResponse
    {
        $id_name_attributes = $this->id_name;

        // Use the cache()->remember() with generic typing (LengthAwarePaginator).
        // Here, we specify the return type for the closure.
        $products = cache()->remember(PRODUCTS_PAGINATION_CACHE_KEY, 1800, fn():
            LengthAwarePaginator => Product::query()
                ->withCount(SUBCATEGORIES_TABLE)
                ->orderBy(SUBCATEGORIES_TABLE.'_count')
                ->latest()
                ->with([
                    ...$this->relatedCategories(),
                    SUBCATEGORIES_TABLE => static fn(BelongsToMany $subcategory) => $subcategory->select($id_name_attributes)->withTrashed(),
                    THUMB_IMAGES        => static fn(HasMany $thumbImage)        => $thumbImage->select(THUMB_IMAGE, PRODUCT_ID),
                    SIZES               => static fn(HasMany $size)              => $size->select(SIZE, PRODUCT_ID),
                ])
                ->when(conditionRequest(), static fn($query) => $query->onlyTrashed())
                ->fastPaginate(16)
        );

        $categories = cache()->remember(CATEGORIES_TABLE.'_for'.PRODUCTS_TABLE, 1800, fn() =>
            Category::get($id_name_attributes)
        );
        $subcategories = cache()->remember(SUBCATEGORIES_TABLE.'_for'.PRODUCTS_TABLE, 1800, fn() =>
            Subcategory::get($id_name_attributes)
        );
        $sizes = PRODUCT_SIZE_ENUM;

        $add_product_error    = static fn(string $attributeName) => formError(ADD, PRODUCT_MODEL, $attributeName);
        $update_product_error = static fn(string $attributeName) => formError(UPDATE, PRODUCT_MODEL, $attributeName);

        return request()?->ajax()
            ? ajaxPaginationResponse($products, ADMIN_PRODUCTS_PAGINATION, PRODUCTS_TABLE)
            : view(ADMIN_PRODUCTS_VIEW, compact(PRODUCTS_TABLE, CATEGORIES_TABLE, SUBCATEGORIES_TABLE, SIZES, ADD_PRODUCT_ERROR, UPDATE_PRODUCT_ERROR));
    }

    /**
     * Customers and Admins Users.
     *
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function users(): Application|Factory|View|JsonResponse
    {
        // Use the cache()->remember() with generic typing (LengthAwarePaginator).
        // Here, we specify the return type for the closure.
        $users = cache()->remember(USERS_PAGINATION_CACHE_KEY, 1800, fn():
            LengthAwarePaginator => User::when(conditionRequest(), static fn($query) => $query->onlyTrashed())
                ->fastPaginate(16)
        );

        $roles = USER_ROLE_ENUM;

        $add_user_error     = static fn(string $attributeName) => formError(ADD,    USER_MODEL,  $attributeName);
        $update_user_error  = static fn(string $attributeName) => formError(UPDATE, USER_MODEL,  $attributeName);
        $filter_users_error = static fn(string $attributeName) => formError(FILTER, USERS_TABLE, $attributeName);

        $users_pagination_route = match (Route::currentRouteName()) {
            SEARCH_USERS => SEARCH_USERS,
            default       => ADMIN_USERS_ROUTE,
        };

        return request()?->ajax()
            ? ajaxPaginationResponse($users, ADMIN_USERS_PAGINATION, USERS_TABLE, compact(USERS_PAGINATION_ROUTE))
            : view(ADMIN_USERS_VIEW, compact(USERS_TABLE, pluralize(ROLE), ADD_USER_ERROR, UPDATE_USER_ERROR, FILTER_USERS_ERROR, USERS_PAGINATION_ROUTE));
    }

    /**
     * Orders.
     *
     * @return RedirectResponse|Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function orders(): RedirectResponse|Application|Factory|View|JsonResponse
    {
        $status = request()?->input(STATUS);

        $last_valid_status = session('last_valid_status');

        if (!$status || !in_array((int) $status, array_values(ORDER_STATUS_ENUM), true)) {
            // Use the last valid status if available, otherwise, get the latest order status
            $redirect_status = $last_valid_status ?? Order::query()->latest()->first()?->{STATUS};

            return to_route(ADMIN_ORDERS_ROUTE, [STATUS => $redirect_status]);
        }

        session()->push('last_valid_status', $status);

        $orders_ids = cache()->remember(ORDERS_PAGINATION_CACHE_KEY.'_'.$status, 1800, fn() =>
            Order::query()->whereStatus($status)
                ->withTrashed()
                ->pluck(ID)
                ->toArray()
        );

        $orders = paginateWithFallback(new Order(), $orders_ids);

        $statuses     = ORDER_STATUS_ENUM;
        $orders_title = key(array_intersect($statuses, (array) $status)).' '.ucfirst(ORDERS_TABLE);
        $order_status = current(array_intersect($statuses, (array) $status));

        $update_order_error  = static fn(string $attributeName) => formError(UPDATE, ORDER_MODEL,  $attributeName);
        $filter_orders_error = static fn(string $attributeName) => formError(FILTER, ORDERS_TABLE, $attributeName);

        $orders_pagination_route = match (Route::currentRouteName()) {
            SEARCH_ORDERS => SEARCH_ORDERS,
            default       => ADMIN_ORDERS_ROUTE,
        };

        return request()?->ajax()
            ? ajaxPaginationResponse($orders, ADMIN_ORDERS_PAGINATION, ORDERS_TABLE, compact(ORDERS_PAGINATION_ROUTE))
            : view(ADMIN_ORDERS_VIEW, compact(ORDERS_TABLE, pluralize(STATUS), ORDERS_TITLE, ORDER_MODEL.'_'.STATUS, UPDATE_ORDER_ERROR, FILTER_ORDERS_ERROR, ORDERS_PAGINATION_ROUTE));
    }

    /**
     * Reviews.
     *
     * @return RedirectResponse|Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function reviews(): RedirectResponse|Application|Factory|View|JsonResponse
    {
        $rating = request()?->input(RATING);

        $last_valid_rating = session('last_valid_rating');

        if (!$rating || !in_array((int) $rating, array_values(REVIEW_RATING_ENUM), true)) {
            // Use the last valid rating if available, otherwise, get the latest review rating
            $redirect_rating = $last_valid_rating ?? Review::query()->latest()->first()?->{RATING};

            return to_route(ADMIN_REVIEWS_ROUTE, [RATING => $redirect_rating]);
        }

        session()->push('last_valid_rating', $rating);

        $reviews_ids = cache()->remember(REVIEWS_PAGINATION_CACHE_KEY.'_'.$rating, 1800, fn() =>
            Review::with([
                PRODUCT_MODEL => fn(BelongsTo $product)     => $product->select($this->id_name),
                USER_MODEL    => static fn(BelongsTo $user) => $user->select(USER_SELECTED_ATTRIBUTES),
            ])
                ->where(RATING, $rating)
                ->withTrashed()
                ->pluck(ID)
                ->toArray()
        );

        $reviews = paginateWithFallback(new Review(), $reviews_ids);

        $review_rating = current(array_intersect(REVIEW_RATING_ENUM, (array) $rating));

        $update_review_error = static fn(string $attributeName) => reviewData(operation: UPDATE, attributeName: $attributeName);

        return request()?->ajax()
            ? ajaxPaginationResponse($reviews, ADMIN_REVIEWS_PAGINATION, REVIEWS_TABLE)
            : view(ADMIN_REVIEWS_VIEW, compact(REVIEWS_TABLE, REVIEW_RATING, UPDATE_REVIEW_ERROR));
    }

    /**
     * Categories Relation.
     *
     * @return array
     */
    private function relatedCategories(): array
    {
        return [
            CATEGORIES_TABLE => fn(BelongsToMany $category) => $category->select($this->id_name)->withTrashed(),
        ];
    }
}
