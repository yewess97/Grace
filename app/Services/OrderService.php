<?php

namespace App\Services;

use App\Http\Requests\OrderRequest;
use App\Mail\OrderMail;
use App\Models\Cart;
use App\Models\Order;
use App\Notifications\NewAdminActionTaken;
use App\Notifications\NewOrderPlaced;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\LazyCollection;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OrderService {
    /**
     * Store an order.
     *
     * @return RedirectResponse|int
     * @throws ValidationException|RandomException|Throwable
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
                throw ValidationException::withMessages([
                    'You have already placed this '.ORDER_MODEL.'. <br> Please check your '.CART_MODEL.' or add some '.PRODUCTS_TABLE.' to it.',
                ])->status(Response::HTTP_BAD_REQUEST);
            }

            $order = Order::query()->create([
                TRACKING_NUM => 'GR'.random_int(11111, 99999),
                NUM_ITEMS    => $user_cart_items->sum(PRODUCT_QUANTITY),
                TOTAL_COST   => $order_total_cost,
                $status      => $status_value,
                USER_ID      => auth()->id(),
                $address_id  => $address_id_value ?? null,
            ]);

            $this->createOrderItems($user_cart_items, $order);

            DB::commit();

            Mail::to(auth()->user()?->{EMAIL})->send(new OrderMail($order));

            sendNotificationToAdmins(new NewOrderPlaced($order));

            return Cart::destroy($user_cart_items->pluck(ID)->toArray());
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

        $order = Order::query()->findOrFail($order_id, [ID, TRACKING_NUM, STATUS]);

        $update_order = $order->update([STATUS => $status_value]);

        sendNotificationToAdmins(new NewAdminActionTaken([$order, $order->{TRACKING_NUM}], UPDATE), true);

        return $update_order;
    }

    /**
     * Delete a specified order.
     *
     * @param Order $order
     * @return bool
     */
    final public function deleteOrder(Order $order): bool
    {
        return customDelete($order, TRACKING_NUM);
    }

    /**
     * Delete the selected orders.
     *
     * @param Order $orders
     * @return bool
     */
    final public function deleteMultipleOrders(Order $orders): bool
    {
        return customDelete($orders);
    }

    /**
     * Restore a specified order.
     *
     * @param Order $order
     * @return bool
     */
    final public function restoreOrder(Order $order): bool
    {
        return restore($order, TRACKING_NUM);
    }

    /**
     * Restore the selected orders.
     *
     * @param Order $orders
     * @return bool
     */
    final public function restoreMultipleOrders(Order $orders): bool
    {
        return restore($orders);
    }

    /**
     * Create an order item for specified cart items.
     *
     * @param LazyCollection $cartItems
     * @param Order $order
     * @return void
     */
    private function createOrderItems(LazyCollection $cartItems, Order $order): void
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
