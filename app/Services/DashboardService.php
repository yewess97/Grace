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
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Throwable;

class DashboardService
{
    /**
     * Dashboard Data.
     *
     * @param bool $isFilter
     * @return Application|Factory|View|string
     * @throws ValidationException|Throwable
     */
    final public function dashboardData(bool $isFilter = false): Application|Factory|View|string
    {
        if ($isFilter) {
            $filter_dashboard = new FilterByDatesRequest(FILTER, DASHBOARD, FILTER_BY_DATES_ATTRIBUTES);

            validateAttributes($filter_dashboard);

            $filter_dashboard_dates = $filter_dashboard->dataValues();
        }

        // Get the orders' data
        $orders_completed = Order::whereStatus(array_values(Arr::only(ORDER_STATUS_ENUM, ['Completed']))[0]);
        $orders_shipped_delivered_completed = Order::whereSDC();

        $completed_orders = $isFilter
            ? $orders_completed->filterByDates($filter_dashboard_dates)
            : $orders_completed;

        $shipped_delivered_completed_orders = $isFilter
            ? $orders_shipped_delivered_completed->filterByDates($filter_dashboard_dates)
            : $orders_shipped_delivered_completed;


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
                TOTAL_COST     => $isFilter ? Order::filterByDates($filter_dashboard_dates)->allTotalCost() : Order::allTotalCost(),
                'statistic'    => $isFilter ? Order::filterByDates($filter_dashboard_dates)->statisticsInLast24Hours() : Order::statisticsInLast24Hours(),
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
                    ORDERS_TABLE.'_count' => $isFilter ? $orders->filterByDates($filter_dashboard_dates)->count() : $orders->count(),
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
        $not_processing_or_cancelled_orders = static fn() => $shipped_delivered_completed_orders;
        $users_with_orders = User::query()->select(USER_SELECTED_ATTRIBUTES)
            ->with([
                ADDRESSES_TABLE => static fn(HasMany $address) => addressCountry($address)
            ])
            ->whereHas(ORDERS_TABLE, $not_processing_or_cancelled_orders)
            ->withCount([ORDERS_TABLE => $not_processing_or_cancelled_orders]);

        $users = $isFilter
            ? $users_with_orders->filterByDates($filter_dashboard_dates)->fastPaginate(5)
            : $users_with_orders->fastPaginate(5);

        // Get the countries with the total users registered from each one
        $registered_users_by_country = Address::query()->select(COUNTRY)
            ->selectRaw('COUNT(DISTINCT '.USER_ID.') as '.USERS_TABLE.'_count')
            ->groupBy(COUNTRY);

        $registered_users = $isFilter
            ? $registered_users_by_country->filterByDates($filter_dashboard_dates)->get()
            : $registered_users_by_country->get();

        // Get the subcategories with the total products in each one
        $subcategories_with_products_count = Subcategory::query()->select(NAME)
            ->withCount(PRODUCTS_TABLE);

        $subcategories = $isFilter
            ? $subcategories_with_products_count->filterByDates($filter_dashboard_dates)->get()
            : $subcategories_with_products_count->get();

        // Get the error messages
        $filter_dashboard_error = static fn(string $attributeName) => formError(FILTER, DASHBOARD, $attributeName);

        $view_vars = compact(ORDERS_TABLE.'_metrics', ORDERS_TABLE.'_'.pluralize(STATUS), REVIEWS_TABLE.'_'.pluralize(RATING), USERS_TABLE, 'registered_'.USERS_TABLE, SUBCATEGORIES_TABLE, FILTER_DASHBOARD_ERROR);

        return request()?->ajax()
            ? view(ADMIN_DASHBOARD_COMPONENT, $view_vars)->render()
            : view(ADMIN_DASHBOARD_VIEW, $view_vars);
    }
}
