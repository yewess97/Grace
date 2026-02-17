<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
use Throwable;

class CartController extends Controller
{
    /**
     * Cart Controller Constructor.
     *
     * @param CartService $cartService
     * @return void
     */
    final public function __construct(private readonly CartService $cartService){}

    /**
     * Display the cart resource.
     *
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function index(): Application|Factory|View|JsonResponse
    {
        return request()?->ajax()
            ? ajaxPaginationResponse(userCollectionsData()[CART_MODEL][ITEMS], CART_PAGINATION, USER_CART_ITEMS)
            : showView(USER_CART_VIEW);
    }


    /**
     * Store or Update a cart.
     *
     * @param string $operation
     * @return JsonResponse
     * @throws ModelNotFoundException|ValidationException|CacheInvalidArgumentException|Throwable
     */
    final public function storeOrUpdate(string $operation): JsonResponse
    {
        $this->cartService->createOrUpdateCart($operation);

        $last_page = getLastPage(new Cart(), 5);

        return responseWithData($this->cartService->getCartData([LAST_PAGE => $last_page]));
    }

    /**
     *  Delete a specified cart
     *  or decrement the cart's product quantity.
     *
     * @param Cart $cart
     * @return JsonResponse
     * @throws ModelNotFoundException|CacheInvalidArgumentException|Throwable
     */
    final public function destroy(Cart $cart): JsonResponse
    {
        $cart_deleted = $this->cartService->deleteCart($cart);

        $compact_vars = $this->cartService->getCartData();

        return is_array($cart_deleted)
            ? responseWithData([STATUS => $cart_deleted[0], ...$compact_vars])
            : responseWithData($compact_vars);
    }


    /**
     * Delete all user's carts.
     *
     * @return JsonResponse
     * @throws CacheInvalidArgumentException|Throwable
     */
    final public function destroyMultiple(): JsonResponse
    {
        $this->cartService->deleteAllCarts();

        $row = view(USER_CART_VIEW)->render();

        return responseWithData(array_merge(array_diff_key($this->cartService->getCartData(), [ROW]), compact(ROW)));
    }
}
