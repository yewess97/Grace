<?php

namespace App\Services;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\ThumbImage;
use App\Notifications\NewAdminActionTaken;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use stdClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;

class CategoryService
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
        $category_request = new CategoryRequest($operation, CATEGORY_MODEL, CATEGORY_ATTRIBUTES);

        $category_id = request()?->input(UPDATE_CATEGORY_ID);

        validateAttributes($category_request, $category_id);

        [$name, $main_image, $banner_image] = CATEGORY_ATTRIBUTES;

        [$name_value, $main_image_value, $banner_image_value] = $category_request->dataValues();

        $main_image_name   = storeOrUpdateImage(new Category(), $category_id, MAIN_IMAGE,   $main_image_value);
        $banner_image_name = storeOrUpdateImage(new Category(), $category_id, BANNER_IMAGE, $banner_image_value);

        $category = Category::query()->updateOrCreate(
            [ID => $category_id],
            [
                $name         => $name_value,
                SLUG          => str($name_value)->slug(),
                $main_image   => $main_image_name,
                $banner_image => $banner_image_name,
            ]
        );

        $this->forgetCategoryCache();

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

        $this->forgetCategoryCache();

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

        $this->forgetCategoryCache();

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

        $this->forgetCategoryCache();

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

        $this->forgetCategoryCache();

        return $restored_categories;
    }

    /**
     * Delete all or Detach the related collection items of category(ies).
     *
     * @param Category $category
     * @param string $modelClass
     * @return void
     * @throws CacheInvalidArgumentException
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

        $this->forgetCategoryCache();
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

    /**
     * Forget the category cache.
     *
     * @return void
     * @throws CacheInvalidArgumentException
     */
    private function forgetCategoryCache(): void
    {
        forgetCache([CATEGORIES_PAGINATION_CACHE_KEY, SUBCATEGORIES_PAGINATION_CACHE_KEY, PRODUCTS_PAGINATION_CACHE_KEY, CATEGORIES_FOR_SUBCATEGORIES_CACHE_KEY, CATEGORIES_FOR_PRODUCTS_CACHE_KEY, HOME_PRODUCTS, PRODUCTS_TABLE]);
    }
}
