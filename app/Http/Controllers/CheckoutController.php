<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CheckoutController extends Controller
{
    /**
     * Display the checkout resource.
     *
     * @return RedirectResponse|JsonResponse|Application|Factory|View
     * @throws Throwable
     */
    final public function index(): RedirectResponse|JsonResponse|Application|Factory|View
    {
        $user_cart_items = Cart::query()->where(USER_ID, auth()->id())->lazy();

        if ($user_cart_items->collect()->isEmpty()) {
            return to_route('home')->with('checkoutError', 'Please add some '.PRODUCTS_TABLE.' to your '.CART_MODEL.' first')->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $user_cart_items->each(static function (Cart $cart_item) {
            $product_available = Product::query()->whereId($cart_item->{PRODUCT_MODEL}->{ID})
                ->whereStatus(1)
                ->exists();

            if (!$product_available) {
                $cart_item->delete();
            }
        });

        $total_cost      = cartConfig()[TOTAL_COST];
        $user_addresses  = auth()->user()?->{ADDRESSES_TABLE}()->fastPaginate(4, [ID, ...ADDRESS_ATTRIBUTES]);

        $add_order_error = static fn(string $attributeName) => formError(ADD, ORDER_MODEL, $attributeName);

        return request()?->ajax()
            ? ajaxPaginationResponse($user_addresses, CHECKOUT_USER_ADDRESSES_PAGINATION, USER_ADDRESSES)
            : view(USER_CHECKOUT_VIEW, compact(USER_CART_ITEMS, TOTAL_COST, USER_ADDRESSES, ADD_ORDER_ERROR));
    }
}
