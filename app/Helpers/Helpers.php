<?php

use App\Http\Requests\AuthRequest;
use App\Http\Requests\UserRequest;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Review;
use App\Models\Subcategory;
use App\Models\ThumbImage;
use App\Models\User;
use App\Notifications\NewAdminActionTaken;
use App\Notifications\NewUserRegistered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Route as Routing;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Notifications\Notification as NotificationInstance;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;


if (!function_exists('canonicalUrl')) {
    /**
     * Get the canonical url.
     *
     * @return string
     */
    function canonicalUrl(): string
    {
        if (str($current_url = url()->current())->startsWith('https://www.')) {
            return str_replace('https://www.', 'https://', $current_url);
        }

        return str_replace('https://', 'https://www.', $current_url);
    }
}


if (!function_exists('basicRoute')) {
    /**
     * The basic routes.
     *
     * @param string $url
     * @param string $routeName
     * @param string|null $callbackName
     * @return Routing
     */
    function basicRoute(string $url, string $routeName, ?string $callbackName = null): Routing
    {
        return Route::get("/$url", $callbackName ?? $url)->name($routeName);
    }
}


if (!function_exists('whereInRoute')) {
    /**
     * Routes that have whereIn() constraint.
     *
     * @param string $url
     * @param string $column
     * @param array $values
     * @param string $routeName
     * @return Routing
     */
    function whereInRoute(string $url, string $column, array $values, string $routeName): Routing
    {
        return Route::get("/$url", $url)->whereIn($column, $values)->name($routeName);
    }
}


if (!function_exists('guestControllerRoutes')) {
    /**
     * Generate auth routes for a specific controller.
     *
     * @param string $controller
     * @param string $url
     * @return RouteRegistrar
     */
    function guestControllerRoutes(string $controller, string $url): RouteRegistrar
    {
        $url_kebab     = kebabAll($url);
        $post_method_callback = capitalizeSecond($url);

        return Route::controller($controller)->group(function () use ($url, $url_kebab, $post_method_callback) {
            Route::get('/'.$url_kebab, 'index')->name($url);
            Route::post('/'.$url_kebab, $post_method_callback)->name($url.'_'.USER_MODEL);
        });
    }
}


if (!function_exists('generalControllerRoutes')) {
    /**
     * Generate CRUD routes for a specific controller.
     *
     * @param string $controller
     * @param string $modelName
     * @param string|null $urlParam
     * @return RouteRegistrar
     */
    function generalControllerRoutes(string $controller, string $modelName, ?string $urlParam = null): RouteRegistrar
    {
        $create_or_update_model = CREATE.'_'.UPDATE.'_'.$modelName;
        $edit_model             = EDIT.'_'.$modelName;
        $update_model           = UPDATE.'_'.$modelName;
        $delete_model           = DELETE.'_'.$modelName;
        $restore_model          = RESTORE.'_'.$modelName;

        return Route::controller($controller)->group(function () use ($modelName, $create_or_update_model, $edit_model, $update_model, $delete_model, $restore_model, $urlParam) {
            if ($modelName !== REVIEW_MODEL && !isAdminRoute()) {
                $url = $modelName === CART_MODEL
                    ? $modelName
                    : pluralize($modelName);

                Route::get('/'.kebabAll($modelName).(isset($urlParam) ? "/{{$urlParam}}" : ''), 'index')->name($url);
            }

            Route::match(['post', 'put'], '/'.kebabAll($create_or_update_model).'/{operation}', STORE_OR_UPDATE)->name($create_or_update_model);

            if (in_array($modelName, [ORDER_MODEL, ADDRESS_MODEL], true)) {
                Route::put('/'.kebabAll($update_model), UPDATE)->name($update_model);
            }

            if ($modelName !== CART_MODEL) {
                Route::get('/'.kebabAll($edit_model).'/{'.$modelName.'}', EDIT)->name($edit_model);
            }

            Route::delete('/'.kebabAll($delete_model).'/{'.$modelName.'}', DESTROY)->name($delete_model)->withTrashed();

            Route::delete('/'.kebabAll(pluralize($delete_model)), DESTROY_MULTIPLE)->name(pluralize($delete_model))->withTrashed();

            Route::put('/'.kebabAll($restore_model).'/{'.$modelName.'}', RESTORE)->name($restore_model)->withTrashed();

            Route::put('/'.kebabAll(pluralize($restore_model)), RESTORE_MULTIPLE)->name(pluralize($restore_model))->withTrashed();
        });
    }
}


if (!function_exists('searchRoute')) {
    /**
     * Generate search routes.
     *
     * @param string $searchableTable
     * @param string|null $urlParam
     * @return Routing
     */
    function searchRoute(string $searchableTable, ?string $urlParam = null): Routing
    {
        $searchable_table = str($searchableTable)->ltrim(ADMIN.'_')->value();
        $search_uri = '/'.kebabAll($searchable_table).(isset($urlParam) ? '/'.$urlParam : '');

        return Route::match(['get', 'post'], $search_uri, capitalizeSecond($searchable_table))->name($searchableTable);
    }
}


if (!function_exists('is'.ucfirst(ADMIN).'Route')) {
    /**
     * Check if the route is related to the admin.
     *
     * @param bool $returnRole
     * @return string|bool
     */
    function isAdminRoute(bool $returnRole = false): string|bool
    {
        $is_admin = str_contains(url()->current(), ADMIN);

        if ($returnRole) {
            return $is_admin
                ? ADMIN
                : USER_MODEL;
        }

        return $is_admin;
    }
}


