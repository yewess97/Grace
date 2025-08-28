<?php

namespace App\Services;

use App\Http\Requests\FilterByDatesRequest;
use App\Models\Address;
use App\Models\Order;
use App\Models\Review;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Throwable;

class DashboardService
{
    /**
     * Dashboard Data.
     *
     * @param bool $isFilter
     * @return Application|Factory|View|JsonResponse|string
     * @throws ValidationException|Throwable
     */
    final public function dashboardData(bool $isFilter = false): Application|Factory|View|JsonResponse|string
    {
        $filter_dashboard_dates = [];

        if ($isFilter) {
            $filter_dashboard = new FilterByDatesRequest(FILTER, DASHBOARD, FILTER_BY_DATES_ATTRIBUTES);

            validateAttributes($filter_dashboard);

            $filter_dashboard_dates = $filter_dashboard->dataValues();
        }

        // Get Orders Metrics
        $orders_metrics = $this->getOrdersMetrics($isFilter, $filter_dashboard_dates)[ORDERS_TABLE.'_metrics'];

        // Get fulfilled orders for further use
        $fulfilled_orders = $this->getOrdersMetrics($isFilter, $filter_dashboard_dates)['fulfilled_'.ORDERS_TABLE];

        // Orders Statuses
        $orders_statuses = $this->getOrdersStatusesData($isFilter, $filter_dashboard_dates);

        // Reviews Ratings
        $reviews_ratings = $this->getReviewsRatingsData($isFilter, $filter_dashboard_dates);

        // Get all users with the total orders (Shipped, Delivered, Completed) for each one
        $users = $this->getCachedData(
            $isFilter,
            $filter_dashboard_dates,
            USERS_TABLE.'_with_'.ORDERS_TABLE,
            300,
            5,
            User::query()
                    ->select(USER_SELECTED_ATTRIBUTES)
                    ->with([ADDRESSES_TABLE => static fn(HasMany $address) => addressCountry($address)])
                    ->whereHas(ORDERS_TABLE, static fn() => $fulfilled_orders)
                    ->withCount([ORDERS_TABLE => static fn() => $fulfilled_orders]),
        );

        // Get the countries with the total users registered from each one
        $registered_users = $this->getCachedData(
            $isFilter,
            $filter_dashboard_dates,
            USERS_TABLE.'_by_'.COUNTRY,
            3600,
            0,
            Address::query()
                    ->select(COUNTRY)
                    ->selectRaw('COUNT(DISTINCT '.USER_ID.') as '.USERS_TABLE.'_count')
                    ->groupBy([COUNTRY]),
        );

        // Get the subcategories with the total products in each one
        $subcategories = $this->getCachedData(
            $isFilter,
            $filter_dashboard_dates,
            SUBCATEGORIES_TABLE.'_with_'.PRODUCTS_TABLE,
            1000,
            0,
            Subcategory::query()
                    ->select(NAME)
                    ->withCount(PRODUCTS_TABLE),
        );

        // Get the filtering error messages
        $filter_dashboard_error = static fn(string $attributeName) => formError(FILTER, DASHBOARD, $attributeName);

        $view_vars = compact(ORDERS_TABLE.'_metrics', ORDERS_TABLE.'_'.pluralize(STATUS), REVIEWS_TABLE.'_'.pluralize(RATING), USERS_TABLE, 'registered_'.USERS_TABLE, SUBCATEGORIES_TABLE, FILTER_DASHBOARD_ERROR);

        $view = view(ADMIN_DASHBOARD_VIEW, $view_vars);

        if (request()?->ajax()) {
            return request()?->input('page')
                ? ajaxPaginationResponse($users, ADMIN_DASHBOARD_PAGINATION, USERS_TABLE)
                : $view->render();
        }

        return $view;
    }

