<?php

namespace App\Services;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Notifications\NewAdminActionTaken;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Random\RandomException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
use Throwable;

class ProductService
{
    /**
     * Get the product details.
     *
     * @param string $productSlug
     * @return Application|Factory|View|array|string
     * @throws Throwable
     */
    final public function getProductDetails(string $productSlug): Application|Factory|View|array|string
    {
        $product = cache()->remember(PRODUCT_MODEL.'_'.$productSlug, 500, static fn() =>
            Product::query()->with([
                REVIEWS_TABLE,
                SIZES => static fn(HasMany $sizes) => $sizes->select(SIZE, PRODUCT_ID),
            ])
                ->whereSlug($productSlug)
                ->withoutTrashed()
                ->first()
        );

        if (is_null($product)) {
            throw new ModelNotFoundException("This ".PRODUCT_MODEL." is not found!");
        }

        $add_cart_product_error = static fn(string $attributeName) => formError(ADD, CART_MODEL, $attributeName);

        if (request()?->ajax()) {
            return request()?->input(QUICK_VIEW)
                ? compact(PRODUCT_MODEL)
                : view(REVIEWS_COMPONENT, getReviews($product->{ID}) + compact(PRODUCT_MODEL, PRODUCT_MODEL.ucfirst(SLUG)))->render();
        }

        return showView(USER_PRODUCT_DETAILS_VIEW, getReviews($product->{ID}) + compact(PRODUCT_MODEL, ADD_CART_PRODUCT_ERROR, PRODUCT_MODEL.ucfirst(SLUG)));
    }

    /**
     * Store or Update a product
     * and its images in the database and storage.
     *
     * @param string $operation
     * @return array
     * @throws ValidationException|NotFoundHttpException|ServiceUnavailableHttpException|RandomException|CacheInvalidArgumentException
     */
    final public function createOrUpdateProduct(string $operation): array
    {
        $product_attributes = PRODUCT_ATTRIBUTES;
        $thumb_image_input_name = "{$operation}_".PRODUCT_MODEL."_".THUMB_IMAGE;

        if (request()?->hasFile($thumb_image_input_name)) {
            $product_attributes[] = THUMB_IMAGE;
        }

        $product_request = new ProductRequest($operation, PRODUCT_MODEL, $product_attributes);

        $product_id = request()?->input(UPDATE_PRODUCT_ID);

        validateAttributes($product_request, $product_id);

        [$name, $short_description, $long_description, $main_image, $related_categories_ids, $related_subcategories_ids, $sizes, $old_price, $new_price, $quantity, $status] = $product_attributes;

        [$name_value, $short_description_value, $long_description_value, $main_image_value, $related_categories_ids_values, $related_subcategories_ids_values, $sizes_values, $old_price_value, $new_price_value, $quantity_value, $status_value] = $product_request->dataValues();

        $main_image_name = storeOrUpdateImage(new Product(), $product_id, MAIN_IMAGE, $main_image_value);

        $product = Product::query()->updateOrCreate(
            [ID => $product_id],
            [
                $name              => $name_value,
                SLUG               => str($name_value)->slug(),
                $short_description => $short_description_value,
                $long_description  => $long_description_value,
                $main_image        => $main_image_name,
                $old_price         => $old_price_value,
                $new_price         => $new_price_value,
                $quantity          => $quantity_value,
                $status            => $status_value,
            ]);

        $new_product_id = [PRODUCT_ID => $product->{ID}];

        /*---------------------------- One to Many Relationships ----------------------------*/
        // Product Thumbnail Images
        if (Arr::last($product_attributes) === THUMB_IMAGE) {
            $thumb_images = request()?->file($thumb_image_input_name);
            $thumb_images_data = array_map(static function (UploadedFile $thumb_image) use ($new_product_id) {
                $thumb_image_path = "public/images/".PRODUCTS_TABLE.DIRECTORY_SEPARATOR.THUMB_IMAGES_TABLE;
                $thumb_image_name = storeImageWithoutBackground($thumb_image, $thumb_image_path);
//                $thumb_image_name = time().random_int(10, 100).'.png';
//                $thumb_image->storeAs($thumb_image_path, $thumb_image_name);

                return [
                    THUMB_IMAGE => $thumb_image_name,
                    ...$new_product_id
                ];
            }, $thumb_images);

            $product->{THUMB_IMAGES}()->upsert($thumb_images_data, [THUMB_IMAGE, PRODUCT_ID]);
        }

        // Product Sizes
        $sizes_values = array_filter((array) $sizes_values);
        array_walk($sizes_values, static function (&$size_value) use ($new_product_id) {
            $size_value = [
                SIZE => $size_value,
                ...$new_product_id
            ];
        });
        $product->{SIZES}()->delete();
        $product->{SIZES}()->createMany($sizes_values);

        /*---------------------------- Many to Many Relationships ----------------------------*/
        // Product Related Categories
        createOrUpdateMultipleCollections($product, CATEGORIES_TABLE, $related_categories_ids_values);

        // Product Related Subcategories
        createOrUpdateMultipleCollections($product, SUBCATEGORIES_TABLE, $related_subcategories_ids_values);

        $this->forgetProductCache($product);

        sendNotificationToAdmins(new NewAdminActionTaken([$product, $product->{NAME}], $operation), true);

        return [$product, getLastPage(new Product())];
    }

    /**
     * Delete a specified product
     * and its images from the database and storage.
     *
     * @param Product $product
     * @return bool
     * @throws NotFoundHttpException|CacheInvalidArgumentException
     */
    final public function deleteProduct(Product $product): bool
    {
        $deleted_product = removeDeleteOrRestore($product, $product->{NAME}, true);

        $this->forgetProductCache($product);

        return $deleted_product;
    }

    /**
     * Delete the selected products
     * and their images from the database and storage.
     *
     * @param Product $products
     * @return bool
     * @throws NotFoundHttpException|CacheInvalidArgumentException
     */
    final public function deleteMultipleProducts(Product $products): bool
    {
        $deleted_products = removeDeleteOrRestore($products, deleteImages: true);

        $this->forgetProductCache($products);

        return $deleted_products;
    }

    /**
     * Restore a specified product.
     *
     * @param Product $product
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function restoreProduct(Product $product): bool
    {
        $restored_product = removeDeleteOrRestore($product, $product->{NAME});

        $this->forgetProductCache($product);

        return $restored_product;
    }

    /**
     * Restore the selected products.
     *
     * @param Product $products
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function restoreMultipleProducts(Product $products): bool
    {
        $restored_products = removeDeleteOrRestore($products);

        $this->forgetProductCache($products);

        return $restored_products;
    }

    /**
     * Forget the product cache.
     *
     * @param Product $product
     * @return void
     * @throws CacheInvalidArgumentException
     */
    private function forgetProductCache(Product $product): void
    {
        forgetCache([PRODUCTS_PAGINATION_CACHE_KEY, REVIEWS_PAGINATION_CACHE_KEY, HOME_PRODUCTS, PRODUCTS_TABLE]);
        forgetCache(PRODUCT_MODEL, $product, SLUG);
    }
}
