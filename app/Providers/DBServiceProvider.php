<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class DBServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    final public function boot(): void
    {
        /**
         * Dates Filter.
         *
         * @param array $dates
         * @return Builder
         */
        Builder::macro('filterByDates', fn(array $dates) => $this->whereBetween(DATES[0], $dates));

        /**
         * Orders Statuses Filter.
         *
         * @return Builder
         */
        Builder::macro('whereSDC', fn() => $this->whereIn(STATUS, array_values(Arr::only(ORDER_STATUS_ENUM, ['Shipped', 'Delivered', 'Completed']))));

        /**
         * Sum Total Cost.
         *
         * @return Builder
         */
        Builder::macro('allTotalCost', fn() => $this->sum(TOTAL_COST));

        /**
         * Statistics of orders in the last 24 hours.
         *
         * @return double
         */
        Builder::macro('statisticsInLast24Hours', function () {
            $last_24_hours = now()->subHours(24);
            $orders_count_based_on_time = static fn($order, string $operator) => $order->whereTime(DATES[0], $operator,  $last_24_hours)->count();

            $last_24_hours_orders_count = $orders_count_based_on_time($this, '<');
            $new_orders_count           = $orders_count_based_on_time($this, '>=');

            if ($last_24_hours_orders_count > 0) {
                return ($new_orders_count - $last_24_hours_orders_count) * 100 / $last_24_hours_orders_count;
            }

            return $new_orders_count > 0 ? 100 : 0;
        });

        /**
         * Search.
         *
         * @return Builder
         */
        Builder::macro('search', function ($searchValue, $columns = [], $relations = []) {
            $orGetWhere = static function ($query, $columns, $searchValue) {
                $query->where(function ($subQuery) use ($columns, $searchValue) {
                    foreach ($columns as $column) {
                        $subQuery->orWhere($column, 'LIKE', "%$searchValue%");
                    }
                });
            };

            return $this->where(function ($query) use ($searchValue, $columns, $relations, $orGetWhere) {
                if (isset($columns)) {
                    $orGetWhere($query, $columns, $searchValue);
                }

                if (isset($relations)) {
                    foreach ($relations as $relation => $relationColumns) {
                        $query->orWhereHas($relation, function ($q) use ($searchValue, $relationColumns, $orGetWhere) {
                            $orGetWhere($q, $relationColumns, $searchValue);
                        });
                    }
                }
            });
        });
    }
}