if (!function_exists('adminCurrentUrl')) {
    /**
     * Add class(es) to the current admin URL.
     *
     * @param string $url
     * @param array $classes
     * @return string
     */
    function adminCurrentUrl(string $url, array $classes): string
    {
        return str(url()->current())->whenContains(ADMIN."/$url", function () use ($classes) {
            return implode(' ', $classes);
        });
    }
}


if (!function_exists('forgetCache')) {
    /**
     * Forget the cache.
     *
     * @param string|array $key
     * @param Model|stdClass|null $model
     * @param string|null $additionalSuffix
     * @param array|null $extraConfig
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    function forgetCache(string|array $key, Model|stdClass $model = null, ?string $additionalSuffix = null, ?array $extraConfig = []): bool {
        if (is_null($model)) {
            return cache()->deleteMultiple(is_array($key) ? $key : [$key]);
        }

        $selected_ids = selectedIdsRequest()
            ? array_map('intval', [selectedIdsRequest()])
            : [$model->{ID}];

        $query = $model::query()->whereIn(ID, $selected_ids)
            ->withTrashed();

        $query->pluck($additionalSuffix)
            ->unique()
            ->each(static fn(string $suffix) =>
                cache()->forget($key.('_'.$suffix ?: ''))
            );

        if (empty($extraConfig)) {
            return true;
        }

        $relations = collect($extraConfig['relation'] ?? [])
            ->when(is_string($extraConfig['relation'] ?? null), static fn() => collect([$extraConfig['relation']]));

        $relation_columns = collect($extraConfig['relation_only_columns'] ?? []);

        $query->when($relations->isNotEmpty(), static fn($q) =>
            $q->with(
            $relations->mapWithKeys(static fn($relation) => [
                        $relation => static fn($relQuery) =>
                            $relQuery->select($relation_columns->get($relation, [ID]))
                    ]
                )
                ->toArray()
            )
        );

        $query->cursor()
            ->flatMap(static function ($item) use ($relations, $relation_columns) {
                return $relations->isEmpty()
                    ? collect([$item->only($relation_columns->flatten()->toArray())])
                    : $relations->flatMap(static fn($relation) =>
                        collect($item->{$relation})
                            ->when(!($item->{$relation} instanceof Collection), static fn() =>
                                collect([$item->{$relation}])
                            )
                            ->filter()
                            ->map(static fn($relatedItem) =>
                                $relatedItem->only($relation_columns->get($relation, [ID]))
                            )
                    );
            })
            ->unique($extraConfig['unique_by'] ?? null)
            ->flatMap(static fn($data) =>
                collect($extraConfig['cache_keys'])->map(static fn($builder) => $builder($data))
            )
            ->each(static fn($cacheKey) => cache()->forget($cacheKey));

        return true;
    }
}


if (!function_exists('getLastPage')) {
    /**
     * Get the last page number.
     * To add the new item to the last position in the last page.
     *
     * @param Model|stdClass $model
     * @param int $perPage
     * @return int
     */
    function getLastPage(Model|stdClass $model, int $perPage = 16): int
    {
        $total = $model::query()->count();

        return ceil($total / $perPage);
    }
}


if (!function_exists(ADMIN.'Layout')) {
    /**
     * Get the admin layout name.
     *
     * @param string $layoutName
     * @return string
     */
    function adminLayout(string $layoutName): string
    {
        return ADMIN.'.layouts.'.ADMIN."-$layoutName";
    }
}


if (!function_exists(USER_MODEL.'Layout')) {
    /**
     * Get the user layout name.
     *
     * @param string $layoutName
     * @return string
     */
    function userLayout(string $layoutName): string
    {
        return USER_MODEL.'.layouts.'.$layoutName;
    }
}


if (!function_exists('viewLayout'.ucfirst(TITLE))) {
    /**
     * Get the view layout & title.
     *
     * @param string $role
     * @return array
     */
    function viewLayoutTitle(string $role): array
    {
        $layout = $role === ADMIN
            ? adminLayout('main')
            : userLayout('main');

        $title = [
            TITLE => str(request()?->route()?->getName())
                ->headline()
                ->after(ucfirst($role === ADMIN ? ADMIN : ''))
                ->value()
        ];

        return [$layout => $title];
    }
}


