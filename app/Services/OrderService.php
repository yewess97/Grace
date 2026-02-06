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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\LazyCollection;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Response;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
use Throwable;
use Exception;

class OrderService {
    /**
     * Get the detailed data of a specified order.
     *
     * @return RedirectResponse|Application|Factory|View
     * @throws Throwable
     */
    final public function getOrderDetailsData(): RedirectResponse|Application|Factory|View
    {
        $order = cache()->remember(ORDER_DETAILS, 3600, fn():
            Order => Order::query()->with(
                ORDER_ITEMS, static fn(HasMany $orderItem) => $orderItem->select(ORDER_ITEM_FILLABLE_ATTRIBUTES)
            )
            ->firstWhere(TRACKING_NUM, request()?->input(TRACKING_NUM))
        );

        return is_null($order)
            ? back()
            : showView(ORDER_DETAILS_COMPONENT, getOrderDetails($order));
    }

    /**
     * Store an order.
     *
     * @return RedirectResponse|JsonResponse|int
     * @throws ValidationException|RandomException|CacheInvalidArgumentException|Throwable|Exception
     */
    final public function createOrder(): RedirectResponse|JsonResponse|int
    {
        DB::beginTransaction();

        try {
            $payment_status = request()?->input(PAYMENT_STATUS);

            if (isset($payment_status) && $payment_status === 'canceled') {
                return to_route(CHECKOUT)
                    ->with('paymentFailed', 'Payment failed. Please try again!')
                    ->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            $create_order_attributes = [STATUS, ADDRESS_ID, PAYMENT_METHOD];

            $order_request = new OrderRequest(ADD, ORDER_MODEL, $create_order_attributes);

            validateAttributes($order_request);

            [$status, $address_id, $payment_method] = $create_order_attributes;

            [$status_value, $address_id_value, $payment_method_value] = $order_request->dataValues();

            $user_cart_items  = userCollectionsData()[CART_MODEL][ITEMS];
            $order_total_cost = userCollectionsData()[CART_MODEL][TOTAL_COST];

            if ($user_cart_items->isEmpty()) {
                throw ValidationException::withMessages([
                    'You have already placed this '.ORDER_MODEL.'. <br> Please check your '.CART_MODEL.' or add some '.PRODUCTS_TABLE.' to it.',
                ])->status(Response::HTTP_BAD_REQUEST);
            }

            $stripe = new StripeClient(config('services.stripe.secret'));

            if ((is_null($payment_status) || !$payment_status === 'succeeded') && (int) $payment_method_value === 1) {
                return $this->stripePayment($stripe, $user_cart_items, $order_request->data());
            }

            $order = Order::query()->create([
                TRACKING_NUM    => 'GR'.random_int(11111, 99999),
                NUM_ITEMS       => $user_cart_items->sum(PRODUCT_QUANTITY),
                TOTAL_COST      => $order_total_cost,
                $status         => $status_value,
                $payment_method => $payment_method_value,
                PAYMENT_ID      => request()?->input(PAYMENT_ID),
                USER_ID         => auth()->id(),
                $address_id     => $address_id_value,
            ]);

            $this->createOrderItems($user_cart_items, $order);

            DB::commit();

            Mail::to(auth()->user()?->{EMAIL})->send(new OrderMail($order));

            $this->forgetOrderCache($order);

            sendNotificationToAdmins(new NewOrderPlaced($order));

            $destroy_cart_items = Cart::destroy($user_cart_items->pluck(ID)->toArray());

            forgetCache(CARTS_TABLE);

            if ((int) $payment_method_value === 1 && $payment_status === 'succeeded') {
                return to_route(ORDER_DETAILS, [TRACKING_NUM => $order->{TRACKING_NUM}])
                    ->with('orderPlaced', 'Your '.ORDER_MODEL.' has been placed successfully!');
            }

            return $destroy_cart_items;
        }
        catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    /**
     * Update an order.
     *
     * @return int
     * @throws ValidationException|CacheInvalidArgumentException
     */
    final public function updateOrder(): int
    {
        $order_request = new OrderRequest(UPDATE, ORDER_MODEL, [STATUS]);

        $order_id = request()?->input(UPDATE_ORDER_ID);

        validateAttributes($order_request);

        [$status_value] = $order_request->dataValues();

        $order = Order::query()->findOrFail($order_id, [ID, TRACKING_NUM, STATUS]);

        $this->forgetOrderCache($order);

        $update_order = $order->update([STATUS => $status_value]);

        $this->forgetOrderCache($order);

        sendNotificationToAdmins(new NewAdminActionTaken([$order, $order->{TRACKING_NUM}], UPDATE), true);

        return $update_order;
    }

    /**
     * Delete a specified order.
     *
     * @param Order $order
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function deleteOrder(Order $order): bool
    {
        $deleted_order = removeDeleteOrRestore($order, $order->{TRACKING_NUM});

        $this->forgetOrderCache($order);

        return $deleted_order;
    }

    /**
     * Delete the selected orders.
     *
     * @param Order $orders
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function deleteMultipleOrders(Order $orders): bool
    {
        $deleted_orders = removeDeleteOrRestore($orders);

        $this->forgetOrderCache($orders);

        return $deleted_orders;
    }

    /**
     * Restore a specified order.
     *
     * @param Order $order
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function restoreOrder(Order $order): bool
    {
        $restored_order = removeDeleteOrRestore($order, $order->{TRACKING_NUM});

        $this->forgetOrderCache($order);

        return $restored_order;
    }

    /**
     * Restore the selected orders.
     *
     * @param Order $orders
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function restoreMultipleOrders(Order $orders): bool
    {
        $restored_orders = removeDeleteOrRestore($orders);

        $this->forgetOrderCache($orders);

        return $restored_orders;
    }

    /**
     * Process the Stripe payment and redirect to the Stripe checkout page.
     *
     * @param StripeClient $stripe
     * @param LazyCollection $userCartItems
     * @param array $orderData
     * @return JsonResponse
     * @throws ApiErrorException
     */
    private function stripePayment(StripeClient $stripe, LazyCollection $userCartItems, array $orderData): JsonResponse
    {
        $line_items = $userCartItems->map(static fn(Cart $cart_item) =>
        [
            'price_data' => [
                'currency'     => 'egp',
                'product_data' => [NAME => $cart_item->{PRODUCT_MODEL}->{NAME}],
                'unit_amount'  => (int) $cart_item->{PRODUCT_MODEL}->{NEW_PRICE} * 100,
            ],
            QUANTITY => (int) $cart_item->{PRODUCT_QUANTITY},
        ])->toArray();

        $place_order_attributes = [
            ...$orderData,
            PAYMENT_STATUS => 'succeeded',
        ];

        $stripe_session = $this->stripeCheckout($stripe, $line_items, $place_order_attributes);

        return responseWithData([STATUS => 'stripe_session_created', 'redirect_to' => $stripe_session->url]);
    }

    /**
     * Process the Stripe checkout session.
     *
     * @param StripeClient $stripe
     * @param array[] $lineItems
     * @param array|null $otherArgs
     * @return Session
     * @throws ApiErrorException
     */
    private function stripeCheckout(StripeClient $stripe, array $lineItems, ?array $otherArgs = null): Session
    {
        return $stripe->checkout->sessions->create([
            'payment_method_types' => ['card', 'link'],
            'line_items'           => $lineItems,
            'mode'                 => PAYMENT,
            'success_url'          => route(CREATE_ORDER, $otherArgs).'&payment_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => route(CREATE_ORDER, [PAYMENT_STATUS => 'canceled']),
        ]);
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

    /**
     * Forget the order cache.
     *
     * @param Order $order
     * @return void
     * @throws CacheInvalidArgumentException
     */
    private function forgetOrderCache(Order $order): void
    {
        forgetCache(ORDERS_PAGINATION_CACHE_KEY, $order, STATUS);
        forgetCache(USER_ORDERS_PAGINATION_CACHE_KEY);
        forgetCache(ORDER_DETAILS);
    }
}
