<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CommonBladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    final public function boot(): void
    {
        /**
         * Get the user full name.
         *
         * @return string
         */
        Blade::directive('userFullName', static fn() => "<?php echo auth()->user()->{FULL_NAME} ?>");

        /**
         * Format the product price.
         *
         * @param string $price
         * @return string
         */
        Blade::directive('price', static fn(string $price) => "<?php echo 'EGP '.number_format($price, 2, '.', ','); ?>");

        /**
         * Modal Close Button.
         *
         * @param string $price
         * @return string
         */
        Blade::directive('modalCloseBtn', static fn() =>
            "<?php echo \"<button type='button' role='button' title='Close' class='btn-close' data-mdb-dismiss='modal' aria-label='Close'></button>\" ?>"
        );

        /**
         * Menu Close Button.
         *
         * @param string $price
         * @return string
         */
        Blade::directive('menuCloseBtn', static function (string $ariaControls) {
            $additional_classes = str_contains($ariaControls, ADMIN)
                ? "col-1 ms-3"
                : "position-absolute top-50";

            return "<?php echo \"<i role='button' title='Close menu' class='fa-solid fa-xmark nav-menu-close $additional_classes fs-7 text-center rounded-circle' aria-label='Close menu' aria-controls=\".$ariaControls.\"></i>\" ?>";
        });

        /**
         * Submit Button.
         *
         * @param string $btnName
         * @return string
         */
        Blade::directive('submitButton', static function (string $btnName) {
            $title = array_from($btnName)[0];

            if (str_contains($title, strtoupper(CART_MODEL))) {
                return "<?php echo \"<div class='add-cart'><div class='form-group'><button type='submit' role='button' title='\".capitalizeAll($title).\"' class='btn add-cart-btn d-flex justify-content-center align-items-center gap-2 rounded-1'><i class='ti ti-shopping-cart'></i><span>\".capitalizeAll($title).\"</span></button></div></div>\" ?>";
            }

            return "<?php echo \"<div class='modal-footer p-2'><button type='submit' role='button' title='\".capitalizeAll($title).\"' class='btn'>\".capitalizeAll($title).\"</button></div>\" ?>";
        });

        /**
         * Make the back button.
         *
         * @param string $title
         * @return string
         */
        Blade::directive('backTo', static function (string $backArgs) {
            [$title, $route] = array_from($backArgs);

            return "<?php echo \"<div class='back-btn'><a href=\".route($route).\" type='button' role='link' title='Back to \".ucfirst($title).\"' class='btn top-back-btn d-flex justify-content-center align-items-center rounded-circle' aria-label='Back to \".ucfirst($title).\"'><i class='fa-solid fa-angle-left'></i></a></div>\" ?>";
        });

        /**
         * Search Form.
         *
         * @param string $searchArgs
         * @return string
         */
        Blade::directive('search', static function (string $searchArgs) {
            $table      = array_from($searchArgs)[0];
            $filtration = in_array(constant($table), [SEARCH_ADDRESSES, SEARCH_ORDERS, SEARCH_REVIEWS], true)
                ? array_from($searchArgs)[1]
                : null;

            return "<?php echo \"<form action='\".route($table, $filtration).\"' method='get' role='form' id='search_form' class='grace-form col-12 \".($table === SEARCH_ORDERS ? 'col-lg-5 col-md-5' : 'col-lg-6 col-md-6').\"' data-no_results=\".imageSource('no-results.png').\"><div class='grace-form-body row col-12'><div class='form-outline d-flex justify-content-lg-start justify-content-md-start justify-content-sm-center'><input type='search' inputmode='search' name='search' id='search' class='form-control bg-white rounded-2'><label for='search' class='form-label'>\".capitalizeAll(str($table)->ltrim(ADMIN)->value()).\"...</label><i id='clear_search' class='fa-solid fa-xmark clear-search-btn position-absolute top-50 fs-7 text-center rounded-circle' data-route=\".route($table, $filtration).\"></i></div></div></form>\" ?>";
        });

        /**
         * Clear Search or Filter.
         *
         * @param string $route
         * @return string
         */
        Blade::directive('clearSearchFilter', static fn(string $route) =>
            "<?php echo \"<a href=\".array_from($route)[0].\" role='link' id='clear_filter' class='text-decoration-underline lh-base'>Clear Search/\".ucfirst(FILTER).\"</a>\" ?>"
        );

        /**
         * Collection Buttons.
         *
         * @param string $buttonsArgs
         * @return string
         */
        Blade::directive('collectionButtons', static function (string $buttonsArgs) {
            [$table_name, $route] = array_from($buttonsArgs);

            return "<?php
                    \$main_buttons_class = 'col-md-4';
                    \$button_class       = 'btn d-flex justify-content-center align-items-center gap-2';
                    \$trash_icon_class   = 'fa-solid fa-trash';
                    \$status             = request()?->input(STATUS);

                    \$get_status_title = static function (\$haystack) use (\$status) {
                        return ucfirst(array_search((int) \$status, \$haystack, true)).'_';
                    };

                    \$button_text = \$restore_all_selected_button = \$add_button = \$status_title = \$trashed_main_button = '';

                    \$button_text = REMOVE;

                    \$route = !isAdminRoute() ? trim(str_replace(ADMIN.'_', '', $route)) : $route;

                    if (Route::currentRouteName() === ADMIN_ORDERS_ROUTE) {
                        \$main_buttons_class = 'col-md-12 mt-3';
                        \$status_title       = \$get_status_title(ORDER_STATUS_ENUM);
                    }

                    if (Route::currentRouteName() === ADMIN_REVIEWS_ROUTE) {
                        \$status_title = \$get_status_title(REVIEW_RATING_ENUM);
                    }

                    \$trashed_main_button = \"<a href=\".route(\$route, [...request()?->input(), CONDITION => TRASHED]).\" type='button' role='link' title='\".capitalizeAll(TRASHED.'_'.\$status_title.$table_name).\"' class='trashed-btn mt-2 \$button_class' aria-label='\".capitalizeAll(TRASHED.'_'.\$status_title.$table_name).\"'><i class='\$trash_icon_class'></i> \".capitalizeAll(TRASHED.'_'.\$status_title.$table_name).\"</a>\";

                    if (request()?->input(CONDITION)) {
                        \$button_text = DELETE;

                        \$restore_all_selected_button = \"<button type='button' role='button' title='\".capitalizeAll(RESTORE.'_'.\$status_title.$table_name).\"' id='restore_\".$table_name.\"_btn' class='restore-btn \$button_class' data-route=\".route(RESTORE.'_'.$table_name).\"><i class='fa-solid fa-rotate-left'></i> \".ucfirst(RESTORE).\" all selected</button>\";

                        \$trashed_main_button = \"<a href=\".route(\$route, [...request()?->except(CONDITION)]).\" type='button' role='link' title='\".capitalizeAll('Main_'.\$status_title.$table_name).\"' class='main-btn mt-2 \$button_class' aria-label='\".capitalizeAll('Main_'.\$status_title.$table_name).\"'><i class='fa-solid fa-circle-left'></i>\".capitalizeAll('Main_'.\$status_title.$table_name).\"</a>\";
                    }

                    \$delete_remove_all_selected_button = \"<button type='button' role='button' title='\".capitalizeAll(\$button_text.'_'.\$status_title.$table_name).\"' id='delete_\".$table_name.\"_btn' class='delete-btn \$button_class' data-route=\".route(DELETE.'_'.$table_name).\"><i class='\$trash_icon_class-can'></i> \".ucfirst(\$button_text).\" all selected</button>\";

                    if (!in_array(Route::currentRouteName(), [ADMIN_ORDERS_ROUTE, ADMIN_REVIEWS_ROUTE]) && request()?->input(CONDITION) !== TRASHED) {
                        \$add_button = \"<button type='button' role='button' title='\".capitalizeAll(ADD.'_'.singularize($table_name)).\"' class='add-btn \$button_class' data-mdb-toggle='modal' data-mdb-target='#add_\".singularize($table_name).\"_modal'><i class='fas fa-plus-circle'></i> \".capitalizeAll(ADD.'_'.singularize($table_name)).\"</button>\";
                    }

                    echo \"
                        <article class='col-12 d-flex justify-content-center justify-content-md-end gap-3 \$main_buttons_class'>
                            <div class='d-flex flex-wrap justify-content-center align-items-center gap-3'>
                                \$delete_remove_all_selected_button \$restore_all_selected_button \$add_button
                            </div>
                        </article>
                        \$trashed_main_button
                    \";
            ?>";
        });

        /**
         * Table Headers.
         *
         * @param string $headers
         * @return string
         */
        Blade::directive('tableHeaders', static function (string $headers) {
            $headers_arr = array_from($headers);
            $table_headers = implode('', array_map(static fn(string $header) => "<th scope='col'>$header</th>", $headers_arr));
            $table_headers = "<th scope='col'>#</th> $table_headers";

            if (in_array(Route::currentRouteName(), [ADMIN_DASHBOARD_ROUTE, PROFILE], true)) {
                return "<?php echo \"$table_headers\" ?>";
            }

            return "<?php echo \"<th scope='col' class='position-relative'><input type='checkbox' role='checkbox' id='check_all'><span role='checkbox' id='custom_check_all' class='custom-check position-absolute top-50 start-50 translate-middle' aria-labelledBy='check_all'></span></th> $table_headers <th scope='col'>Action</th>\" ?>";
        });

        /**
         * Check Row.
         *
         * @param string $id
         * @return string
         */
        Blade::directive('checkRow',  static fn(string $id) =>
            "<?php echo \"<td class='position-relative'><input type='checkbox' role='checkbox' id='check_row_$id' class='check-row' value='$id'><span role='checkbox' class='custom-check-row custom-check position-absolute top-50 start-50 translate-middle' aria-labelledBy='check_row_$id'></span></td>\" ?>"
        );

        /**
         * Iteration Loop for Table Rows.
         *
         * @return string
         */
        Blade::directive('loopIteration', static fn() => "<?php echo \"<td class='row-num'><p></p></td>\" ?>");

        /**
         * No Results Found.
         *
         * @param string $emptyArgs
         * @return string
         */
        Blade::directive('noResults', static function (string $emptyArgs) {
            [$table_name, $colspan] = array_from($emptyArgs);

            in_array(Route::currentRouteName(), [ADMIN_DASHBOARD_ROUTE, PROFILE], true)
                ? ++$colspan
                : $colspan += 3;

            $message_label = "<?php \$message = 'No '.capitalizeAll((str_contains(url()->current(), ORDERS_TABLE) ? array_search((int) request()?->input(STATUS), ORDER_STATUS_ENUM, true) : request()?->input(STATUS) ?? request()?->input(CONDITION) ?? '')).' '.capitalizeAll($table_name).' Found' ?>";

            return $message_label."<?php echo \"<tr><td colspan='$colspan' class='py-4 fs-6 fw-500 text-muted'>\$message</td></tr>\" ?>";
        });

        /**
         * Pagination Links.
         *
         * @param string $paginationArgs
         * @return string
         */
        Blade::directive('pagination', static function (string $paginationArgs) {
            [$collection, $route] = array_from($paginationArgs);

            return "<?php echo with($collection)->links(PAGINATION_COMPONENT, ['route' => route($route, [ID => request()?->input(ID), STATUS => request()?->input(STATUS), RATING => request()?->input(RATING)])]) ?>";
        });
    }
}
