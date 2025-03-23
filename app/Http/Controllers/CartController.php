<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            ? ajaxPaginationResponse(cartConfig()[USER_CART_ITEMS], CART_PAGINATION, USER_CART_ITEMS)
            : showView(USER_CART_VIEW);
    }


    /**
     * Store or Update a cart.
     *
     * @param string $operation
     * @return JsonResponse
     * @throws AuthenticationException|ModelNotFoundException|ValidationException|Throwable
     */
    final public function storeOrUpdate(string $operation): JsonResponse
    {
        [$user_cart_items, $last_page] = $this->cartService->createOrUpdateCart($operation);

        $row = view(CART_CONTENT_PARTIAL, compact(USER_CART_ITEMS))->render();
        $total_cost = cartConfig()[TOTAL_COST];

        return responseWithData(compact(USER_CART_ITEMS, ROW, LAST_PAGE, TOTAL_COST));
    }

    /**
     *  Delete a specified cart
     *  or decrement the cart's product quantity.
     *
     * @param Cart $cart
     * @return JsonResponse
     * @throws Throwable
     */
    final public function destroy(Cart $cart): JsonResponse
    {
        $delete_cart     = $this->cartService->deleteCart($cart);
        $user_cart_items = cartConfig()[USER_CART_ITEMS];
        $total_cost      = cartConfig()[TOTAL_COST];
        $row             = view(CART_CONTENT_PARTIAL, compact(USER_CART_ITEMS))->render();

        if (is_array($delete_cart)) {
            return responseSuccess($delete_cart[0], compact(ROW, TOTAL_COST));
        }

        return responseWithData(compact(ROW, TOTAL_COST));
//        return responseSuccess();
    }


    /**
     * Delete all user's carts.
     *
     * @return Response
     */
    final public function destroyMultiple(): Response
    {
        $this->cartService->deleteAllCarts();

        return responseSuccess();
    }
}
