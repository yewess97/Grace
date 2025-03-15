<?php

namespace App\Services;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\ThumbImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use stdClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class CategoryService
{
    /**
     * Store or Update a category
     * and its images in the database and storage.
     *
     * @param string $operation
     * @return array
     * @throws ValidationException|NotFoundHttpException|ServiceUnavailableHttpException|RandomException
     */
    final public function createOrUpdateCategory(string $operation): array
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

        return [$category, getLastPage(new Category())];
    }

    /**
     * Delete a specified category
     * and its images from the database & storage.
     *
     * @param Category $category
     * @return bool
     * @throws NotFoundHttpException
     */
    final public function deleteCategory(Category $category): bool
    {
        $this->deleteRelatedCollectionItems($category, (new Subcategory()));
        $this->deleteRelatedCollectionItems($category, (new Product()));

        return delete($category, false, true);
    }

    /**
     * Delete the selected categories
     * and their images from the database & storage.
     *
     * @param Category $categories
     * @return Category|bool
     * @throws NotFoundHttpException
     */
    final public function deleteMultipleCategories(Category $categories): Category|bool
    {
        $this->deleteRelatedCollectionItems($categories, (new Subcategory()));
        $this->deleteRelatedCollectionItems($categories, (new Product()));

        return delete($categories, true, true);
    }

    /**
     * Restore a specified category.
     *
     * @param Category $category
     * @return bool
     */
    final public function restoreCategory(Category $category): bool
    {
        return restore($category);
    }

    /**
     * Restore the selected categories.
     *
     * @param Category $categories
     * @return bool
     */
    final public function restoreMultipleCategories(Category $categories): bool
    {
        return restore($categories, true);
    }

    /**
     * Delete all or Detach the related collection items of category(ies).
     *
     * @param Category $category
     * @param Model|stdClass $model
     * @return void
     */
    private function deleteRelatedCollectionItems(Category $category, Model|stdClass $model): void
    {
        $selected_ids = request()?->input('selected_'.pluralize(ID));

        $categories_ids = $selected_ids
            ? array_map('intval', array_from($selected_ids))
            : [$category->{ID}];

        // Get all related collection items once
        // Used lazy() to improve memory efficiency when handling large datasets
        $related_collection_items = $model::query()->whereHas(CATEGORIES_TABLE, static function ($query) use ($categories_ids) {
            $query->whereIn(ID, $categories_ids)->onlyTrashed();
        })->with(CATEGORIES_TABLE)->lazy();

        $related_collection_items->each(function (Model|stdClass $related_collection_item) use ($categories_ids, $model) {
            // Reduced database queries by using pluck(ID)
            $related_categories_ids = $related_collection_item->{CATEGORIES_TABLE}()->withTrashed()->pluck(ID);

            // Used diff() to efficiently check if the subcategory should be deleted or the category should be detached
            if ($related_categories_ids->diff($categories_ids)->isNotEmpty()) {
                return $related_collection_item->{CATEGORIES_TABLE}()->detach($categories_ids);
            }

            Storage::delete(imageSource($related_collection_item, MAIN_IMAGE, true));

            if ($model instanceof Product) {
                $related_collection_item->{THUMB_IMAGES}->each(static fn(ThumbImage $thumb_image) => Storage::delete(imageSource($thumb_image, THUMB_IMAGE, true)));
            }

            return $related_collection_item->forceDelete();
        });
    }
}
