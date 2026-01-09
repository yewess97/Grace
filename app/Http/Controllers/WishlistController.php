<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Http\Controllers\Controller;
use App\Services\WishlistService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;

class WishlistController extends Controller
{
    /**
     * Wishlist Controller Constructor.
     *
     * @param WishlistService $wishlistService
     * @return void
     * @throws Throwable
     */
    final public function __construct(private readonly WishlistService $wishlistService){}

    /**
     * Display the wishlist resource.
     *
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function index(): Application|Factory|View|JsonResponse
    {
        return request()?->ajax()
            ? ajaxPaginationResponse(cartConfig()[USER_CART_ITEMS], CART_PAGINATION, USER_CART_ITEMS)
            : showView(USER_CART_VIEW);
    }


    /**
     * Store a wishlist.
     *
     * @param string $operation
     * @return JsonResponse
     * @throws AuthenticationException|ModelNotFoundException|ValidationException|CacheInvalidArgumentException|Throwable
     */
    final public function store(): JsonResponse
    {
        $this->wishlistService->createOrUpdateCart();

        $last_page = getLastPage(new Cart(), 5);

        return responseWithData($this->wishlistService->getCartData([LAST_PAGE => $last_page]));
    }

    /**
     *  Delete a specified wishlist
     *  or decrement the wishlist's product quantity.
     *
     * @param Wishlist $wishlist
     * @return JsonResponse
     * @throws ModelNotFoundException|CacheInvalidArgumentException|Throwable
     */
    final public function destroy(Wishlist $wishlist): JsonResponse
    {
        $delete_cart = $this->wishlistService->deleteCart($wishlist);

        $compact_vars = $this->wishlistService->getCartData();

        return is_array($delete_cart)
            ? responseWithData([STATUS => $delete_cart[0], ...$compact_vars])
            : responseWithData($compact_vars);
    }


    /**
     * Delete all user's wishlists.
     *
     * @return JsonResponse
     * @throws CacheInvalidArgumentException|Throwable
     */
    final public function destroyMultiple(): JsonResponse
    {
        $this->wishlistService->deleteAllCarts();

        $row = view(USER_CART_VIEW)->render();

        return responseWithData(array_merge(array_diff_key($this->wishlistService->getCartData(), [ROW]), compact(ROW)));
    }
}
