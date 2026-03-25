<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
use Throwable;

class WishlistService
{
    /**
     * Get the wishlist data.
     *
     * @param array $otherVars
     * @return array
     * @throws Throwable
     */
    final public function getWishlistData(array $otherVars = []): array
    {
        $user_wishlist_items  = userCollectionsData()[WISHLIST_MODEL][ITEMS];
        $wishlist_total_items = userCollectionsData()[WISHLIST_MODEL][TOTAL_ITEMS];

        $row_view = $wishlist_total_items === 0
            ? USER_WISHLIST_VIEW
            : WISHLIST_CONTENT_PARTIAL;

        $row = view($row_view, compact(USER_WISHLIST_ITEMS, WISHLIST_TOTAL_ITEMS))->render();

        $compact_vars = compact(WISHLIST_TOTAL_ITEMS, ROW);

        return [
            ...$compact_vars,
            ...$otherVars,
        ];
    }

    /**
     * Store or Delete a wishlist.
     *
     * @return Wishlist|array
     * @throws ModelNotFoundException|CacheInvalidArgumentException
     */
    final public function createOrDeleteWishlist(): Wishlist|array
    {
        $product = $this->getProductOrFail();

        $wishlist_relations = [
            USER_ID    => auth()->id(),
            PRODUCT_ID => $product->getKey(),
        ];

        $first_found_wishlist_item = Wishlist::query()->firstWhere($wishlist_relations);

        if ($first_found_wishlist_item) {
            $this->deleteWishlist($first_found_wishlist_item);

            $this->forgetWishlistCache();

            return [
                [STATUS => toPastTense(DELETE)],
                ID      => [PRODUCT_ID => $product->getKey()],
            ];
        }

        Wishlist::query()->create($wishlist_relations);

        $this->forgetWishlistCache();

        return [
            [STATUS => toPastTense(CREATE)],
            ID      => [PRODUCT_ID => $product->getKey()],
        ];
    }

    /**
     * Delete a specified wishlist.
     *
     * @param Wishlist $wishlist
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function deleteWishlist(Wishlist $wishlist): bool
    {
        $product_id = Wishlist::query()->whereId($wishlist->getKey())
            ->value(PRODUCT_ID);

        $deleted_wishlist = removeDeleteOrRestore($wishlist);

        $this->forgetWishlistCache($product_id);

        return $deleted_wishlist;
    }

    /**
     * Delete all user's wishlists.
     *
     * @return int
     * @throws CacheInvalidArgumentException
     */
    final public function deleteAllWishlists(): int
    {
        $wishlists = Wishlist::query()->whereHasAuthUser()
            ->get([ID, PRODUCT_ID]);

        if ($wishlists->isEmpty()) {
            return 0;
        }

        $deleted_wishlists = Wishlist::destroy($wishlists->modelKeys());

        $wishlists->pluck(PRODUCT_ID)
            ->unique()
            ->each(fn(int $product_id) => $this->forgetWishlistCache($product_id));

        return $deleted_wishlists;
    }

    /**
     * Get the product or throw an exception in case not found.
     *
     * @param int|null $productId
     * @return Product
     * @throws ModelNotFoundException
     */
    private function getProductOrFail(int $productId = null): Product
    {
        $product_id = request()?->input(ADD.'_'.REMOVE.'_'.WISHLIST_MODEL.'_'.PRODUCT_ID) ?? $productId;

        $product = Product::query()->whereId($product_id)
            ->whereStatus(1)
            ->first([ID, NAME, SLUG, MAIN_IMAGE, NEW_PRICE, STATUS]);

        if (!$product) {
            throw new ModelNotFoundException('The '.PRODUCT_MODEL.' you want is not found!');
        }

        return $product;
    }

    /**
     * Forget the cart cache.
     *
     * @param int|null $productId
     * @return void
     * @throws CacheInvalidArgumentException
     */
    private function forgetWishlistCache(int $productId = null): void
    {
        forgetCache(PRODUCT_MODEL, $this->getProductOrFail($productId), SLUG);
        forgetCache([WISHLISTS_TABLE.'_'.auth()->id(), HOME_PRODUCTS, PRODUCTS_TABLE]);
    }
}
