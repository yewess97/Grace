<?php

namespace App\Services;

use App\Http\Requests\SubcategoryRequest;
use App\Models\Subcategory;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class SubcategoryService
{
    /**
     * Store or Update a subcategory
     * and its main image in the database and storage.
     *
     * @param string $operation
     * @return array
     * @throws ValidationException|NotFoundHttpException|ServiceUnavailableHttpException|RandomException
     */
    final public function createOrUpdateSubcategory(string $operation): array
    {
        $subcategory_request = new SubcategoryRequest($operation, SUBCATEGORY_MODEL, SUBCATEGORY_ATTRIBUTES);

        $subcategory_id = request()?->input(UPDATE_SUBCATEGORY_ID);

        validateAttributes($subcategory_request, $subcategory_id);

        [$name, $main_image] = SUBCATEGORY_ATTRIBUTES;

        [$name_value, $main_image_value, $related_categories_ids_values] = $subcategory_request->dataValues();

        $main_image_name = storeOrUpdateImage(new Subcategory(), $subcategory_id, MAIN_IMAGE, $main_image_value);

        $subcategory = Subcategory::query()->updateOrCreate(
            [ID => $subcategory_id],
            [
                $name       => $name_value,
                SLUG        => str($name_value)->slug(),
                $main_image => $main_image_name,
            ]
        );

        createOrUpdateMultipleCollections($subcategory, CATEGORIES_TABLE, $related_categories_ids_values);

        return [$subcategory, getLastPage(new Subcategory())];
    }

    /**
     * Delete a specified subcategory
     * and its main image from the database and storage.
     *
     * @param Subcategory $subcategory
     * @return bool
     * @throws NotFoundHttpException
     */
    final public function deleteSubcategory(Subcategory $subcategory): bool
    {
        return delete($subcategory, false, true);
    }

    /**
     * Delete the selected subcategories
     * and their main images from the database and storage.
     *
     * @param Subcategory $subcategories
     * @return bool
     * @throws NotFoundHttpException
     */
    final public function deleteMultipleSubcategories(Subcategory $subcategories): bool
    {
        return delete($subcategories, true, true);
    }

    /**
     * Restore a specified subcategory.
     *
     * @param Subcategory $subcategory
     * @return bool
     */
    final public function restoreSubcategory(Subcategory $subcategory): bool
    {
        return restore($subcategory);
    }

    /**
     * Restore the selected subcategories.
     *
     * @param Subcategory $subcategories
     * @return bool
     */
    final public function restoreMultipleSubcategories(Subcategory $subcategories): bool
    {
        return restore($subcategories, true);
    }
}