if (!function_exists('commonCollections')) {
    /**
     * Get the common collections to be used in the frontend side.
     *
     * @return array
     */
    function commonCollections(): array
    {
        $categories_subcategories_common = [ID, NAME, SLUG, MAIN_IMAGE];
        $categories    = Category::get([...$categories_subcategories_common, BANNER_IMAGE]);
        $subcategories = Subcategory::get($categories_subcategories_common);
        $new_products  = Product::query()
            ->latest()
            ->take(4)
            ->get(PRODUCT_ITEM_ATTRIBUTES);

        if (str(Route::currentRouteName())->exactly(PRODUCTS_LIST)) {
            $categories    = $categories->load(PRODUCTS_TABLE);
            $subcategories = $subcategories->load(PRODUCTS_TABLE);
        }

        $navbar_dropdowns = [
            [
                'title'      => CATEGORIES_TABLE,
                'collection' => $categories,
                'route_name' => CATEGORY_MODEL,
            ],
            [
                'title'      => 'collections',
                'collection' => $subcategories,
                'route_name' => SUBCATEGORY_MODEL,
            ],
        ];

        $navbar_items = [
            [
                'route_name' => PAYMENT,
            ],
            [
                'route_name' => ABOUT_US,
            ],
            [
                'route_name' => CONTACT_US,
            ],
        ];

        $navbar_offers = [
            'Every day up to 45% off',
            'End of hot summer sale',
            'Get 50% off on four orders',
        ];

        $footer_menus = [
            'information' => [
                ucfirst(pluralize(PRICE)).' Drop',
                capitalizeAll(NEW_PRODUCTS),
                'Best Sales',
                'Sitemap',
                'Store',
            ],
            'our company' => [
                'Delivery',
                'Legal Notice',
                capitalizeAll(ABOUT_US),
                'Secure Payment',
                capitalizeAll(CONTACT_US),
            ],
            'your account' => [
                'Personal Info',
                ucfirst(ORDERS_TABLE),
                'Credit Slips',
                ucfirst(ADDRESSES_TABLE),
                ucfirst(CART_MODEL),
            ],
        ];

        $navbar_dropdowns = object_from_array($navbar_dropdowns);
        $navbar_items     = object_from_array($navbar_items);
        $footer_menus     = object_from_array($footer_menus);

        return compact(CATEGORIES_TABLE, SUBCATEGORIES_TABLE, NEW_PRODUCTS, 'navbar_dropdowns', 'navbar_items', 'navbar_offers', 'footer_menus');
    }
}


if (!function_exists('commonAsideMenus')) {
    /**
     * Get the common menus to be used in the frontend side.
     *
     * @return array
     */
    function commonAsideMenus(): array
    {
        $accessories_menu_item = [
            'Top Accessories' => [
                'Sports T-Shirts',
                'Track pants',
                'Cargos',
                'Top wear',
                'Track pants',
            ],
        ];

        $sunglasses_menu_item = [
            'Sunglasses' => [
                'Shirts',
                'Boxers',
                'Vests',
                'Belts',
                'Accessories',
            ],
        ];

        $top_wear = [
            ...$accessories_menu_item,
            ...$sunglasses_menu_item,
            'Top Wear' => [
                'Shirts',
                'Kurtas',
                'T-Shirts',
                'Belts',
                'Jewellery',
            ],
        ];

        $bottom_wear = [
            'Bottom Accessories' => [
                'Vests',
                'Sunglasses',
                'Bottom wear',
                'Jeans',
                'Cargos',
            ],
            ...$sunglasses_menu_item,
            ...$accessories_menu_item,
            'Bottom Wear' => [
                'Sports T-Shirts',
                'Jewellery',
                'Track pants',
                'Cargos',
                'Boxer',
            ],
        ];

        $customers_reviews = [
            [
                NAME          => 'Yousif Ayman',
                PRODUCT_MODEL => 'Blazer Jacket',
                REVIEW_MODEL  => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aspernatur dolore nostrum, odit quidem reiciendis vel voluptas? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores beatae consectetur deleniti dicta doloremque dolorum ea excepturi, facere fuga harum iure iusto magnam minima molestiae optio quas quisquam sapiente, sequi?',
            ],
            [
                NAME          => 'Ayman ahmed',
                PRODUCT_MODEL => 'Blazer Jacket',
                REVIEW_MODEL  => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aspernatur dolore nostrum, odit quidem reiciendis vel voluptas?',
            ],
            [
                NAME          => 'ahmed mohamed',
                PRODUCT_MODEL => 'Blazer Jacket',
                REVIEW_MODEL  => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aspernatur dolore nostrum, odit quidem reiciendis vel voluptas?',
            ],
        ];

        $top_wear          = object_from_array($top_wear);
        $bottom_wear       = object_from_array($bottom_wear);
        $customers_reviews = object_from_array($customers_reviews);

        return compact('top_wear', 'bottom_wear', 'customers_reviews');
    }
}


if (!function_exists(CART_MODEL.'Config')) {
    /**
     * Configure the cart.
     *
     * @param array $vars
     * @return array|string
     * @throws Throwable
     */
    function cartConfig(array $vars = []): array|string
    {
        $user_carts_ids = cache()->remember(CARTS_TABLE, 1800, static fn() =>
            Cart::query()->whereHasAuthUser()
                ->pluck(ID)
                ->toArray()
        );

        $user_carts = Cart::query()->whereIn(ID, $user_carts_ids)
            ->with(PRODUCT_MODEL, static fn(BelongsTo $query) => $query->select(PRODUCT_ITEM_ATTRIBUTES));

        $total_items = $user_carts->sum(PRODUCT_QUANTITY);

        $total_cost = $user_carts->cursor()
            ->sum(fn(Cart $cartItem) => $cartItem->{PRODUCT_MODEL}->{NEW_PRICE} * $cartItem->{PRODUCT_QUANTITY});

        $user_cart_items = Route::currentRouteName() === CART_MODEL
            ? $user_carts->fastPaginate(5)
            : $user_carts->cursor();

        $user_cart_items->isEmpty()
            ? session()->flash(EMPTY_CART)
            : session()->forget(EMPTY_CART);

        $compact_vars = compact(USER_CART_ITEMS, TOTAL_COST, TOTAL_ITEMS);

        return empty($vars)
            ? $compact_vars
            : [...$compact_vars, ...$vars];
    }
}


