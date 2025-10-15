<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterByDatesRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UserRequest;
use App\Models\Address;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class SearchController extends Controller
{
    private string|null $search_value, $type;

    /**
     * Search Controller Constructor.
     *
     * @return void
     */
    final public function __construct(private readonly DashboardService $dashboardService) {
        $this->search_value = request()?->input('search_value');
        $this->type         = request()?->input('type');
    }

    /**
     * Search for specified category(ies).
     *
     * @return JsonResponse
     * @throws NotFoundHttpException|Throwable
     */
    final public function searchCategories(): JsonResponse
    {
        $categories = Category::search($this->search_value, [NAME])
            ?->fastPaginate(16);

        noResultsException($categories);

        return ajaxPaginationResponse($categories, ADMIN_CATEGORIES_PAGINATION, CATEGORIES_TABLE);
    }

    /**
     * Search for specified subcategory(ies).
     *
     * @return JsonResponse
     * @throws NotFoundHttpException|Throwable
     */
    final public function searchSubcategories(): JsonResponse
    {
        $subcategories = Subcategory::search($this->search_value, [NAME], [CATEGORIES_TABLE => [NAME]])
            ?->fastPaginate(16);

        noResultsException($subcategories);

        return ajaxPaginationResponse($subcategories, ADMIN_SUBCATEGORIES_PAGINATION, SUBCATEGORIES_TABLE);
    }

    /**
     * Search for specified product(s).
     *
     * @return Application|Factory|View|JsonResponse
     * @throws NotFoundHttpException|Throwable
     */
    final public function searchProducts(): Application|Factory|View|JsonResponse
    {
        $products = Product::query()->latest()
            ->search($this->search_value,
                [NAME, SHORT_DESCRIPTION, LONG_DESCRIPTION, OLD_PRICE, NEW_PRICE, QUANTITY, STATUS],
                [
                    CATEGORIES_TABLE    => [NAME],
                    SUBCATEGORIES_TABLE => [NAME],
                ])
            ?->fastPaginate(16);

        return viewProducts($products);
    }

    /**
     * Search for specified user(s).
     *
     * @return JsonResponse
     * @throws Throwable
     */
    final public function searchUsers(): JsonResponse
    {
        $users = User::query()->when($this->type === FILTER, static function ($user) {
            $filter_users_request = new UserRequest(FILTER, USERS_TABLE, [ROLE]);

            validateAttributes($filter_users_request);

            [$role] = $filter_users_request->dataValues();

            return $user->where(ROLE, $role);
        },
            fn($user) =>
                $user->search($this->search_value, [FIRST_NAME, LAST_NAME, EMAIL])
        )
            ->fastPaginate(16);

        $users_pagination_route = match (Route::currentRouteName()) {
            SEARCH_USERS => SEARCH_USERS,
            default       => ADMIN_USERS_ROUTE,
        };

        noResultsException($users);

        return ajaxPaginationResponse($users, ADMIN_USERS_PAGINATION, USERS_TABLE, compact(USERS_PAGINATION_ROUTE));
    }

    /**
     * Search for specified address(s).
     *
     * @param int $userId
     * @return JsonResponse
     * @throws NotFoundHttpException|Throwable
     */
    final public function searchAddresses(int $userId): JsonResponse
    {
        $user_addresses = Address::query()->where(USER_ID, $userId)
            ->search($this->search_value, ADDRESS_ATTRIBUTES)
            ?->fastPaginate(16);

        noResultsException($user_addresses);

        return ajaxPaginationResponse($user_addresses, USER_ADDRESSES_PAGINATION_PARTIAL, USER_ADDRESSES);
    }

    /**
     * Search or Filter for specified order(s).
     *
     * @return JsonResponse
     * @throws ValidationException|NotFoundHttpException|Throwable
     */
    final public function searchOrders(): JsonResponse
    {
        $status = request()?->input(STATUS);

        $orders = Order::query()->latest()
            ->whereStatus($status)
            ->when($this->type === FILTER, static function ($order) {
            $filter_orders = new FilterByDatesRequest(FILTER, ORDERS_TABLE, FILTER_BY_DATES_ATTRIBUTES);

            validateAttributes($filter_orders);

            return $order->filterByDates($filter_orders->dataValues());
        },
            fn($order) =>
                $order->search($this->search_value, ORDER_ATTRIBUTES, [USER_MODEL => [FIRST_NAME, LAST_NAME]])
        )
            ->fastPaginate(16);

        $orders_pagination_route = match (Route::currentRouteName()) {
            SEARCH_ORDERS => SEARCH_ORDERS,
            default       => ADMIN_ORDERS_ROUTE,
        };

        noResultsException($orders);

        return ajaxPaginationResponse($orders, ADMIN_ORDERS_PAGINATION, ORDERS_TABLE, compact(ORDERS_PAGINATION_ROUTE));
    }

    /**
     * Search for specified review(s).
     *
     * @return JsonResponse
     * @throws Throwable
     */
    final public function searchReviews(): JsonResponse
    {
        $reviews = Review::query()->where(RATING, request()?->input(RATING))
            ->search($this->search_value,
                [TITLE, BODY_TEXT],
                [
                    PRODUCT_MODEL => [NAME],
                    USER_MODEL    => [FIRST_NAME, LAST_NAME],
                ])
            ?->fastPaginate(16);

        noResultsException($reviews);

        return ajaxPaginationResponse($reviews, ADMIN_REVIEWS_PAGINATION, REVIEWS_TABLE);
    }

    /**
     * Filter the dashboard by dates.
     *
     * @return Application|Factory|View|JsonResponse|string
     * @throws ValidationException|Throwable
     */
    final public function filterDashboard(): Application|Factory|View|JsonResponse|string
    {
        return $this->dashboardService->dashboardData(true);
    }

    /**
     * Filter the product(s).
     *
     * @return Application|Factory|View|JsonResponse
     * @throws ValidationException|NotFoundHttpException|Throwable
     */
    final public function filterProducts(): Application|Factory|View|JsonResponse
    {
        $products     = Product::query();
        $query_params = Arr::except(request()?->query(), ['page']);

        if (!empty($query_params)) {
            collect($query_params)->each(static fn($collectionValue, $relatedCollection) =>
                $products->whereHas($relatedCollection, static fn(Builder $product) =>
                    is_array($collectionValue)
                        ? $product->whereIn(SLUG, $collectionValue)
                        : $product->where(SLUG, $collectionValue)
                )
            );
        }

        $filter_products_request = new ProductRequest(FILTER, PRODUCTS_TABLE, FILTER_PRODUCTS_ATTRIBUTES);

        if (empty($filter_products_request->data())) {
            $products = $products->select(PRODUCT_ITEM_ATTRIBUTES)
                ->fastPaginate(16);

            return viewProducts($products);
        }

        validateAttributes($filter_products_request);

        session()->forget(FILTER_PRODUCTS);

        $products = $products->filter(FILTER_PRODUCTS_ATTRIBUTES, $filter_products_request->dataValues());

        return viewProducts($products);
    }
}
