<?php

namespace App\Services;

use App\Http\Requests\CartRequest;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
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
     * @return Cart|int|bool
     * @throws AuthenticationException|ModelNotFoundException|ValidationException
     */
    final public function createOrUpdateCart(string $operation): Cart|int|bool
    {
        if (!auth()->check()) {
            throw new AuthenticationException('unauthenticated');
        }

        $cart_attributes = [PRODUCT_ID];

        $cart_attributes = $this->mergeIfRequestHas($this->getActionNames($operation), $cart_attributes, [PRODUCT_SIZE, PRODUCT_QUANTITY]);

        $cart_attributes = $this->mergeIfRequestHas($this->getActionNames($operation, true), $cart_attributes, [PRODUCT_SIZE_QUICK_VIEW, PRODUCT_QUANTITY_QUICK_VIEW]);

        $cart_request = new CartRequest($operation, CART_MODEL, $cart_attributes);

        $product_id = $cart_attributes[0];

        if ($operation === UPDATE) {
            return $this->updateCartItems($cart_request, $product_id);
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

        return $this->createOrUpdateCartItem($cart_attributes, $cart_relations, $product_sizes_values, $product_quantity_value);
    }

    /**
     * Delete a specified cart
     * or decrement the cart's product quantity if it's greater than 1.
     *
     * @param Cart $cart
     * @return JsonResponse|bool
     * @throws ModelNotFoundException
     */
    final public function deleteCart(Cart $cart): JsonResponse|bool
    {
        $cart_item = Cart::query()->firstWhere(ID, $cart->getKey());

        if (is_null($cart_item)) {
            throw new ModelNotFoundException("This ".CART_MODEL."is not found!");
        }

        if ($cart_item->{PRODUCT_QUANTITY} > 1) {
            $cart_item->decrement(PRODUCT_QUANTITY);

            return responseSuccess('decremented');
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
     * @return bool
     * @throws ModelNotFoundException
     */
    private function updateCartItems(CartRequest $cartRequest, string $productId): bool
    {
        [$products_ids_values, $products_sizes_values, $products_quantities_values] = $cartRequest->dataValues();

        $products_ids_values        = array_filter((array) $products_ids_values);
        $products_quantities_values = array_filter((array) $products_quantities_values);

        $products_ids = $this->getProductOrFail($products_ids_values, true);

        $cart_items = Cart::query()->where(USER_ID, auth()->id())
            ->whereIn($productId, $products_ids)
            ->get([ID, PRODUCT_QUANTITY]);

        if ($cart_items->count() !== count($products_quantities_values)) {
            throw new ModelNotFoundException('The number of the '.PRODUCTS_TABLE.' in the '.CART_MODEL.' is not equal to the number of the received quantities!');
        }

        $cart_items->each(static function (Model $cartItem, int $key) use ($products_quantities_values) {
            $cartItem->{PRODUCT_QUANTITY} = $products_quantities_values[$key];
            $cartItem->save();
        });

        return true;
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
        return empty(array_intersect([PRODUCT_SIZE, PRODUCT_QUANTITY], $cartAttributes))
            && empty(array_intersect([PRODUCT_SIZE_QUICK_VIEW, PRODUCT_QUANTITY_QUICK_VIEW], $cartAttributes));
    }

    /**
     * Update the product quantity of the cart item,
     * or create a new one if it doesn't exist.
     *
     * @param array $cartAttributes
     * @param array $cartRelations
     * @param array $productSizesValues
     * @param int $productQuantityValue
     * @return Cart|bool
     */
    private function createOrUpdateCartItem(array $cartAttributes, array &$cartRelations, array &$productSizesValues, int &$productQuantityValue): Cart|bool
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
