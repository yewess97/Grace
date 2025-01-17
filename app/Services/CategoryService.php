<?php

namespace App\Services;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
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
     * and its images from the database and storage.
     *
     * @param Category $category
     * @return bool
     * @throws NotFoundHttpException
     */
    final public function deleteCategory(Category $category): bool
    {
        $category->deleteRelatedSubcategories();

        return delete($category, false, true);
    }

    /**
     * Delete the selected categories
     * and their images from the database and storage.
     *
     * @param Category $categories
     * @return bool
     * @throws NotFoundHttpException
     */
    final public function deleteMultipleCategories(Category $categories): bool
    {
        $categories->deleteRelatedSubcategories();

        return delete($categories, true, true);
    }
}
