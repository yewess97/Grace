<?php

namespace App\Services;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryService
{
    /**
     * Store or Update a category
     * and its images in the database and storage.
     *
     * @param string $operation
     * @return Category
     * @throws ValidationException|RandomException
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

        return Category::query()->updateOrCreate(
            [ID => $category_id],
            [
                $name         => $name_value,
                SLUG          => str($name_value)->slug(),
                $main_image   => $main_image_name,
                $banner_image => $banner_image_name,
            ]
        );
    }

    /**
     * Get the data of a specified category.
     *
     * @param Category $category
     * @return JsonResponse
     */
    final public function getCategoryData(Category $category): JsonResponse
    {
        return responseWithData([CATEGORY_MODEL => $category->data]);
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
        $this->deleteRelatedSubcategories($category);

        return delete($category, false, true);
    }

    /**
     * Delete the selected categories
     * and their images from the database & storage.
     *
     * @param Category $categories
     * @return Category|bool
     */
    final public function deleteMultipleCategories(Category $categories): Category|bool
    {
        $this->deleteRelatedSubcategories($categories);

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
     * Delete all or Detach the related subcategories of category(ies).
     *
     * @param Category $category
     * @return void
     */
    private function deleteRelatedSubcategories(Category $category): void
    {
        $selected_ids = request()?->input('selected_'.pluralize(ID));

        $categories_ids = $selected_ids
            ? array_map('intval', array_from($selected_ids))
            : [$category->{ID}];

        // Get all related subcategories once
        // Used lazy() to improve memory efficiency when handling large datasets
        $related_subcategories = Subcategory::whereHas(CATEGORIES_TABLE, static function ($query) use ($categories_ids) {
            $query->whereIn(ID, $categories_ids)->onlyTrashed();
        })->with(CATEGORIES_TABLE)->lazy();

        $related_subcategories->each(function (Subcategory $related_subcategory) use ($categories_ids) {
            // Reduced database queries by using pluck(ID)
            $related_categories_ids = $related_subcategory->{CATEGORIES_TABLE}()->withTrashed()->pluck(ID);

            // Used diff() to efficiently check if the subcategory should be deleted or the category should be detached
            if ($related_categories_ids->diff($categories_ids)->isNotEmpty()) {
                return $related_subcategory->{CATEGORIES_TABLE}()->detach($categories_ids);
            }

            Storage::delete(imageSource($related_subcategory, MAIN_IMAGE, true));

            return $related_subcategory->forceDelete();
        });
    }
}