    /**
     * Get Orders Metrics.
     *
     * @param bool $isFilter
     * @param array $filterDashboardDates
     * @return array
     */
    private function getOrdersMetrics(bool $isFilter, array $filterDashboardDates): array
    {
        [$completed_orders, $fulfilled_orders] = collect([
            ['Completed'],
            ['Shipped', 'Delivered', 'Completed']
        ])->map(static fn($status) =>
            Order::whereIn(STATUS, array_values(Arr::only(ORDER_STATUS_ENUM, $status))
        )
        ->when($isFilter, fn($order) => $order->filterByDates($filterDashboardDates)));

        // Metrics calculation
        $metrics = [
            'Sales' => [
                'icon' => 'analytics',
                'data' => $fulfilled_orders,
                'padding' => 'pe-lg-3'
            ],
            'Expenses' => [
                'icon' => 'bar_chart',
                'data' => $this->applyFilter(
                    Order::query(),
                    $isFilter,
                    $filterDashboardDates
                ),
                'padding' => 'px-lg-2'
            ],
            'Income' => [
                'icon' => 'stacked_line_chart',
                'data' => $completed_orders,
                'padding' => 'ps-lg-3'
            ]
        ];

        $orders_metrics = collect($metrics)->map(function ($metric, $name) {
            $orders = $metric['data'];

            return [
                NAME           => $name,
                'icon'         => $metric['icon'],
                'card_padding' => $metric['padding'],
                TOTAL_COST     => cache()->remember(strtolower($name).'_total_cost', 300, static fn() =>
                    $orders->allTotalCost()),
                'statistic'    => cache()->remember(strtolower($name).'_statistic', 300, static fn() =>
                    $orders->statisticsInLast24Hours()),
            ];
        })
        ->values()
        ->all();

        return [
            ORDERS_TABLE.'_metrics'   => object_from_array($orders_metrics),
            'fulfilled_'.ORDERS_TABLE => $fulfilled_orders,
        ];
    }

    /**
     * Get Orders Statuses Data.
     *
     * @param bool $isFilter
     * @param array $filterDashboardDates
     * @return Collection
     */
    private function getOrdersStatusesData(bool $isFilter, array $filterDashboardDates): Collection
    {
        return collect(ORDER_STATUS_ENUM)
            ->map(function (int $value, string $status) use ($isFilter, $filterDashboardDates) {
                $cache_key = $this->allOrFilteredCacheKey($isFilter)."_{$status}_orders_count";

                return cache()->remember($cache_key, 300, function () use ($value, $status, $isFilter, $filterDashboardDates) {
                    $orders = $this->applyFilter(Order::query()->whereStatus($value), $isFilter, $filterDashboardDates);

                    return (object)[
                        'label' => $status,
                        STATUS  => $value,
                        'count' => $orders->count(),
                    ];
                });
            })
            ->values();
    }

    /**
     * Get Reviews Ratings Data.
     *
     * @param bool $isFilter
     * @param array $filterDashboardDates
     * @return Collection
     */
    private function getReviewsRatingsData(bool $isFilter, array $filterDashboardDates): Collection
    {
        return collect(REVIEW_RATING_ENUM)
            ->map(function (int $value, string $rating) use ($isFilter, $filterDashboardDates) {
                $stars = count(explode("★", $rating)) - 1;
                $cache_key = $this->allOrFilteredCacheKey($isFilter)."_{$stars}star_reviews";

                return cache()->remember($cache_key, 300, function () use ($value, $rating, $stars, $isFilter, $filterDashboardDates) {
                    $reviews = $this->applyFilter(
                        Review::query()->where(RATING, $value),
                        $isFilter,
                        $filterDashboardDates
                    );

                    return (object)[
                        RATING.'_count'        => $stars,
                        RATING                 => $value,
                        REVIEWS_TABLE.'_count' => $reviews->count(),
                    ];
                });
            })
            ->values();
    }

    /**
     * Get cached data.
     *
     * @param bool $isFilter
     * @param array $filterDashboardDates
     * @param string $type
     * @param int $ttl
     * @param int $paginationItemsNumber
     * @param Builder $query
     * @return LengthAwarePaginator|EloquentCollection
     */
    private function getCachedData(bool $isFilter, array $filterDashboardDates, string $type, int $ttl, int $paginationItemsNumber, Builder $query): LengthAwarePaginator|EloquentCollection
    {
        return cache()->remember(
            $this->allOrFilteredCacheKey($isFilter)."_{$type}_dashboard".currentPage(),
            $ttl,
            function () use ($query, $isFilter, $filterDashboardDates, $paginationItemsNumber) {
                $query = $this->applyFilter($query, $isFilter, $filterDashboardDates);

                return is_int($paginationItemsNumber) && $paginationItemsNumber > 0
                    ? $query->fastPaginate($paginationItemsNumber)
                    : $query->get();
            }
        );
    }

    /**
     * Get the query or apply filter on it based on the request.
     *
     * @param Builder $query
     * @param bool $isFilter
     * @param array $filterDashboardDates
     * @return Builder
     */
    private function applyFilter(Builder $query, bool $isFilter, array $filterDashboardDates): Builder
    {
        return $isFilter
            ? $query->filterByDates($filterDashboardDates)
            : $query;
    }

    /**
     * Set the first part of the cache key based on whether the data is filtered or not.
     *
     * @param bool $isFilter
     * @return string
     */
    private function allOrFilteredCacheKey(bool $isFilter): string
    {
        return $isFilter ? 'filtered' : 'all';
    }
}