if (!function_exists(ADDRESS_MODEL.ucfirst(COUNTRY))) {
    /**
     * Get the user's address country(ies).
     *
     * @param HasMany $address
     * @return HasMany
     */
    function addressCountry(HasMany $address): HasMany
    {
        return $address->select(COUNTRY, USER_ID)
            ->distinct(COUNTRY)
            ->groupBy(COUNTRY, USER_ID);
    }
}


if (!function_exists(ORDER_MODEL.ucfirst(STATUS))) {
    /**
     * Get the status of an order with its badge.
     *
     * @param Model|stdClass $order
     * @param string|null $type
     * @return string
     */
    function orderStatus(Model|stdClass $order, ?string $type = null): string
    {
        $order_status       = (int) $order->{STATUS};
        $order_status_name  = array_search($order_status, ORDER_STATUS_ENUM,       true);
        $order_status_badge = array_search($order_status, ORDER_STATUS_BADGE_ENUM, true);
        $order_status_icon  = array_search($order_status, ORDER_STATUS_ICON_ENUM,  true);

        if ($type === 'badge') {
            return $order_status_badge;
        }

        if ($type === 'icon') {
            return $order_status_icon;
        }

        return $order_status_name;
    }
}


if (!function_exists(pluralize('date'))) {
    /**
     * Get the creation and updated dates of a model.
     *
     * @param Model|stdClass $model
     * @param int $dateIndex
     * @param bool $isTime
     * @return string
     */
    function dates(Model|stdClass $model, int $dateIndex, bool $isTime = false): string
    {
        $model_date = $model->{DATES[$dateIndex]}->format('d-m-Y');
        $model_time = $model->{DATES[$dateIndex]}->setTimezone('Africa/Cairo')->format('h : i A');

        return $model_date.($isTime ? '<br> { '.$model_time.' }' : '');
    }
}


if (!function_exists('get'.str(ORDER_DETAILS)->studly()->value())) {
    /**
     * Get the specified order's details.
     *
     * @param Order $order
     * @return array
     */
    function getOrderDetails(Order $order): array
    {
        $order_number_title = ucfirst(ORDER_MODEL).' Number #'.$order->{TRACKING_NUM};

        $order_details = [
            "Bought at" => "<span class='fw-500'>".dates($order, 0)."</span>",
            "Number of ".ucfirst(PRODUCTS_TABLE) => "<span class='fw-500'>".$order->{NUM_ITEMS}.' '.ucfirst(PRODUCTS_TABLE)."</span>",
            ucfirst(STATUS) => "<span class='badge badge-".orderStatus($order, 'badge')." rounded-pill p-2'>".orderStatus($order)."</span>",
        ];

        $order_product_size = static fn(OrderItem $orderItem) => key(array_intersect(PRODUCT_SIZE_ENUM, (array) $orderItem->{PRODUCT_SIZE}));

        return compact(ORDER_MODEL, ORDER_NUMBER_TITLE, ORDER_DETAILS, ORDER_PRODUCT_SIZE);
    }
}


if (!function_exists(PRODUCT_MODEL.ucfirst(SIZES))) {
    /**
     * Get the sizes of a product.
     *
     * @param Model|stdClass $product
     * @param bool $areValues
     * @return array
     */
    function productSizes(Model|stdClass $product, bool $areValues = false): array
    {
        $product_sizes = array_intersect(PRODUCT_SIZE_ENUM, $product->{SIZES}->pluck(SIZE)->toArray());

        return $areValues
            ? $product_sizes
            : array_keys($product_sizes);
    }
}


if (!function_exists(PRODUCTS_TABLE.'PageVars')) {
    /**
     * Set the variables of the products' page.
     *
     * @param LengthAwarePaginator $products
     * @param string|null $productsPaginationRoute
     * @return array|string
     */
    function productsPageVars(LengthAwarePaginator $products, ?string $productsPaginationRoute = null): array|string
    {
        $products_list_title = str(Route::currentRouteName())
            ->whenContains([SEARCH_PRODUCTS, FILTER_PRODUCTS],
                static fn() => ucwords(PRODUCTS_TABLE),
                static fn() => ucwords(basename(str_replace('-', ' & ', url()->current()))));

        $sizes = collect(PRODUCT_SIZE_ENUM)
            ->map(fn(int $value, string $size) => (object)[
                SIZE                    => $size,
                SIZE.'_value'           => $value,
                PRODUCTS_TABLE.'_count' => ProductSize::query()->where(SIZE, $value)->count(),
            ])->values();

        $prices_range = (object)[
            MIN_PRICE => Product::query()->min(NEW_PRICE),
            MAX_PRICE => Product::query()->max(NEW_PRICE),
        ];

        $filter_products_error = static fn(string $attributeName) => formError(FILTER, PRODUCTS_TABLE, $attributeName);

        return [
            PRODUCTS_TABLE            => $products,
            PRODUCTS_LIST_TITLE       => $products_list_title,
            PRODUCT_SIZES_TABLE       => $sizes,
            PRODUCTS_PRICES           => $prices_range,
            FILTER_PRODUCTS_ERROR     => $filter_products_error,
            PRODUCTS_PAGINATION_ROUTE => $productsPaginationRoute,
        ];
    }
}


