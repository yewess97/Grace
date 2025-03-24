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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class CartController extends Controller
{
    private LengthAwarePaginator $user_cart_items;
    private int $total_cost;
    private int $total_items;
    private string $row;
    private string $header_row;

    /**
     * Cart Controller Constructor.
     *
     * @param CartService $cartService
     * @return void
     * @throws Throwable
     */
    final public function __construct(private readonly CartService $cartService)
    {
        $this->user_cart_items = cartConfig()[USER_CART_ITEMS];
        $this->total_cost      = cartConfig()[TOTAL_COST];
        $this->total_items     = cartConfig()[TOTAL_ITEMS];

        $this->row = view(CART_CONTENT_PARTIAL, [
            USER_CART_ITEMS => $this->user_cart_items,
            TOTAL_ITEMS     => $this->total_items
        ])->render();

        $this->header_row = view(CART_HEADER_CONTENT_PARTIAL, [
            USER_CART_ITEMS => $this->user_cart_items,
            TOTAL_COST      => $this->total_cost,
            TOTAL_ITEMS     => $this->total_items
        ])->render();
    }

    /**
     * Display the cart resource.
     *
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function index(): Application|Factory|View|JsonResponse
    {
        return request()?->ajax()
            ? ajaxPaginationResponse($this->user_cart_items, CART_PAGINATION, USER_CART_ITEMS)
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
        $this->cartService->createOrUpdateCart($operation);

        $total_cost  = $this->total_cost;
        $total_items = $this->total_items;
        $header_row  = $this->header_row;
        $row         = $this->row;
        $last_page   = getLastPage(new Cart(), 5);

        return responseWithData(compact(TOTAL_COST, TOTAL_ITEMS, HEADER_ROW, ROW, LAST_PAGE));
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

        $total_cost      = $this->total_cost;
        $total_items     = $this->total_items;
        $header_row      = $this->header_row;
        $row             = $this->row;

        $compact_vars    = compact(TOTAL_COST, TOTAL_ITEMS, HEADER_ROW, ROW);

        return is_array($delete_cart)
            ? responseSuccess($delete_cart[0], $compact_vars)
            : responseWithData($compact_vars);
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
