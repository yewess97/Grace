<?php

namespace App\Services;

use App\Http\Requests\CartRequest;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartService
{
    /**
     * Store or Update a cart.
     *
     * @param string $operation
     * @return array
     * @throws AuthenticationException|ModelNotFoundException|ValidationException
     */
    final public function createOrUpdateCart(string $operation): array
    {
        if (!auth()->check()) {
            throw new AuthenticationException('unauthenticated');
        }

        $cart_attributes = [PRODUCT_ID];

        $cart_attributes = $this->mergeIfRequestHas($this->getActionNames($operation), $cart_attributes, CART_COMMON_ATTRIBUTES);

        $cart_attributes = $this->mergeIfRequestHas($this->getActionNames($operation, true), $cart_attributes, QUICK_VIEW_COMMON_ATTRIBUTES);

        $cart_request = new CartRequest($operation, CART_MODEL, $cart_attributes);

        $product_id = $cart_attributes[0];

        if ($operation === UPDATE) {
            $updated_cart = $this->updateCartItems($cart_request, $product_id);

            return [$updated_cart, getLastPage(new Cart(), 5)];
        }

        $product_id_value       = (int)   $cart_request->dataValues()[0];
        $product_sizes_values   = (array) ($cart_request->dataValues()[1] ?? 3);
        $product_quantity_value = (int)   ($cart_request->dataValues()[2] ?? 1);

        $product = $this->getProductOrFail($product_id_value);

        $max_sizes_count = count($product->{SIZES}) + 1;

        validateAttributes($cart_request, $max_sizes_count);

        $cart_relations = [
            USER_ID     => auth()->id(),
            $product_id => $product->getKey(),
        ];

        $created_cart = $this->createCartItem($cart_attributes, $cart_relations, $product_sizes_values, $product_quantity_value);

        return [$created_cart, getLastPage(new Cart(), 5)];
    }

    /**
     * Delete a specified cart
     * or decrement the cart's product quantity if it's greater than 1.
     *
     * @param Cart $cart
     * @return array|bool
     * @throws ModelNotFoundException
     */
    final public function deleteCart(Cart $cart): array|bool
    {
        $cart_item = Cart::query()->findOrFail($cart->getKey());

        if (is_null($cart_item)) {
            throw new ModelNotFoundException("This ".CART_MODEL."is not found!");
        }

        if ($cart_item->{PRODUCT_QUANTITY} > 1) {
            $cart_item->decrement(PRODUCT_QUANTITY);

            return ['decremented'];
        }

        return delete($cart_item);
    }

    /**
     * Delete all user's carts.
     *
     * @return int
     */
    final public function deleteAllCarts(): int
    {
        $delete_user_carts = Cart::query()->where(USER_ID, auth()->id())->get();

        return Cart::destroy($delete_user_carts);
    }

    /**
     * Get the action names for the cart items.
     *
     * @param string $operation
     * @param bool $isQuickView
     * @return array
     */
    private function getActionNames(string $operation, bool $isQuickView = false): array
    {
        $prefix = $operation.'_'.CART_MODEL.'_';
        $suffix = $isQuickView ?
            '_'.QUICK_VIEW
            : '';

        return [
            $prefix.PRODUCT_SIZE.$suffix,
            $prefix.PRODUCT_QUANTITY.$suffix,
        ];
    }

    /**
     * Merge the cart attributes with the attributes that are in the request,
     * if the request has the keys.
     *
     * @param array $keys
     * @param array $cartAttributes
     * @param array $attributes
     * @return array
     */
    private function mergeIfRequestHas(array $keys, array $cartAttributes, array $attributes): array
    {
        return request()?->hasAny($keys)
            ? array_merge($cartAttributes, $attributes)
            : $cartAttributes;
    }

    /**
     * Update the cart items.
     *
     * @param CartRequest $cartRequest
     * @param string $productId
     * @return Collection
     * @throws ModelNotFoundException
     */
    private function updateCartItems(CartRequest $cartRequest, string $productId): Collection
    {
        [$products_ids_values, $products_sizes_values, $products_quantities_values] = $cartRequest->dataValues();

        $products_ids_values        = array_filter((array) $products_ids_values);
        $products_quantities_values = array_filter((array) $products_quantities_values);

        $products_ids = $this->getProductOrFail($products_ids_values, true);

        $cart_items = Cart::query()->where(USER_ID, auth()->id())
            ->whereIn($productId, $products_ids)
            ->whereIn(PRODUCT_SIZE, $products_sizes_values)
            ->get([ID, PRODUCT_ID, ...CART_COMMON_ATTRIBUTES])
            ->load([PRODUCT_MODEL => fn(BelongsTo $product) => $product->select(PRODUCT_ITEM_ATTRIBUTES)]);


        if ($cart_items->count() !== count($products_quantities_values)) {
            throw new ModelNotFoundException('The number of the '.PRODUCTS_TABLE.' in the '.CART_MODEL.' is not equal to the number of the received quantities!');
        }

        return $cart_items->each(static function (Model $cartItem, int $key) use ($products_quantities_values) {
            $cartItem->{PRODUCT_QUANTITY} = +$products_quantities_values[$key];
            $cartItem->save();
        });
    }

    /**
     * Get the product or the products' ids,
     * or throw an exception.
     *
     * @param int|array $productIdValue
     * @param bool $isUpdateAction
     * @return Product|array
     * @throws ModelNotFoundException
     */
    private function getProductOrFail(int|array $productIdValue, bool $isUpdateAction = false): Product|array
    {
        if ($isUpdateAction) {
            $available_products_ids = Product::query()->whereIn(ID, $productIdValue)
                ->whereStatus(1)
                ->pluck(ID)
                ->toArray();

            $wrong_ids = array_diff($productIdValue, $available_products_ids);

            if (count($wrong_ids)) {
                throw new ModelNotFoundException("There's one or more ".PRODUCT_MODEL." that is/are not found!");
            }

            return $available_products_ids;
        }

        $product = Product::query()->whereId($productIdValue)
            ->whereStatus(1)
            ->first([ID, NAME, SLUG, MAIN_IMAGE, NEW_PRICE, STATUS])
            ->load(SIZES);

        if (!$product) {
            throw new ModelNotFoundException('The '.PRODUCT_MODEL.' you want is not found!');
        }

        return $product;
    }

    /**
     * Check if the cart (product_size, product_quantity) attributes are existing.
     *
     * @param array $cartAttributes
     * @return bool
     */
    private function shouldCheckCartAttributes(array $cartAttributes): bool
    {
        return empty(array_intersect(CART_COMMON_ATTRIBUTES, $cartAttributes))
            && empty(array_intersect(QUICK_VIEW_COMMON_ATTRIBUTES, $cartAttributes));
    }

    /**
     * Create a cart item
     * or increment the product quantity if the cart item is found.
     *
     * @param array $cartAttributes
     * @param array $cartRelations
     * @param array $productSizesValues
     * @param int $productQuantityValue
     * @return Cart|bool
     */
    private function createCartItem(array $cartAttributes, array &$cartRelations, array &$productSizesValues, int &$productQuantityValue): Cart|bool
    {
        if ($this->shouldCheckCartAttributes($cartAttributes)) {
            $first_found_cart = Cart::query()->where($cartRelations)
                ->whereIn(PRODUCT_SIZE, $productSizesValues)
                ->first();

            return $first_found_cart
                ? $first_found_cart->increment(PRODUCT_QUANTITY)
                : Cart::query()->create($cartRelations);
        }

        return array_walk($productSizesValues, static function (int $product_size_value) use (&$cartRelations, &$productQuantityValue) {
            $first_found_cart_item = Cart::query()->firstWhere([
                ...$cartRelations,
                PRODUCT_SIZE => $product_size_value,
            ]);

            if ($first_found_cart_item) {
                $first_found_cart_item->{PRODUCT_QUANTITY} += $productQuantityValue;
                return $first_found_cart_item->save();
            }

            return Cart::query()->create([
                ...$cartRelations,
                PRODUCT_SIZE     => $product_size_value,
                PRODUCT_QUANTITY => $productQuantityValue,
            ]);
        });
    }
}