if (!function_exists('view'.ucfirst(PRODUCTS_TABLE))) {
    /**
     * Display the view for the products' resource,
     * when searching or filtering.
     *
     * @param LengthAwarePaginator $products
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    function viewProducts(LengthAwarePaginator $products): Application|Factory|View|JsonResponse
    {
        $products_pagination_route = match (Route::currentRouteName()) {
            SEARCH_PRODUCTS => SEARCH_PRODUCTS,
            FILTER_PRODUCTS => FILTER_PRODUCTS,
            default         => PRODUCTS_LIST,
        };

        noResultsException($products);

        if (request()?->ajax()) {
            return isAdminRoute()
                ? ajaxPaginationResponse($products, ADMIN_PRODUCTS_PAGINATION, PRODUCTS_TABLE)
                : ajaxPaginationResponse($products, USER_PRODUCTS_PAGINATION, PRODUCTS_TABLE, [PRODUCTS_PAGINATION_ROUTE => $products_pagination_route]);
        }

        return showView(USER_PRODUCTS_VIEW, productsPageVars($products, $products_pagination_route));
    }
}


if (!function_exists(USER_MODEL.ucfirst(PRODUCTS_TABLE).'View')) {
    /**
     * Display the view for the products' resource.
     *
     * @param string $table
     * @param string|null $slug
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    function userProductsView(string $table, ?string $slug = null): Application|Factory|View|JsonResponse
    {
        $products_ids = cache()->remember(PRODUCTS_TABLE, 1800, static fn() =>
            Product::query()
                ->pluck(ID)
                ->toArray()
        );

        $products = paginateWithFallback(Product::class, $products_ids, attributes: PRODUCT_ITEM_ATTRIBUTES, callback: static fn(Builder $query) =>
            $query->when($table !== PRODUCTS_TABLE, static fn(Builder $q) =>
                $q->whereHas($table, static fn(Builder $item) =>
                    $item->where(SLUG, $slug)
                )
            )
        );

        return viewProducts($products);
    }
}


if (!function_exists(REVIEW_MODEL.'Data')) {
    /**
     * Get the average rate or adding/updating error of a review.
     *
     * @param int|null $productId
     * @param string|null $operation
     * @param string|null $attributeName
     * @return int|string|null
     */
    function reviewData(?int $productId = null, ?string $operation = null, ?string $attributeName = null): int|string|null
    {
        if ($productId) {
            return cache()->remember(AVERAGE_RATE.'_'.$productId, 1800, static fn() =>
                Review::query()->where(PRODUCT_ID, $productId)
                    ->withoutTrashed()
                    ->avg(RATING) ?? '0'
            );
        }

        if ($operation && $attributeName) {
            return formError($operation, REVIEW_MODEL, $attributeName);
        }

        return null;
    }
}


if (!function_exists('get'.ucfirst(REVIEWS_TABLE))) {
    /**
     * Get the reviews of a specified product.
     *
     * @param int $productId
     * @return array
     */
    function getReviews(int $productId): array
    {
        $average_rate        = reviewData($productId);
        $add_review_error    = static fn(string $attributeName) => reviewData(operation: ADD, attributeName: $attributeName);
        $update_review_error = static fn(string $attributeName) => reviewData(operation: UPDATE, attributeName: $attributeName);

        return compact(AVERAGE_RATE, ADD_REVIEW_ERROR, UPDATE_REVIEW_ERROR);
    }
}


if (!function_exists('showView')) {
    /**
     * Display the view for a specified resource.
     *
     * @param string $viewName
     * @param array $vars
     * @return Application|Factory|View
     * @throws Throwable
     */
    function showView(string $viewName, array $vars = []): Application|Factory|View
    {
        $view_vars = isAdminRoute()
            ? $vars
            : cartConfig($vars);

        return view($viewName, $view_vars);
    }
}


if (!function_exists('validateAttributes')) {
    /**
     * Validate attributes of the request.
     *
     * @param object $formRequest
     * @param mixed|null $extraValidationCheck
     * @return ValidatorContract|array
     * @throws ValidationException
     */
    function validateAttributes(object $formRequest, mixed $extraValidationCheck = null): ValidatorContract|array
    {
        $validator = Validator::make($formRequest->data(), $formRequest->rules($extraValidationCheck ?? null), $formRequest->messages());

        if ($validator->fails()) {
            throw new ValidationException($validator, responseValidationError($validator));
        }

        return $validator->errors()->all();
    }
}


if (!function_exists('formError')) {
    /**
     * Show the form error message.
     *
     * @param string $action
     * @param string $modelOrTable
     * @param string $attribute
     * @return string
     */
    function formError(string $action, string $modelOrTable, string $attribute): string
    {
        echo "<div class='grace-form-error'><ul role='list' id='{$action}_{$modelOrTable}_{$attribute}_error' class='form-error $action-error fs-7 text-danger'></ul></div>";

        return '';
    }
}


if (!function_exists('noResultsException')) {
    /**
     * Show (no results) image.
     *
     * @param LengthAwarePaginator $model
     * @return void
     * @throws NotFoundHttpException
     */
    function noResultsException(LengthAwarePaginator $model): void
    {
        session()->forget('no_results');

        if ($model->isEmpty()) {
            if (request()?->ajax()) {
                throw new NotFoundHttpException('no-results');
            }

            session()->flash('no_results');
        }
    }
}


