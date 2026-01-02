<?php

namespace App\Services;

use App\Http\Requests\SubcategoryRequest;
use App\Models\Subcategory;
use App\Notifications\NewAdminActionTaken;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;

class SubcategoryService
{
    /**
     * Store or Update a subcategory
     * and its main image in the database and storage.
     *
     * @param string $operation
     * @return Subcategory
     * @throws ValidationException|NotFoundHttpException|ServiceUnavailableHttpException|RandomException|CacheInvalidArgumentException
     */
    final public function createOrUpdateSubcategory(string $operation): Subcategory
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

        $this->forgetSubcategoryCache();

        sendNotificationToAdmins(new NewAdminActionTaken([$subcategory, $subcategory->{NAME}], $operation), true);

        return $subcategory;
    }

    /**
     * Delete a specified subcategory
     * and its main image from the database and storage.
     *
     * @param Subcategory $subcategory
     * @return bool
     * @throws NotFoundHttpException|CacheInvalidArgumentException
     */
    final public function deleteSubcategory(Subcategory $subcategory): bool
    {
        $deleted_subcategory = removeDeleteOrRestore($subcategory, $subcategory->{NAME});

        $this->forgetSubcategoryCache();

        return $deleted_subcategory;
    }

    /**
     * Delete the selected subcategories
     * and their main images from the database and storage.
     *
     * @param Subcategory $subcategories
     * @return bool
     * @throws NotFoundHttpException|CacheInvalidArgumentException
     */
    final public function deleteMultipleSubcategories(Subcategory $subcategories): bool
    {
        $deleted_subcategories = removeDeleteOrRestore($subcategories);

        $this->forgetSubcategoryCache();

        return $deleted_subcategories;
    }

    /**
     * Restore a specified subcategory.
     *
     * @param Subcategory $subcategory
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function restoreSubcategory(Subcategory $subcategory): bool
    {
        $restored_subcategory = removeDeleteOrRestore($subcategory, $subcategory->{NAME});

        $this->forgetSubcategoryCache();

        return $restored_subcategory;
    }

    /**
     * Restore the selected subcategories.
     *
     * @param Subcategory $subcategories
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function restoreMultipleSubcategories(Subcategory $subcategories): bool
    {
        $restored_subcategories = removeDeleteOrRestore($subcategories);

        $this->forgetSubcategoryCache();

        return $restored_subcategories;
    }

    /**
     * Forget the subcategory cache.
     *
     * @return void
     * @throws CacheInvalidArgumentException
     */
    private function forgetSubcategoryCache(): void
    {
        forgetCache([SUBCATEGORIES_PAGINATION_CACHE_KEY, PRODUCTS_PAGINATION_CACHE_KEY, SUBCATEGORIES_FOR_PRODUCTS_CACHE_KEY, HOME_PRODUCTS, PRODUCTS_TABLE]);
    }
}
