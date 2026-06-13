<?php

namespace App\Http\Controllers;

use App\Models\Address;
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
     * @return Application|Factory|View|RedirectResponse|JsonResponse
     * @throws Throwable
     */
    final public function index(): Application|Factory|View|RedirectResponse|JsonResponse
    {
        $user_cart_items = userCollectionsData()[CART_MODEL][ITEMS];

        if ($user_cart_items->isEmpty()) {
            return to_route(HOME)
                ->with('checkoutError', 'Please add some '.PRODUCTS_TABLE.' to your '.CART_MODEL.' first')
                ->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        // Check if a product is unavailable, then delete it from the cart when proceeding to checkout
        $user_cart_items->each(function (Cart $cart_item) {
            return !Product::query()->whereId($cart_item->{PRODUCT_MODEL}->{ID})
                ->whereStatus(1)
                ->exists()
            && $cart_item->delete();
        });

        $user_addresses_ids = cache()->remember(USER_ADDRESSES_PAGINATION_CACHE_KEY, now()->addMinutes(30), static fn() =>
            auth()->user()?->{ADDRESSES_TABLE}()
                ->pluck(ID)
                ->toArray()
        );

        $user_addresses = paginateWithFallback(Address::class, $user_addresses_ids, 4, [ID, ...ADDRESS_ATTRIBUTES]);

        $add_order_error = static fn(string $attributeName) =>
            formError(ADD, ORDER_MODEL, $attributeName);

        return request()?->ajax()
            ? ajaxPaginationResponse($user_addresses, CHECKOUT_USER_ADDRESSES_PAGINATION, USER_ADDRESSES)
            : view(USER_CHECKOUT_VIEW, compact(USER_CART_ITEMS, USER_ADDRESSES, ADD_ORDER_ERROR));
    }
}