if (!function_exists('storeImageWithoutBackground')) {
    /**
     * Remove an image background using (remove.bg) API
     *
     * @param mixed $image
     * @param string $image_path
     * @return string
     * @throws ServiceUnavailableHttpException|RandomException
     */
    function storeImageWithoutBackground(mixed $image, string $image_path): string
    {
        $response = Http::withHeaders([
            'X-Api-Key' => 'TGtoLSB6D6d98KEse4PRYkBE',
        ])
            ->attach('image_file', file_get_contents($image->getRealPath()), $image->getClientOriginalName())
            ->post('https://api.remove.bg/v1.0/removebg', ['size' => 'auto']);

        if (!$response->successful()) {
            throw new ServiceUnavailableHttpException(null, 'The remove.bg service is currently unavailable. Please try again later!');
        }

        $removed_image_bg = $response->body();

        $image_name = time().random_int(10, 100).'.png';  // PNG is the default format returned by remove.bg

        Storage::put($image_path.DIRECTORY_SEPARATOR.$image_name, $removed_image_bg);

        return $image_name;
    }
}


if (!function_exists(STORE_OR_UPDATE.'Image')) {
    /**
     * Store or Update the main image.
     *
     * @param Model|stdClass $model
     * @param string|null $modelId
     * @param string|null $imageType
     * @param mixed|null $image
     * @return string
     * @throws NotFoundHttpException|ServiceUnavailableHttpException|RandomException
     */
    function storeOrUpdateImage(Model|stdClass $model, ?string $modelId = null, ?string $imageType = null, mixed $image = null): string
    {
        $exist_image_name = $model::query()->firstWhere(ID, $modelId)?->{$imageType};
        $image_path = "public/images/".$model->getTable().DIRECTORY_SEPARATOR.pluralize($imageType);

        if (is_null($image) && isset($exist_image_name)) {
            return $exist_image_name;
        }

        if (isset($exist_image_name)) {
            Storage::exists($image_path.DIRECTORY_SEPARATOR.$exist_image_name)
                ? Storage::delete($image_path.DIRECTORY_SEPARATOR.$exist_image_name)
                : throw new NotFoundHttpException('The targeted image is not found in the storage disk');
        }

//        $image_name = time().random_int(10, 100).'.png';
//        $image->storeAs($image_path, $image_name);
//        return $image_name;

        return storeImageWithoutBackground($image, $image_path);
    }
}


if (!function_exists('imageSource')) {
    /**
     * Get the image source.
     *
     * @param Model|stdClass|string $modelOrImageName
     * @param string|null $imageType
     * @param bool $forDeletePath
     * @return string
     */
    function imageSource(Model|stdClass|string $modelOrImageName, ?string $imageType = null, bool $forDeletePath = false): string
    {
        $image_path = "images/";

        if (is_string($modelOrImageName)) {
            return asset(Storage::url("$image_path$modelOrImageName"));
        }

        $image_name = $modelOrImageName->{$imageType};

        if (str_contains($imageType, PRODUCT_MODEL)) {
            $imageType = str_replace(PRODUCT_MODEL.'_', '', $imageType);
            $image_name = $modelOrImageName->{PRODUCT_MODEL."_$imageType"};
        }

        $image_path .= str($imageType)->exactly(THUMB_IMAGE) || $modelOrImageName->getTable() === ORDER_ITEMS_TABLE
            ? PRODUCTS_TABLE
            : $modelOrImageName->getTable();

        $image_path .= DIRECTORY_SEPARATOR.pluralize($imageType).DIRECTORY_SEPARATOR.$image_name;

        if ($forDeletePath) {
            return "public".DIRECTORY_SEPARATOR.$image_path;
        }

        return asset(Storage::url($image_path));
    }
}


if (!function_exists(CREATE.'Or'.ucfirst(UPDATE).'MultipleCollections')) {
    /**
     * Create or Update multiple collections related to a specified record of a model.
     *
     * @param Model $newOrExistingCollection
     * @param string $relation
     * @param mixed $relatedCollectionValues
     * @return array
     */
    function createOrUpdateMultipleCollections(Model $newOrExistingCollection, string $relation, mixed $relatedCollectionValues): array
    {
        $related_collection_values = array_filter($relatedCollectionValues);

        return $newOrExistingCollection->{$relation}()->sync($related_collection_values);
    }
}


if (!function_exists(STORE_OR_UPDATE.ucfirst(USER_MODEL))) {
    /**
     * Store or Update a user.
     *
     * @param string $operation
     * @return User
     * @throws ValidationException|CacheInvalidArgumentException
     */
    function storeOrUpdateUser(string $operation): User
    {
        $user_attributes = USER_ATTRIBUTES;

        if ($operation === REGISTER) {
            array_pop($user_attributes);

            $user_request = new AuthRequest($operation, USER_MODEL, $user_attributes);

            validateAttributes($user_request);
        }
        else {
            $user_request = new UserRequest($operation, USER_MODEL, $user_attributes);

            $user_id = request()?->input(UPDATE_USER_ID);

            validateAttributes($user_request, $user_id);
        }

        [$first_name, $last_name, $email, $password] = $user_attributes;

        [$first_name_value, $last_name_value, $email_value, $password_value] = $user_request->dataValues();

        $attributes = [
            $first_name => $first_name_value,
            $last_name  => $last_name_value,
            $email      => $email_value,
            $password   => bcrypt($password_value),
        ];

        if ($operation === REGISTER) {
            $user = User::query()->create($attributes);

            forgetCache([USERS_PAGINATION_CACHE_KEY, USER_MODEL]);

            sendNotificationToAdmins(new NewUserRegistered($user));

            return $user;
        }

        $role_value = Arr::last($user_request->dataValues());

        $attributes = array_merge($attributes, [ROLE => (int) $role_value]);

        $user = User::query()->updateOrCreate(
            [ID => $user_id], $attributes
        );

        forgetCache([USERS_PAGINATION_CACHE_KEY, USER_MODEL]);

        sendNotificationToAdmins(new NewAdminActionTaken([$user, $user->{FULL_NAME}], $operation), true);

        return $user;
    }
}


