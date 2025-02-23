<?php

namespace App\Services;

use App\Http\Requests\SubcategoryRequest;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\ThumbImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubcategoryService
{
    /**
     * Store or Update a subcategory
     * and its main image in the database and storage.
     *
     * @param string $operation
     * @return Subcategory
     * @throws ValidationException|RandomException
     */
    final public function createOrUpdateSubcategory(string $operation): Subcategory
    {
        $subcategory_request = new SubcategoryRequest($operation, SUBCATEGORY_MODEL, SUBCATEGORY_ATTRIBUTES);

        $subcategory_id = request()?->input(UPDATE_SUBCATEGORY_ID);

        validateAttributes($subcategory_request, $subcategory_id);

        [$name, $main_image] = SUBCATEGORY_ATTRIBUTES;

        [$name_value, $main_image_value, $related_categories_ids_values] = $subcategory_request->dataValues();

        $main_image_name = storeOrUpdateImage(new Subcategory(), $subcategory_id, MAIN_IMAGE, $main_image_value);

        $create_or_update_subcategory = Subcategory::query()->updateOrCreate(
            [ID => $subcategory_id],
            [
                $name       => $name_value,
                SLUG        => str($name_value)->slug(),
                $main_image => $main_image_name,
            ]
        );

        createOrUpdateMultipleCollections($create_or_update_subcategory, CATEGORIES_TABLE, $related_categories_ids_values);

        return $create_or_update_subcategory;
    }

    /**
     * Get the data of a specified subcategory
     * with its related categories.
     *
     * @param Subcategory $subcategory
     * @return JsonResponse
     */
    final public function getSubcategoryData(Subcategory $subcategory): JsonResponse
    {
        return responseWithData([SUBCATEGORY_MODEL => $subcategory->data]);
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
        $this->deleteRelatedProducts($subcategory);

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
        $this->deleteRelatedProducts($subcategories);

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

    /**
     * Delete all or Detach the related products of subcategory(ies).
     *
     * @param Subcategory $subcategory
     * @return void
     */
    final public function deleteRelatedProducts(Subcategory $subcategory): void
    {
        $selected_ids = request()?->input('selected_'.pluralize(ID));

        $subcategories_ids = $selected_ids
            ? array_map('intval', array_from($selected_ids))
            : [$subcategory->{ID}];

        // Get all related products once
        // Used lazy() to improve memory efficiency when handling large datasets
        $related_products = Product::whereHas(SUBCATEGORIES_TABLE, static function ($query) use ($subcategories_ids) {
            $query->whereIn(ID, $subcategories_ids)->onlyTrashed();
        })->with(SUBCATEGORIES_TABLE)->lazy();

        $related_products->each(function (Product $related_product) use ($subcategories_ids) {
            // Reduced database queries by using pluck(ID)
            $related_subcategories_ids = $related_product->{SUBCATEGORIES_TABLE}()->withTrashed()->pluck(ID);

            // Used diff() to efficiently check if the product should be deleted or the subcategory should be detached
            if ($related_subcategories_ids->diff($subcategories_ids)->isNotEmpty()) {
                return $related_product->{SUBCATEGORIES_TABLE}()->detach($subcategories_ids);
            }

            Storage::delete(imageSource($related_product, MAIN_IMAGE, true));
            $related_product->{THUMB_IMAGES}->each(static fn(ThumbImage $thumb_image) => Storage::delete(imageSource($thumb_image, THUMB_IMAGE, true)));

            return $related_product->forceDelete();
        });
    }
}
