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
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class SearchController extends Controller
{
    private string|null $search_value;

    /**
     * Search Controller Constructor.
     *
     * @return void
     */
    final public function __construct(private readonly DashboardService $dashboardService) {
        $this->search_value = request()?->input('search_value');
    }

    /**
     * Filter the dashboard by dates.
     *
     * @return Application|Factory|View|string
     * @throws ValidationException|Throwable
     */
    final public function filterDashboard(): Application|Factory|View|string
    {
        return $this->dashboardService->dashboardData(true);
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
     * Filter the product(s).
     *
     * @return Application|Factory|View|JsonResponse
     * @throws ValidationException|NotFoundHttpException|Throwable
     */
    final public function filterProducts(): Application|Factory|View|JsonResponse
    {
        $query_params = Arr::except(request()?->query(), ['page']);

        if ($query_params) {
            $query_params = collect($query_params);
            $products = Product::query()->select(PRODUCT_ITEM_ATTRIBUTES);

            $query_params->each(fn($collectionValue, $collection) =>
            $products->whereHas($collection, function (Builder $product) use ($collectionValue) {
                is_array($collectionValue)
                    ? $product->whereIn(SLUG, $collectionValue)
                    : $product->where(SLUG, $collectionValue);
            }));

            return viewProducts($products->fastPaginate(16));
        }

        $filter_products_request = new ProductRequest(FILTER, PRODUCTS_TABLE, FILTER_PRODUCTS_ATTRIBUTES);

        validateAttributes($filter_products_request);

        $filter_products_request_values = $filter_products_request->dataValues();

        array_walk($filter_products_request_values, static fn(&$filterProductsRequestValue) =>
        is_array($filterProductsRequestValue) ? array_pop($filterProductsRequestValue) : null);

        $products = Product::filter(FILTER_PRODUCTS_ATTRIBUTES, $filter_products_request_values);

        return viewProducts($products);
    }

    /**
     * Search for specified user(s).
     *
     * @param string|null $type
     * @return JsonResponse
     * @throws ValidationException|NotFoundHttpException|Throwable
     */
    final public function searchUsers(string $type = null): JsonResponse
    {
        $filter_users_attribute = array(Arr::last(USER_ATTRIBUTES));

        $users = User::query()->when($type === FILTER, static function ($user) use ($filter_users_attribute) {
            $filter_users_request = new UserRequest(FILTER, USERS_TABLE, $filter_users_attribute);

            validateAttributes($filter_users_request);

            [$role] = $filter_users_request->dataValues();

            return $user->where(ROLE, $role);
        },
            fn($user) => $user->search($this->search_value, [FIRST_NAME, LAST_NAME, EMAIL]));

        $users = $users->fastPaginate(16);

        noResultsException($users);

        return ajaxPaginationResponse($users, ADMIN_USERS_PAGINATION, USERS_TABLE);
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

        return ajaxPaginationResponse($user_addresses, USER_ADDRESSES_PAGINATION, USER_ADDRESSES);
    }

    /**
     * Search or Filter for specified order(s).
     *
     * @param int $status
     * @param string|null $type
     * @return JsonResponse
     * @throws ValidationException|NotFoundHttpException|Throwable
     */
    final public function searchOrders(int $status, string $type = null): JsonResponse
    {
        $orders = Order::query()->latest()
            ->whereStatus($status);

        $orders->when($type === FILTER, function ($order) {
            $filter_orders = new FilterByDatesRequest(FILTER, ORDERS_TABLE, FILTER_BY_DATES_ATTRIBUTES);

            validateAttributes($filter_orders);

            return $order->filterByDates($filter_orders->dataValues());
        },
            fn($order) =>
                $order->search($this->search_value, ORDER_ATTRIBUTES, [USER_MODEL => [FIRST_NAME, LAST_NAME]])
        );

        $orders = $orders->fastPaginate(16);

        noResultsException($orders);

        return ajaxPaginationResponse($orders, ADMIN_ORDERS_PAGINATION, ORDERS_TABLE);
    }

    /**
     * Search for specified review(s).
     *
     * @param string $rating
     * @return JsonResponse
     * @throws NotFoundHttpException|Throwable
     */
    final public function searchReviews(string $rating): JsonResponse
    {
        $reviews = Review::query()->where(RATING, $rating)
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
}