if (!function_exists('getData')) {
    /**
     * Get the data of a specified record of a model.
     *
     * @param Model|stdClass $model
     * @param array $desiredData
     * @return object
     */
    function getData(Model|stdClass $model, array $desiredData): object
    {
        return $model::query()->findOrFail($model->getKey(), [ID, ...$desiredData]);
    }
}


if (!function_exists(REMOVE.ucfirst(DELETE).'Or'.ucfirst(RESTORE))) {
    /**
     * Remove, Delete, or Restore a record of a model.
     *
     * @param Model|stdClass $model
     * @param string|null $forNotification
     * @param bool $deleteImages
     * @return bool
     * @throws NotFoundHttpException
     */
    function removeDeleteOrRestore(Model|stdClass $model, ?string $forNotification = null, bool $deleteImages = false): bool
    {
        $selected_ids = selectedIdsRequest()
            ? array_map('intval', [selectedIdsRequest()])
            : [$model->{ID}];

        $selected_collections = $model::query()->whereIn(ID, $selected_ids);

        $is_collection_trashed = $selected_collections->cursor()
            ->every(static fn($collection) =>
                Cart::class
                    ? false
                    : $collection->trashed()
            );

        $send_notification_to_admins = static fn(string $action) => sendNotificationToAdmins(new NewAdminActionTaken([$model, $forNotification], $action, count($selected_ids) > 1), true);

        // Remove
        if (!$is_collection_trashed) {
            $destroyed_ids = $model::destroy($selected_ids);

            $send_notification_to_admins(REMOVE);

            return $destroyed_ids;
        }

        // Restore
        if (request()?->input(RESTORE)) {
            $restore_ids = $selected_collections->restore();

            $send_notification_to_admins(RESTORE);

            return $restore_ids;
        }

        // Delete
        if ($deleteImages) {
            deleteImages($model, $selected_ids);
        }

        $force_deleted_ids = $selected_collections->forceDelete();

        $send_notification_to_admins(DELETE);

        return $force_deleted_ids;
    }
}


if (!function_exists(DELETE.'Images')) {
    /**
     * Delete the image(s) of a specified record or all/some records of a model.
     *
     * @param Model|stdClass $model
     * @param array $selectedIds
     * @return bool
     * @throws NotFoundHttpException
     */
    function deleteImages(Model|stdClass $model, array $selectedIds = []): bool
    {
        $images_data_list       = [];
        $images                 = [];
        $all_other_images_found = true;
        $table_name             = $model->getTable();
        $force_delete           = request()?->input('force_'.DELETE);
        $db_images_count        = 'db_images_count';
        $storage_images_count   = 'storage_images_count';
        $image_type             = 'image_type';
        $model_item_name        = 'model_item_name';
        $deletable_images       = 'deletable_images';

        $exception_message = static fn(array $imageData) => "One or more $imageData[$image_type] in the $table_name named as (".implode(', ', array_unique($imageData[$model_item_name])).") not found in the storage disk!";

        $ids_to_delete = empty($selectedIds)
            ? [$model->{ID}]
            : $selectedIds;

        $images_data = static fn(string $imageType) => empty($selectedIds)
            ? getImagesToDelete($model, $table_name, $imageType)
            : getImagesToDelete($model, $table_name, $imageType, true, $selectedIds);

        $images_to_delete = $model::query()->whereIn(ID, $ids_to_delete)->onlyTrashed();

        if (str($table_name)->exactly(CATEGORIES_TABLE)
            && $images_to_delete->pluck(BANNER_IMAGE)->count())
        {
            $images_data_list = $images_data(BANNER_IMAGE);
        }

        if (str($table_name)->exactly(PRODUCTS_TABLE)
            && $images_to_delete->withCount(THUMB_IMAGES)->pluck(THUMB_IMAGES_TABLE.'_count')->first() > 0)
        {
                $images_data_list = $images_data(THUMB_IMAGE);
        }

        if (!empty($images_data_list)) {
            if ($images_data_list[$db_images_count] !== $images_data_list[$storage_images_count] && !$force_delete) {
                throw new NotFoundHttpException($exception_message($images_data_list));
            }

            $images = [...$images_data_list[$deletable_images]];

            if ($images_data_list[$db_images_count] !== $images_data_list[$storage_images_count] && $force_delete) {
                $all_other_images_found = false;
            }
        }

        if (!empty($images)) {
            $images_data_list = $images_data(MAIN_IMAGE);

            if ($images_data_list[$db_images_count] === $images_data_list[$storage_images_count]) {
                $all_other_images_found = true;
            }

            if (($all_other_images_found && $images_data_list[$db_images_count] !== $images_data_list[$storage_images_count] && !$force_delete) || (!$all_other_images_found && +$force_delete !== 2)) {
                throw new NotFoundHttpException($exception_message($images_data_list));
            }

            $images = [...$images, ...$images_data_list[$deletable_images]];
        }

        $images_to_delete = array_filter($images, static fn($element) => !is_array($element));

        return Storage::delete($images_to_delete);
    }
}


