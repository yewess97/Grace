<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        Blade::directive('userFullName', static fn() =>
            "<?php echo auth()->user()?->{FULL_NAME} ?>"
        );

        /**
         * Format the product price.
         *
         * @param string $price
         * @return string
         */
        Blade::directive('priceFormat', static fn(string $price) =>
            "<?php echo 'EGP '.number_format($price, 2); ?>"
        );

        /**
         * Modal Close Button.
         *
         * @return string
         */
        Blade::directive('modalCloseBtn', static fn() =>
            "<?php
                echo \"
                    <button type='button' role='button' title='Close' class='btn-close' data-mdb-dismiss='modal' aria-label='Close'></button>
                \"
            ?>"
        );

        /**
         * Menu Close Button.
         *
         * @param string $ariaControls
         * @return string
         */
        Blade::directive('menuCloseBtn', static fn(string $ariaControls) =>
            "<?php
                \$__additional_classes = str_contains($ariaControls, ADMIN)
                    ? 'col-1 ms-3'
                    : 'position-absolute top-50';

                echo \"
                    <i role='button' title='Close menu' class='fa-solid fa-circle-xmark nav-menu-close \$__additional_classes text-center rounded-circle' aria-label='Close menu' aria-controls=\".$ariaControls.\"></i>
                \";
            ?>"
        );

        /**
         * Submit Button.
         *
         * @param string $submitBtnArgs
         * @return string
         */
        Blade::directive('submitButton', static fn(string $submitBtnArgs) =>
            "<?php
                \$__btn_name = [$submitBtnArgs][0];

                if (str_contains(\$__btn_name, WISHLIST_MODEL)) {
                    \$__product_id   = [$submitBtnArgs][1] ?? null;
                    \$__title        = wishlistTitleIcon(\$__product_id, TITLE);
                    \$__filling_icon = wishlistTitleIcon(\$__product_id, 'icon');

                    echo \"
                        <div class='add-remove-wishlist'>
                           <div class='form-group'>
                               <button type='button' role='button' title='\$__title' class='btn add-remove-wishlist-btn add-remove-wishlist-lg-btn d-grid place-items-center rounded-1'>
                                    <i class='fa-\$__filling_icon fa-heart'></i>
                               </button>
                           </div>
                        </div>
                    \";
                }
                elseif (str_contains(\$__btn_name, CART_MODEL)) {
                    echo \"
                        <div class='add-cart'>
                           <div class='form-group'>
                               <button type='submit' role='button' title='\".capitalizeAll(\$__btn_name).\"' class='btn add-cart-btn add-cart-lg-btn d-flex justify-content-center align-items-center gap-2 rounded-1'>
                                    <i class='ti ti-shopping-cart'></i>
                                    <span>\".capitalizeAll(\$__btn_name).\"</span>
                               </button>
                           </div>
                        </div>
                    \";
                }
                else {
                    echo \"
                        <div class='modal-footer p-2'>
                            <button type='submit' role='button' title='\".capitalizeAll(\$__btn_name).\"' class='btn'>\".capitalizeAll(\$__btn_name).\"</button>
                        </div>
                    \";
                }
            ?>"
        );

        /**
         * Back Button to a specified route or to the previous page.
         *
         * @param string $backBtnArgs
         * @return string
         */
        Blade::directive('backTo', static fn(string $backBtnArgs) =>
            "<?php
                \$__title        = [$backBtnArgs][0];
                \$__route        = [$backBtnArgs][1] ?? null;
                \$__query_params = [$backBtnArgs][2] ?? null;

                \$__url = isset(\$__route)
                    ? route(\$__route, \$__query_params)
                    : (Route::has(\$__title) ? route(\$__title) : url()->previous());

                echo \"
                    <div class='back-btn'>
                        <a href='\$__url' type='button' role='link' title='Back to \".capitalizeAll(\$__title).\"' class='btn top-back-btn d-flex justify-content-center align-items-center rounded-circle' aria-label='Back to \".capitalizeAll(\$__title).\"'>
                            <i class='fa-solid fa-angle-left'></i>
                        </a>
                    </div>
                \"
            ?>"
        );

        /**
         * Search Form.
         *
         * @param string $searchArgs
         * @return string
         */
        Blade::directive('search', static fn(string $searchArgs) =>
            "<?php
                \$__table        = [$searchArgs][0];
                \$__query_params = [$searchArgs][1] ?? null;
                \$__form_class   = 'grace-form '.match (\$__table) {
                    SEARCH_ORDERS    => 'col-12 col-lg-5 col-md-5',
                    SEARCH_ADDRESSES => 'flex-fill',
                    default          => 'col-12 col-lg-6 col-md-6',
                };

                echo
                \"
                    <form action=\".route(\$__table, \$__query_params).\" method='get' role='form' id='search_form' class='\".\$__form_class.\"' data-no_results=\".imageSource('no-results.png').\">
                        <div class='grace-form-body row col-12'>
                            <div class='form-outline d-flex justify-content-lg-start justify-content-md-start justify-content-sm-center'>
                                <input type='search' inputmode='search' name='search' id='search' class='form-control bg-white rounded-2'>
                                <label for='search' class='form-label'>\".capitalizeAll(str(\$__table)->ltrim(ADMIN)->value()).\"...</label>
                                <i id='clear_search' class='fa-solid fa-xmark clear-search-btn position-absolute top-50 fs-7 text-center rounded-circle cursor-pointer' data-route=\".route(\$__table, \$__query_params).\"></i>
                            </div>
                        </div>
                    </form>
                \";
            ?>"
        );

        /**
         * Clear Search or Filter.
         *
         * @param string $route
         * @return string
         */
        Blade::directive('clearSearchFilter', static fn(string $route) =>
            "<?php
                echo \"
                    <a href=\".$route.\" role='link' id='clear_filter' class='text-decoration-underline lh-base'>Clear Search/\".ucfirst(FILTER).\"</a>
                \"
            ?>"
        );

        /**
         * Collection Buttons.
         *
         * @param string $collectionButtonsArgs
         * @return string
         */
        Blade::directive('collectionButtons', static fn(string $collectionButtonsArgs) =>
            "<?php
                [\$__table_name, \$__route] = [$collectionButtonsArgs];
                \$__query_params            = [$collectionButtonsArgs][2] ?? [];
                \$__main_buttons_class      = 'col-md-4';
                \$__button_class            = 'btn d-flex justify-content-center align-items-center gap-2';

                \$__get_title = static function (string \$__needle, array \$__haystack) use (\$__query_params) {
                    return ucfirst(array_search((int) \$__query_params[\$__needle], \$__haystack, true)).'_';
                };

                \$__button_text = \$__restore_all_selected_button = \$__add_button = \$__subcollection_title = \$__trashed_main_button = '';

                \$__button_text = REMOVE;

                \$__route = !isAdminRoute() ? trim(str_replace(ADMIN.'_', '', \$__route)) : \$__route;

                if (Route::currentRouteName() === ADMIN_ORDERS_ROUTE) {
                    \$__main_buttons_class  = 'col-md-12 mt-3';
                    \$__subcollection_title = \$__get_title(STATUS, ORDER_STATUS_ENUM);
                }

                if (Route::currentRouteName() === ADMIN_REVIEWS_ROUTE) {
                    \$__subcollection_title = \$__get_title(RATING, REVIEW_RATING_ENUM);
                }

                \$__trashed_main_button = \"
                    <a href=\".route(\$__route, [...\$__query_params, CONDITION => TRASHED]).\" type='button' role='link' title='\".capitalizeAll(TRASHED.'_'.\$__subcollection_title.\$__table_name).\"' class='trashed-btn mt-2 \$__button_class' aria-label='\".capitalizeAll(TRASHED.'_'.\$__subcollection_title.\$__table_name).\"'>
                        \".Blade::render('<x-action-icon action='.\$__button_text.'/>').capitalizeAll(TRASHED.'_'.\$__subcollection_title.\$__table_name).\"
                    </a>
                \";

                if (conditionRequest()) {
                    \$__button_text = DELETE;

                    \$__restore_all_selected_button = \"
                        <button type='button' role='button' title='\".capitalizeAll(RESTORE.'_'.\$__subcollection_title.\$__table_name).\"' id='restore_\".\$__table_name.\"_btn' class='restore-btn \$__button_class' data-route=\".route(RESTORE.'_'.\$__table_name).\" data-main=\".route(\$__route, [...\$__query_params, CONDITION => conditionRequest()]).\">
                            \".Blade::render('<x-action-icon action='.RESTORE.'/>').ucfirst(RESTORE).\" all selected
                        </button>
                    \";

                    \$__trashed_main_button = \"
                        <a href=\".route(\$__route, \$__query_params).\" type='button' role='link' title='\".capitalizeAll('Main_'.\$__subcollection_title.\$__table_name).\"' class='main-btn mt-2 \$__button_class' aria-label='\".capitalizeAll('Main_'.\$__subcollection_title.\$__table_name).\"'>
                            <i class='fa-solid fa-circle-left'></i>
                            \".capitalizeAll('Main_'.\$__subcollection_title.\$__table_name).\"
                        </a>
                    \";
                }

                \$__delete_remove_all_selected_button = \"
                    <button type='button' role='button' title='\".capitalizeAll(\$__button_text.'_'.\$__subcollection_title.\$__table_name).\"' id='delete_\".\$__table_name.\"_btn' class='delete-btn \$__button_class' data-route=\".route(DELETE.'_'.\$__table_name).\" data-main=\".route(\$__route, [...\$__query_params, CONDITION => conditionRequest()]).\">
                        \".Blade::render('<x-action-icon action='.\$__button_text.'/>').ucfirst(\$__button_text).\" all selected
                    </button>
                \";

                if (!in_array(Route::currentRouteName(), [ADMIN_ORDERS_ROUTE, ADMIN_REVIEWS_ROUTE]) && conditionRequest() !== TRASHED) {
                    \$__add_button = \"
                        <button type='button' role='button' title='\".capitalizeAll(ADD.'_'.singularize(\$__table_name)).\"' class='add-btn \$__button_class' data-mdb-toggle='modal' data-mdb-target='#add_\".singularize(\$__table_name).\"_modal'>
                            \".Blade::render('<x-action-icon action='.ADD.'/>').capitalizeAll(ADD.'_'.singularize(\$__table_name)).\"
                        </button>
                    \";
                }

                echo \"
                    <article class='col-12 d-flex justify-content-center justify-content-md-end gap-3 \$__main_buttons_class'>
                        <div class='d-flex flex-wrap justify-content-center align-items-center gap-3'>
                            \$__delete_remove_all_selected_button \$__restore_all_selected_button \$__add_button
                        </div>
                    </article>
                    \$__trashed_main_button
                \";
            ?>"
        );

        /**
         * Table Headers.
         *
         * @param string $headers
         * @return string
         */
        Blade::directive('tableHeaders', static fn(string $headers) =>
            "<?php
                \$__table_headers = implode('', array_map(static fn(string \$__header) => '<th scope=\'col\'>'.\$__header.'</th>', [$headers]));
                \$__table_headers = '<th scope=\'col\'>#</th>'.\$__table_headers;

                if (in_array(Route::currentRouteName(), [ADMIN_DASHBOARD_ROUTE, PROFILE], true)) {
                    echo \$__table_headers;
                }
                else {
                    echo \"
                        <th scope='col' class='position-relative'>
                            <input type='checkbox' role='checkbox' id='check_all'>
                            <span role='checkbox' id='custom_check_all' class='custom-check position-absolute top-50 start-50 translate-middle' aria-labelledBy='check_all'></span>
                        </th> \$__table_headers
                        <th scope='col'>Action</th>
                    \";
                }
            ?>"
        );

        /**
         * Check Row Checkbox.
         *
         * @param string $id
         * @return string
         */
        Blade::directive('checkRow',  static fn(string $id) =>
            "<?php
                echo \"
                    <td class='position-relative'>
                        <input type='checkbox' role='checkbox' id='check_row_$id' class='check-row' value='$id'>
                        <span role='checkbox' class='custom-check-row custom-check position-absolute top-50 start-50 translate-middle' aria-labelledBy='check_row_$id'></span>
                    </td>
                \"
            ?>"
        );

        /**
         * Iteration Loop for Table Rows.
         *
         * @return string
         */
        Blade::directive('loopIteration', static fn() =>
            "<?php
                echo \"
                    <td class='row-num'>
                        <p></p>
                    </td>
                \"
            ?>"
        );

        /**
         * Strike the relation if it is trashed.
         *
         * @param string $trashedData
         * @return string
         */
        Blade::directive('strikeIfTrashed', static fn(string $trashedData) =>
            "<?php
                [\$__model, \$__relation] = [$trashedData];

                \$__message = in_array(\$__relation, trashedRelationsData(\$__model->trashedRelations)[TRASHED_RELATIONS], true)
                    ? '<del>'.\$__relation.'</del>'
                    : \$__relation;

                echo \"
                    <p>\$__message</p>
                \";
            ?>"
        );

        /**
         * No Results Found.
         *
         * @param string $emptyArgs
         * @return string
         */
        Blade::directive('noResults', static fn(string $emptyArgs) =>
            "<?php
                [\$__table_name, \$__colspan] = [$emptyArgs];

                in_array(Route::currentRouteName(), [ADMIN_DASHBOARD_ROUTE, PROFILE], true)
                    ? ++\$__colspan
                    : \$__colspan += 3;

                \$__message = 'No '.capitalizeAll((str_contains(url()->current(), ORDERS_TABLE)
                    ? array_search((int) request()?->input(STATUS), ORDER_STATUS_ENUM, true)
                    : request()?->input(STATUS) ?? conditionRequest() ?? '')).' '.capitalizeAll(\$__table_name).' Found';

                echo \"
                    <tr>
                        <td colspan='\$__colspan' class='py-4 fs-6 fw-500 text-muted'>\$__message</td>
                    </tr>
                \";
            ?>"
        );

        /**
         * Pagination Links.
         *
         * @param string $paginationArgs
         * @return string
         */
        Blade::directive('pagination', static fn(string $paginationArgs) =>
            "<?php
                [\$__collection, \$__route] = [$paginationArgs];

                \$__query_params = \Illuminate\Support\Arr::except(request()?->query(), ['_token', 'page']);

                echo with(\$__collection)->links(PAGINATION_COMPONENT, ['route' => route(\$__route, \$__query_params)]);
            ?>"
        );
    }
}
