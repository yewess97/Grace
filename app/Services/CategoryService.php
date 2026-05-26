<?php

namespace App\Services;

use App\Contracts\ServiceData;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\ThumbImage;
use App\Notifications\NewAdminActionTaken;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use stdClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;

class CategoryService implements ServiceData
{
    /**
     * Store or Update a category
     * and its images in the database and storage.
     *
     * @param string $operation
     * @return Category
     * @throws ValidationException|NotFoundHttpException|ServiceUnavailableHttpException|RandomException|CacheInvalidArgumentException
     */
    final public function createOrUpdateCategory(string $operation): Category
    {
        $category_id = request()?->input(UPDATE_CATEGORY_ID);

        $validated_category_request = $this->validateRequest($operation, compact(CATEGORY_ID));

        $category = $this->createOrUpdateCollection($validated_category_request, compact(CATEGORY_ID));

        $this->forgetCollectionCache();

        sendNotificationToAdmins(new NewAdminActionTaken([$category, $category->{NAME}], $operation), true);

        return $category;
    }

    /**
     * Delete a specified category
     * and its images from the database & storage.
     *
     * @param Category $category
     * @return bool
     * @throws NotFoundHttpException|CacheInvalidArgumentException
     */
    final public function deleteCategory(Category $category): bool
    {
        $this->deleteRelatedCollectionItems($category, Subcategory::class);
        $this->deleteRelatedCollectionItems($category, Product::class);

        $deleted_category = removeDeleteOrRestore($category, $category->{NAME});

        $this->forgetCollectionCache();

        return $deleted_category;
    }

    /**
     * Delete the selected categories
     * and their images from the database & storage.
     *
     * @param Category $categories
     * @return Category|bool
     * @throws NotFoundHttpException|CacheInvalidArgumentException
     */
    final public function deleteMultipleCategories(Category $categories): Category|bool
    {
        $this->deleteRelatedCollectionItems($categories, Subcategory::class);
        $this->deleteRelatedCollectionItems($categories, Product::class);

        $deleted_categories = removeDeleteOrRestore($categories);

        $this->forgetCollectionCache();

        return $deleted_categories;
    }

    /**
     * Restore a specified category.
     *
     * @param Category $category
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function restoreCategory(Category $category): bool
    {
        $restored_category = removeDeleteOrRestore($category, $category->{NAME});

        $this->forgetCollectionCache();

        return $restored_category;
    }

    /**
     * Restore the selected categories.
     *
     * @param Category $categories
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function restoreMultipleCategories(Category $categories): bool
    {
        $restored_categories = removeDeleteOrRestore($categories);

        $this->forgetCollectionCache();

        return $restored_categories;
    }

    /**
     * Validate and return the category request.
     *
     * @param string $operation
     * @param array $extra
     * @return CategoryRequest
     * @throws ValidationException
     */
    final public function validateRequest(string $operation, array $extra = []): CategoryRequest
    {
        $category_request = new CategoryRequest($operation, CATEGORY_MODEL, CATEGORY_ATTRIBUTES);

        validateAttributes($category_request, $extra[CATEGORY_ID]);

        return $category_request;
    }

    /**
     * Create or Update the category.
     *
     * @param FormRequest|CategoryRequest $collectionRequest
     * @param array $extra
     * @return Category
     * @throws NotFoundHttpException|ServiceUnavailableHttpException|RandomException
     */
    final public function createOrUpdateCollection(FormRequest|CategoryRequest $collectionRequest, array $extra): Category
    {
        [$name, $main_image, $banner_image] = CATEGORY_ATTRIBUTES;

        [$name_value, $main_image_value, $banner_image_value] = $collectionRequest->dataValues();

        $main_image_name   = storeOrUpdateImage(MAIN_IMAGE,   new Category(), $extra[CATEGORY_ID], $main_image_value, checkImageBackgroundRequest());
        $banner_image_name = storeOrUpdateImage(BANNER_IMAGE, new Category(), $extra[CATEGORY_ID], $banner_image_value, checkImageBackgroundRequest());

        return Category::query()->updateOrCreate(
            [ID => $extra[CATEGORY_ID]],
            [
                $name         => $name_value,
                SLUG          => str($name_value)->slug(),
                $main_image   => $main_image_name,
                $banner_image => $banner_image_name,
            ]
        );
    }

    /**
     * Forget the category cache.
     *
     * @param Model|null $model
     * @return void
     * @throws CacheInvalidArgumentException
     */
    final public function forgetCollectionCache(Model $model = null): void
    {
        forgetCache([CATEGORIES_PAGINATION_CACHE_KEY, SUBCATEGORIES_PAGINATION_CACHE_KEY, PRODUCTS_PAGINATION_CACHE_KEY, REVIEWS_PAGINATION_CACHE_KEY, CATEGORIES_FOR_SUBCATEGORIES_CACHE_KEY, CATEGORIES_FOR_PRODUCTS_CACHE_KEY, HOME_PRODUCTS, PRODUCTS_TABLE]);
    }

    /**
     * Delete all or Detach the related collection items of category(ies).
     *
     * @param Category $category
     * @param string $modelClass
     * @return void
     */
    private function deleteRelatedCollectionItems(Category $category, string $modelClass): void
    {
        $category_ids = selectedIdsRequest()
            ? array_map('intval', array_filter(
                array_map('trim', explode(',', selectedIdsRequest()))
            ))
            : [$category->{ID}];

        $modelClass::query()
            ->whereHas(CATEGORIES_TABLE, static fn(Builder $query) =>
                $query->whereIn(ID, $category_ids)->onlyTrashed()
            )
            ->with([CATEGORIES_TABLE => static fn(Builder $query) => $query->withTrashed()])
            ->cursor()
            ->each(function (Model|stdClass $related_collection_item) use ($category_ids, $modelClass) {
                $related_category_ids = $related_collection_item->{CATEGORIES_TABLE}
                    ->pluck(ID);

                // If the item still has other categories attached -> detach only
                if ($related_category_ids->diff($category_ids)->isNotEmpty()) {
                    return $related_collection_item->{CATEGORIES_TABLE}()
                        ->detach($category_ids);
                }

                $this->deleteRelatedCollectionImages($related_collection_item, $modelClass);

                return $related_collection_item->forceDelete();
            });
    }

    /**
     * Delete all images of the related collection items of category(ies).
     *
     * @param Model|stdClass $related_collection_item
     * @param string $modelClass
     * @return void
     */
    private function deleteRelatedCollectionImages(Model|stdClass $related_collection_item, string $modelClass): void
    {
        $images_paths = [imageSource($related_collection_item, MAIN_IMAGE, true)];

        if ($modelClass === Product::class) {
            $images_paths = array_merge(
                $images_paths,
                $related_collection_item->{THUMB_IMAGES}->map(static fn(ThumbImage $thumb_image) =>
                    imageSource($thumb_image, THUMB_IMAGE, true)
                )->toArray()
            );
        }

        Storage::delete(array_filter($images_paths));
    }
}
