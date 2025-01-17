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
     * @return Application|Factory|View
     * @throws Throwable
     */
    final public function index(): Application|Factory|View
    {
        return showView(USER_CART_VIEW);
    }


    /**
     * Store or Update a cart.
     *
     * @param string $operation
     * @return Response
     * @throws AuthenticationException|ModelNotFoundException|ValidationException
     */
    final public function storeOrUpdate(string $operation): Response
    {
        $this->cartService->createOrUpdateCart($operation);

        return responseSuccess();
    }

    /**
     *  Delete a specified cart
     *  or decrement the cart's product quantity.
     *
     * @param Cart $cart
     * @return Response|JsonResponse
     */
    final public function destroy(Cart $cart): Response|JsonResponse
    {
        $delete_cart = $this->cartService->deleteCart($cart);

        if ($delete_cart instanceof JsonResponse) {
            return $delete_cart;
        }

        return responseSuccess();
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