if (!function_exists('getImagesTo'.ucfirst(DELETE))) {
    /**
     * Get the image(s) of a specified record or all/some records of a model.
     *
     * @param Model|stdClass $model
     * @param string $tableName
     * @param string $imageType
     * @param bool $isMultiple
     * @param array $selectedIds
     * @return array
     */
    function getImagesToDelete(Model|stdClass $model, string $tableName, string $imageType, bool $isMultiple = false, array $selectedIds = []): array
    {
        $images_to_delete = static fn(array $attributes) => $model::query()->onlyTrashed()->findOrFail($selectedIds, [NAME, ...$attributes]);

        if ($isMultiple && count($selectedIds)) {
            $images = str($imageType)->exactly(THUMB_IMAGE)
                ? $images_to_delete([ID])?->pluck(THUMB_IMAGES)->flatten()
                : $images_to_delete([$imageType]);
        }
        else {
            $images = str($imageType)->exactly(THUMB_IMAGE)
                ? $model->{THUMB_IMAGES}
                : collect([$model]);
        }

        $deletable_images = $images->filter(fn($collection) => Storage::exists(imageSource($collection, $imageType, true)))
            ->map(fn(Model $collection) => imageSource($collection, $imageType, true))
            ->toArray();

        $deletable_images[] = $images->pluck($imageType)->toArray();

        $db_images_count = count(end($deletable_images));
        $storage_images_count = count(array_filter($deletable_images, static fn($element) => !is_array($element)));

        $image_type = capitalizeAll($imageType);

        $model_item_name = $images->when(str($imageType)->exactly(THUMB_IMAGE), static function (Collection $collection) use (&$tableName) {
            return $collection->filter(fn(ThumbImage $thumb_image) => !Storage::exists(imageSource($thumb_image, THUMB_IMAGE, true)))
                ->pluck(singularize($tableName))
                ->pluck(NAME)
                ->toArray();
        }, static function (Collection $collection) use (&$imageType) {
            return $collection->filter(fn($collection) => !Storage::exists(imageSource($collection, $imageType, true)))
                ->pluck(NAME)
                ->toArray();
        });

        return compact('deletable_images', 'db_images_count', 'storage_images_count', 'image_type', 'model_item_name');
    }
}


if (!function_exists('soft'.toPastTense(DELETE).'Relations')) {
    /**
     * Get the trashed relations of a model.
     *
     * @param Model|stdClass $model
     * @param array $relations
     * @return array
     */
    function softDeletedRelations(Model|stdClass $model, array $relations): array
    {
        return collect($relations)
            ->mapWithKeys(function (string $relation) use ($model) {
                $related_item = $model->{$relation} ?? null;

                if (!$related_item) {
                    return [];
                }

                // Single relation (belongsTo / hasOne)
                if (method_exists($related_item, 'trashed') && $related_item->trashed()) {
                    return [$relation => 'single'];
                }

                // Collection (belongsToMany / hasMany)
                if ($related_item instanceof Collection) {
                    $any_item_trashed = $related_item->contains(static fn(Model|stdClass $item) =>
                        method_exists($item, 'trashed') && $item->trashed()
                    );

                    if ($any_item_trashed) {
                        return [$relation => 'multiple'];
                    }
                }

                return [];
            })
            ->all();
    }
}


if (!function_exists('sendNotificationToAdmins')) {
    /**
     * Send a notification to all admins.
     *
     * @param NotificationInstance $notification
     * @param bool $exceptAuthAdmin
     * @return void
     */
    function sendNotificationToAdmins(NotificationInstance $notification, bool $exceptAuthAdmin = false): void
    {
        $admins = User::where(ROLE, 1)
            ->when($exceptAuthAdmin, static fn(Builder $user) => $user->whereNot(ID, auth()->id()))
            ->get([ID, ROLE]);

        Notification::send($admins, $notification);
    }
}


if (! function_exists('paginateWithFallback')) {
    /**
     * Paginate with fallback.
     *
     * @param string $modelClass
     * @param array $ids
     * @param int $perPage
     * @param array $attributes
     * @param Closure|null $callback
     * @return LengthAwarePaginator
     */
    function paginateWithFallback(string $modelClass, array $ids, int $perPage = 16, array $attributes = ['*'], Closure $callback = null): LengthAwarePaginator
    {
        $results = $modelClass::query()
            ->whereIn(ID, $ids)
            ->when(true, static function (Builder $query) use ($modelClass, $callback) {
                if (in_array($modelClass, [Product::class, Order::class], true)) {
                    $query->latest();
                }

                if (isset($callback)) {
                    $callback($query);
                }

                if (conditionRequest() === TRASHED) {
                    $query->onlyTrashed();
                }
            })
            ->fastPaginate($perPage, $attributes, 'page', currentPageRequest());

        if ($results->isEmpty() && currentPageRequest() > 1) {
            $results = $query->fastPaginate($perPage, $attributes, 'page', max(currentPageRequest() - 1, 1));
        }

        return $results;
    }
}
