<?php

use App\Contracts\HasImages;
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
use App\Models\User;
use App\Models\Wishlist;
use App\Notifications\NewAdminActionTaken;
use App\Notifications\NewUserRegistered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        return str($current_url = url()->current())->startsWith('https://www.')
            ? str_replace('https://www.', 'https://', $current_url)
            : str_replace('https://', 'https://www.', $current_url);
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
        $formated_url         = kebabAll($url);
        $post_method_callback = capitalizeSecond($url);

        return Route::controller($controller)->group(function () use ($url, $formated_url, $post_method_callback) {
            Route::get('/'.$formated_url, 'index')->name($url);
            Route::post('/'.$formated_url, $post_method_callback)->name($url.'_'.USER_MODEL);
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
        $singular_urls          = [WISHLIST_MODEL, CART_MODEL];

        $routes = collect()
            ->when(($modelName !== REVIEW_MODEL || $modelName !== WISHLIST_MODEL) && !isAdminRoute(), static fn(Collection $routesCollection) =>
                $routesCollection->push(static fn() =>
                    Route::get('/'.kebabAll($modelName).($urlParam ? "/{{$urlParam}}" : ''), 'index')
                        ->name(
                            in_array($modelName, $singular_urls, true)
                                ? $modelName
                                : pluralize($modelName)
                        )
                )
            )
            ->push(static fn() =>
                Route::match(['post', 'put'], '/'.kebabAll($create_or_update_model).'/{operation}', STORE_OR_UPDATE)->name($create_or_update_model)
            )
            ->when(in_array($modelName, [ORDER_MODEL, ADDRESS_MODEL], true), static fn(Collection $routesCollection) =>
                $routesCollection->push(static fn() =>
                    Route::put('/'.kebabAll($update_model), UPDATE)->name($update_model)
                )
            )
            ->when(!in_array($modelName, $singular_urls, true), static fn(Collection $routesCollection) =>
                $routesCollection->push(static fn() =>
                    Route::get('/'.kebabAll($edit_model)."/{{$modelName}}", EDIT)->name($edit_model)
                )
            )
            ->push(static fn() =>
                Route::delete('/'.kebabAll($delete_model)."/{{$modelName}}", DESTROY)->name($delete_model)->withTrashed()
            )
            ->push(static fn() =>
                Route::delete('/'.kebabAll(pluralize($delete_model)), DESTROY_MULTIPLE)->name(pluralize($delete_model))->withTrashed()
            )
            ->when(!in_array($modelName, $singular_urls, true), static fn(Collection $routesCollection) =>
                $routesCollection->push(static fn() =>
                    Route::put('/'.kebabAll($restore_model)."/{{$modelName}}", RESTORE)->name($restore_model)->withTrashed()
                )
                    ->push(static fn() =>
                        Route::put('/'.kebabAll(pluralize($restore_model)), RESTORE_MULTIPLE)->name(pluralize($restore_model))->withTrashed()
                    )
            );

        return Route::controller($controller)->group(static fn() => $routes->each(static fn($route) => $route()));
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
        $search_uri       = '/'.kebabAll($searchable_table).(isset($urlParam) ? '/'.$urlParam : '');

        return Route::match(['get', 'post'], $search_uri, capitalizeSecond($searchable_table))->name($searchableTable);
    }
}


