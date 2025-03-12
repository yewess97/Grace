<?php

namespace App\Services;

use App\Http\Requests\OrderRequest;
use App\Mail\OrderMail;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OrderService {
    /**
     * Store an order.
     *
     * @return RedirectResponse|int
     * @throws ValidationException|Throwable|RandomException
     */
    final public function createOrder(): RedirectResponse|int
    {
        DB::beginTransaction();

        try {
            $create_order_attributes = [STATUS, ADDRESS_ID];

            $order_request = new OrderRequest(ADD, ORDER_MODEL, $create_order_attributes);

            validateAttributes($order_request);

            [$status, $address_id] = $create_order_attributes;

            [$status_value, $address_id_value] = $order_request->dataValues();

            $user_cart_items  = cartConfig()[USER_CART_ITEMS];
            $order_total_cost = cartConfig()[TOTAL_COST];

            if ($user_cart_items->isEmpty()) {
                return to_route('home')->with('checkoutError', 'Please add some '.PRODUCTS_TABLE.' to your '.CART_MODEL.' first')->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            $user_order = Order::query()->create([
                TRACKING_NUM => 'GR'.random_int(11111, 99999),
                NUM_ITEMS    => $user_cart_items->sum(PRODUCT_QUANTITY),
                TOTAL_COST   => $order_total_cost,
                $status      => $status_value,
                USER_ID      => auth()->id(),
                $address_id  => $address_id_value ?? null,
            ]);

            $this->createOrderItems($user_cart_items, $user_order);

            DB::commit();

            Mail::to(auth()->user()->{EMAIL})->send(new OrderMail($user_order));

            return Cart::destroy($user_cart_items);
        }
        catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    /**
     * Get the detailed data of a specified order.
     *
     * @return RedirectResponse|Application|Factory|View
     * @throws Throwable
     */
    final public function getOrderDetailsData(): RedirectResponse|Application|Factory|View
    {
        $order = Order::query()->with([
                ORDER_ITEMS => static fn(HasMany $orderItem) => $orderItem->select(ORDER_ITEM_FILLABLE_ATTRIBUTES),
            ])
            ->firstWhere(TRACKING_NUM, request()?->input(TRACKING_NUM));

        if (is_null($order)) {
            return back();
        }

        return showView(ORDER_DETAILS_COMPONENT, getOrderDetails($order));
    }

    /**
     * Update an order.
     *
     * @return int
     * @throws ValidationException
     */
    final public function updateOrder(): int
    {
        $order_request = new OrderRequest(UPDATE, ORDER_MODEL, [STATUS]);

        $order_id = request()?->input(UPDATE_ORDER_ID);

        validateAttributes($order_request);

        [$status_value] = $order_request->dataValues();

        return Order::query()->whereId($order_id)->update([STATUS => $status_value]);
    }

    /**
     * Delete a specified order.
     *
     * @param Order $order
     * @return bool
     */
    final public function deleteOrder(Order $order): bool
    {
        return delete($order);
    }

    /**
     * Delete the selected orders.
     *
     * @param Order $orders
     * @return bool
     */
    final public function deleteMultipleOrders(Order $orders): bool
    {
        return delete($orders, true);
    }

    /**
     * Restore a specified order.
     *
     * @param Order $order
     * @return bool
     */
    final public function restoreOrder(Order $order): bool
    {
        return restore($order);
    }

    /**
     * Restore the selected orders.
     *
     * @param Order $orders
     * @return bool
     */
    final public function restoreMultipleOrders(Order $orders): bool
    {
        return restore($orders, true);
    }

    /**
     * Create an order item for specified cart items.
     *
     * @param Collection $cartItems
     * @param Order $order
     * @return void
     */
    private function createOrderItems(Collection $cartItems, Order $order): void
    {
        $cartItems->each(static function ($cart_item) use (&$order) {
            $product_total_price = $cart_item->{PRODUCT_QUANTITY} * $cart_item->{PRODUCT_MODEL}->{NEW_PRICE};

            $order->{ORDER_ITEMS}()->create([
                PRODUCT_NAME        => $cart_item->{PRODUCT_MODEL}->{NAME},
                PRODUCT_MAIN_IMAGE  => $cart_item->{PRODUCT_MODEL}->{MAIN_IMAGE},
                PRODUCT_SIZE        => $cart_item->{PRODUCT_SIZE},
                PRODUCT_QUANTITY    => $cart_item->{PRODUCT_QUANTITY},
                PRODUCT_TOTAL_PRICE => $product_total_price,
                ORDER_ID            => $order->getKey(),
            ]);
        });
    }
}
