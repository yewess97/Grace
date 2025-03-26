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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
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
        if ($isFilter) {
            $filter_dashboard = new FilterByDatesRequest(FILTER, DASHBOARD, FILTER_BY_DATES_ATTRIBUTES);

            validateAttributes($filter_dashboard);

            $filter_dashboard_dates = $filter_dashboard->dataValues();
        }

        // Get the orders' data
        $orders_completed = Order::whereStatus(array_values(Arr::only(ORDER_STATUS_ENUM, ['Completed']))[0]);
        $orders_shipped_delivered_completed = Order::query()->whereIn(STATUS, array_values(Arr::only(ORDER_STATUS_ENUM, ['Shipped', 'Delivered', 'Completed'])));

        $completed_orders = $orders_completed;
        $shipped_delivered_completed_orders = $orders_shipped_delivered_completed;

        if ($isFilter) {
            $completed_orders = $orders_completed->filterByDates($filter_dashboard_dates);
            $shipped_delivered_completed_orders = $orders_shipped_delivered_completed->filterByDates($filter_dashboard_dates);
        }

        // Orders Metrics
        $orders_metrics = [
            [
                NAME           => 'Sales',
                'icon'         => 'analytics',
                'card_padding' => 'pe-lg-3',
                TOTAL_COST     => $shipped_delivered_completed_orders->allTotalCost(),
                'statistic'    => $shipped_delivered_completed_orders->statisticsInLast24Hours(),
            ],
            [
                NAME           => 'Expenses',
                'icon'         => 'bar_chart',
                'card_padding' => 'px-lg-2',
                TOTAL_COST     => ($isFilter ? Order::filterByDates($filter_dashboard_dates) : Order::query())->allTotalCost(),
                'statistic'    => ($isFilter ? Order::filterByDates($filter_dashboard_dates) : Order::query())->statisticsInLast24Hours(),
            ],
            [
                NAME           => 'Income',
                'icon'         => 'stacked_line_chart',
                'card_padding' => 'ps-lg-3',
                TOTAL_COST     => $completed_orders->allTotalCost(),
                'statistic'    => $completed_orders->statisticsInLast24Hours(),
            ],
        ];

        $orders_metrics = object_from_array($orders_metrics);


        // Orders Count
        $orders_statuses = collect(ORDER_STATUS_ENUM)
            ->map(function (int $value, string $status) use ($isFilter, &$filter_dashboard_dates) {
                $orders = Order::whereStatus($value);

                return (object)[
                    'label'               => $status,
                    STATUS                => $value,
                    ORDERS_TABLE.'_count' => ($isFilter ? $orders->filterByDates($filter_dashboard_dates) : $orders)->count(),
                ];
            })->values();

        // Reviews Count
        $reviews_ratings = collect(REVIEW_RATING_ENUM)
            ->map(function (int $value, string $rating) use ($isFilter, &$filter_dashboard_dates) {
                $reviews = Review::query()->where(RATING, $value);

                return (object)[
                    RATING.'_count'        => count(explode("★", $rating)) - 1,
                    RATING                 => $value,
                    REVIEWS_TABLE.'_count' => $isFilter ? $reviews->filterByDates($filter_dashboard_dates)->count() : $reviews->count(),
                ];
            })->values();


        // Get all users with the total orders (Shipped, Delivered, Completed) for each one
        $users_with_orders = User::query()->select(USER_SELECTED_ATTRIBUTES)
            ->with([ADDRESSES_TABLE => static fn(HasMany $address) => addressCountry($address)])
            ->whereHas(ORDERS_TABLE, static fn() => $shipped_delivered_completed_orders)
            ->withCount([ORDERS_TABLE => static fn() => $shipped_delivered_completed_orders]);

        // Get the countries with the total users registered from each one
        $registered_users_by_country = Address::query()->select(COUNTRY)
            ->selectRaw('COUNT(DISTINCT '.USER_ID.') as '.USERS_TABLE.'_count')
            ->groupBy(COUNTRY);

        // Get the subcategories with the total products in each one
        $subcategories_with_products_count = Subcategory::query()->select(NAME)
            ->withCount(PRODUCTS_TABLE);

        // Apply filtering dynamically
        $apply_filter = static fn($query) => $isFilter
            ? $query->filterByDates($filter_dashboard_dates)
            : $query;

        $users            = $apply_filter($users_with_orders)->fastPaginate(5);
        $registered_users = $apply_filter($registered_users_by_country)->get();
        $subcategories    = $apply_filter($subcategories_with_products_count)->get();

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
}