if (!function_exists('is'.ucfirst(ADMIN).'Route')) {
    /**
     * Check if the route is related to the admin.
     *
     * @param bool $isReturnRole
     * @return string|bool
     */
    function isAdminRoute(bool $isReturnRole = false): string|bool
    {
        $is_admin = str_contains(url()->current(), ADMIN);

        if ($isReturnRole) {
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
            request()?->ajax()
                ? throw new NotFoundHttpException('no-results')
                : session()->flash('no_results');
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
     * @param string $imageType
     * @param Model|stdClass $model
     * @param string|null $modelId
     * @param mixed|null $image
     * @param string|null $checkBackground
     * @return string
     * @throws NotFoundHttpException|ServiceUnavailableHttpException|RandomException
     */
    function storeOrUpdateImage(string $imageType, Model|stdClass $model, ?string $modelId = null, mixed $image = null, ?string $checkBackground = null): string
    {
        $image_path       = "public/images/".$model->getTable().DIRECTORY_SEPARATOR.pluralize($imageType);
        $exist_image_name = $model::query()->firstWhere(ID, $modelId)?->{$imageType};

        if (is_null($image) && isset($exist_image_name)) {
            return $exist_image_name;
        }

        if (isset($exist_image_name)) {
            Storage::exists($image_path.DIRECTORY_SEPARATOR.$exist_image_name)
                ? Storage::delete($image_path.DIRECTORY_SEPARATOR.$exist_image_name)
                : throw new NotFoundHttpException('The targeted image is not found in the storage disk.');
        }

        if ($checkBackground === 'on') {
            return storeImageWithoutBackground($image, $image_path);
        }

        $image_name = time().random_int(10, 100).'.png';
        $image->storeAs($image_path, $image_name);

        return $image_name;
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
            $imageType  = str_replace(PRODUCT_MODEL.'_', '', $imageType);
            $image_name = $modelOrImageName->{PRODUCT_MODEL."_$imageType"};
        }

        $image_path .= str($imageType)->exactly(THUMB_IMAGE) || $modelOrImageName->getTable() === ORDER_ITEMS_TABLE
            ? PRODUCTS_TABLE
            : $modelOrImageName->getTable();

        $image_path .= DIRECTORY_SEPARATOR.pluralize($imageType).DIRECTORY_SEPARATOR.$image_name;

        return $forDeletePath
            ? "public".DIRECTORY_SEPARATOR.$image_path
            : asset(Storage::url($image_path));
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


if (!function_exists('collectImagesTo'.ucfirst(DELETE))) {
    /**
     * Collect the image(s) of a specified record or all/some records of a model.
     *
     * @param Model|stdClass $model
     * @return Collection
     */
    function collectImagesToDelete(Model|stdClass $model): Collection
    {
        /** @var (HasImages&Model)|(HasImages&stdClass) $model */

        return collect($model->imageProperties())
            ->flatMap(static function (array $property, string $imageType) use ($model) {
                // Column image (single)
                if (($property['type'] === 'column') && !empty($model->{$imageType})) {
                    return [imageSource($model, $imageType, true)];
                }

                // Relation images (multiple)
                if (($property['type'] === 'relation') && $model->relationLoaded($imageType)) {
                    return $model->{$imageType}
                        ->map(static fn(Model|stdClass $img) => imageSource($img, $property['image_type'], true)
                        )
                        ->all();
                }

                return [];
            })
            ->filter()
            ->unique()
            ->values();
    }
}


if (!function_exists(DELETE.'Images')) {
    /**
     * Delete the image(s) of a specified record or all/some records of a model.
     *
     * @param Model|stdClass $model
     * @param array $selectedIds
     * @return bool
     */
    function deleteImages(Model|stdClass $model, array $selectedIds): bool
    {
        /** @var (HasImages&Model)|(HasImages&stdClass) $model */

        $images_relations = collect($model->imageProperties())
            ->filter(static fn(array $image) => $image['type'] === 'relation')
            ->keys()
            ->values()
            ->all();

        $force_delete = request()?->input('force_'.DELETE);

        $images_to_delete = $model::query()
            ->onlyTrashed()
            ->whereIn(ID, $selectedIds)
            ->with($images_relations)
            ->get()
            ->flatMap(static function (Model|stdClass $images_model) use ($force_delete) {
                $images = collectImagesToDelete($images_model);

                if ($images->isEmpty()) {
                    return collect();
                }

                $missing_images = $images->reject(static fn(string $path) => Storage::exists($path));

                if ($missing_images->isNotEmpty() && !$force_delete) {
                    throw new NotFoundHttpException('One or more images were not found in storage.');
                }

                return $force_delete
                    ? $images
                    : $images->diff($missing_images);
            })
            ->values();

        return $images_to_delete->isEmpty()
            || Storage::delete(
                $images_to_delete->unique()
                    ->values()
                    ->all()
            );
    }
}


if (!function_exists(REMOVE.ucfirst(DELETE).'Or'.ucfirst(RESTORE))) {
    /**
     * Remove, Delete, or Restore a record of a model.
     *
     * @param Model|stdClass $model
     * @param string|null $forNotification
     * @return bool
     * @throws NotFoundHttpException
     */
    function removeDeleteOrRestore(Model|stdClass $model, ?string $forNotification = null): bool
    {
        $selected_ids = selectedIdsRequest()
            ? array_map('intval', array_filter(
                array_map('trim', explode(',', selectedIdsRequest()))
            ))
            : [$model->{ID}];

        $selected_collections = $model::query()->whereIn(ID, $selected_ids);

        $is_collection_trashed = $selected_collections->cursor()
            ->every(static fn($collection) =>
            Wishlist::class || Cart::class
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
        if ($model instanceof HasImages) {
            deleteImages($model, $selected_ids);
        }

        $force_deleted_ids = $selected_collections->forceDelete();

        $send_notification_to_admins(DELETE);

        return $force_deleted_ids;
    }
}


if (!function_exists('clearExceptionMessage')) {
    /**
     * Handle exception messages for model actions (remove, delete, or restore).
     *
     * @param string $modelName
     * @param string $action
     * @param bool $isMultiple
     * @return string
     */
    function clearExceptionMessage(string $modelName, string $action, bool $isMultiple = false): string
    {
        $verb = 'is';

        if ($isMultiple) {
            $modelName = pluralize($modelName);
            $verb      = 'are';
        }

        return "The $modelName you are trying to $action $verb not found!";
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
            ->mapWithKeys(static function (string $attribute, string $relation) use ($model) {
                $related = $model->{$relation} ?? null;
                $trashed_method_exists = static fn($item) => method_exists($item, TRASHED) && $item->trashed();

                // Single Relation (belongsTo / hasOne)
                if ($trashed_method_exists($related)) {
                    return [
                        $relation => [
                            'type'                             => 'single',
                            'label'                            => $relation,
                            toPastTense(DELETE).'_items' => [$related->{$attribute} ?? $relation],
                        ]
                    ];
                }

                // Many Relation (hasMany / belongsToMany)
                if ($related instanceof Collection) {
                    $deleted_items = $related->filter(fn($item) => $trashed_method_exists($item))
                        ->map(fn(Model|stdClass $item) => $item->{$attribute} ?? 'Unknown')
                        ->values()
                        ->all();

                    if (!empty($deleted_items)) {
                        return [
                            $relation => [
                                'type'                             => 'multiple',
                                'label'                            => $relation,
                                toPastTense(DELETE).'_items' => $deleted_items,
                            ]
                        ];
                    }
                }

                return [];
            })
            ->all();
    }
}


if (!function_exists(TRASHED.'RelationsData')) {
    /**
     * Get the trashed relations data.
     *
     * @param array $trashedRelations
     * @return array
     */
    function trashedRelationsData(array $trashedRelations): array
    {
        $relations = collect($trashedRelations);

        $message_parts = $relations
            ->map(function (array $info) {
                return match ($info['type']) {
                    'single'   => 'The '.ucfirst($info['label']),
                    'multiple' => 'Some '.ucfirst($info['label']),
                    default    => null,
                };
            })
            ->filter()
            ->values();

        $trashed_relations = $relations
            ->pluck(toPastTense(DELETE).'_items')
            ->filter()
            ->flatten()
            ->values();

        $verb = $message_parts->count() === 1
            ? ' has '
            : ' have ';

        $message = $message_parts->isEmpty()
            ? '<i>Nothing</i>'
            : '<b>'.$message_parts->implode(' and ').$verb.'been '.toPastTense(REMOVE).'</b>';

        return [
            'message'         => $message,
            TRASHED_RELATIONS => $trashed_relations->all(),
        ];
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
            ? array_map('intval', array_filter(
                array_map('trim', explode(',', selectedIdsRequest()))
            ))
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


if (!function_exists(USER_MODEL.'CollectionsData')) {
    /**
     * Get the wishlist and cart data.
     *
     * @param array $vars
     * @return array|array[]
     * @throws Throwable
     */
    function userCollectionsData(array $vars = []): array
    {
        $collections_config = [
            WISHLIST_MODEL => [
                'model'             => Wishlist::class,
                'cache_key'         => WISHLISTS_TABLE.'_'.auth()->id(),
                'empty_session_key' => EMPTY_WISHLIST,
            ],
            CART_MODEL => [
                'model'             => Cart::class,
                'cache_key'         => CARTS_TABLE.'_'.auth()->id(),
                'empty_session_key' => EMPTY_CART,
            ],
        ];

        $compact_vars = [];

        foreach ($collections_config as $type => $config) {
            $collection_ids = cache()->remember($config['cache_key'], 1800, static fn() =>
                $config['model']::query()
                    ->whereHasAuthUser()
                    ->pluck(ID)
                    ->toArray()
            );

            empty($collection_ids)
                ? session()->flash($config['empty_session_key'])
                : session()->forget($config['empty_session_key']);

            $collection = $config['model']::query()
                ->whereIn(ID, $collection_ids)
                ->with(PRODUCT_MODEL, static fn(BelongsTo $product) => $product->select(PRODUCT_ITEM_ATTRIBUTES));

            $items = Route::currentRouteName() === $type
                ? $collection->fastPaginate(5)
                : $collection->cursor();

            $total_items = $type === CART_MODEL
                ? $collection->sum(PRODUCT_QUANTITY)
                : count($collection_ids);

            $collection_data = [
                ITEMS       => $items,
                TOTAL_ITEMS => $total_items,
            ];

            if ($type === CART_MODEL) {
                $total_cost = $collection->cursor()
                    ->sum(static fn($item) =>
                        $item->{PRODUCT_MODEL}->{NEW_PRICE} * $item->{PRODUCT_QUANTITY}
                    );

                $collection_data[TOTAL_COST] = $total_cost;
            }

            $compact_vars[$type] = $collection_data;
        }

        return empty($vars)
            ? $compact_vars
            : $compact_vars + $vars;
    }
}


if (!function_exists(WISHLIST_MODEL.ucfirst(TITLE).'Icon')) {
    /**
     * Check if the product item exists in the user's wishlist,
     * So, return the title or icon of the wishlist button.
     *
     * @param int $productId
     * @param string $property
     * @return string
     * @throws InvalidArgumentException
     */
    function wishlistTitleIcon(int $productId, string $property): string
    {
        match($property) {
            TITLE, 'icon' => null,
            default       => throw new InvalidArgumentException('Property must be either "'.TITLE.'" or "icon"')
        };

        $wishlist_product_exists = Wishlist::query()->whereHasAuthUser()
            ->where(PRODUCT_ID, $productId)
            ?->exists();

        if ($wishlist_product_exists) {
            return $property === TITLE
                ? capitalizeAll(REMOVE.' from '.WISHLIST_MODEL)
                : 'solid';
        }

        return $property === TITLE
            ? capitalizeAll(ADD.' to '.WISHLIST_MODEL)
            : 'regular';
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
            : userCollectionsData($vars);

        return view($viewName, $view_vars);
    }
}


if (!function_exists(EMAIL.'View')) {
    /**
     * Display the view for a specified email.
     *
     * @param string $emailViewName
     * @return string
     */
    function emailView(string $emailViewName): string
    {
        return pluralize(EMAIL).'.'.kebabAll($emailViewName).'-'.EMAIL;
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
        $order_status_badges = [
            'warning'   => 1,
            'secondary' => 2,
            'primary'   => 3,
            'success'   => 4,
            'danger'    => 5,
        ];

        $order_status_icons = [
            'autorenew'      => 1,
            'local_shipping' => 2,
            'done_all'       => 3,
            'check_circle'   => 4,
            'block'          => 5,
        ];

        $order_status       = (int) $order->{STATUS};
        $order_status_name  = array_search($order_status, ORDER_STATUS_ENUM,    true);
        $order_status_badge = array_search($order_status, $order_status_badges, true);
        $order_status_icon  = array_search($order_status, $order_status_icons,  true);

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

        $prices_range = (object) Product::query()
            ->selectRaw('MIN('.NEW_PRICE.') as '.MIN_PRICE.', MAX('.NEW_PRICE.') as '.MAX_PRICE)
            ->first()
            ?->toArray();

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
                    ->whereHas(USER_MODEL, static fn(Builder $user) => $user->withoutTrashed())
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
