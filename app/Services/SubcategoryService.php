<?php

namespace App\Services;

use App\Contracts\ServiceData;
use App\Http\Requests\SubcategoryRequest;
use App\Models\Subcategory;
use App\Notifications\NewAdminActionTaken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;

class SubcategoryService implements ServiceData
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
        $subcategory_id = request()?->input(UPDATE_SUBCATEGORY_ID);

        $validated_subcategory_request = $this->validateRequest($operation, compact(SUBCATEGORY_ID));

        $subcategory = $this->createOrUpdateCollection($validated_subcategory_request, compact(SUBCATEGORY_ID));

        $this->forgetCollectionCache();

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

        $this->forgetCollectionCache();

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

        $this->forgetCollectionCache();

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

        $this->forgetCollectionCache();

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

        $this->forgetCollectionCache();

        return $restored_subcategories;
    }

    /**
     * Validate and return the subcateory request.
     *
     * @param string $operation
     * @param array $extra
     * @return SubcategoryRequest
     * @throws ValidationException
     */
    final public function validateRequest(string $operation, array $extra = []): SubcategoryRequest
    {
        $subcategory_request = new SubcategoryRequest($operation, SUBCATEGORY_MODEL, SUBCATEGORY_ATTRIBUTES);

        validateAttributes($subcategory_request, $extra[SUBCATEGORY_ID]);

        return $subcategory_request;
    }

    /**
     * Create or Update the subcateory.
     *
     * @param FormRequest|SubcategoryRequest $collectionRequest
     * @param array $extra
     * @return Subcategory|JsonResponse
     * @throws NotFoundHttpException|ServiceUnavailableHttpException|RandomException
     */
    final public function createOrUpdateCollection(FormRequest|SubcategoryRequest $collectionRequest, array $extra): Subcategory|JsonResponse
    {
        [$name, $main_image] = SUBCATEGORY_ATTRIBUTES;

        [$name_value, $main_image_value, $related_categories_ids_values] = $collectionRequest->dataValues();

        $main_image_name = storeOrUpdateImage(MAIN_IMAGE, new Subcategory(), $extra[SUBCATEGORY_ID], $main_image_value, checkImageBackgroundRequest());

        $subcategory = Subcategory::query()->updateOrCreate(
            [ID => $extra[SUBCATEGORY_ID]],
            [
                $name       => $name_value,
                SLUG        => str($name_value)->slug(),
                $main_image => $main_image_name,
            ]
        );

        createOrUpdateMultipleCollections($subcategory, CATEGORIES_TABLE, $related_categories_ids_values);

        return $subcategory;
    }

    /**
     * Forget the subcategory cache.
     *
     * @param Model|Subcategory|null $model
     * @return void
     * @throws CacheInvalidArgumentException
     */
    final public function forgetCollectionCache(Model|Subcategory $model = null): void
    {
        forgetCache([SUBCATEGORIES_PAGINATION_CACHE_KEY, PRODUCTS_PAGINATION_CACHE_KEY, SUBCATEGORIES_FOR_PRODUCTS_CACHE_KEY, HOME_PRODUCTS, PRODUCTS_TABLE]);
    }
}
