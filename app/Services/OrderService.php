<?php

namespace App\Services;

use App\Contracts\ServiceData;
use App\Http\Requests\OrderRequest;
use App\Mail\OrderMail;
use App\Models\Cart;
use App\Models\Order;
use App\Notifications\NewAdminActionTaken;
use App\Notifications\NewOrderPlaced;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
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

class OrderService implements ServiceData
{
    private array $create_order_attributes = [STATUS, ADDRESS_ID, PAYMENT_METHOD];

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
     * @throws ValidationException|ApiErrorException|RandomException|Throwable|CacheInvalidArgumentException
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

            $validated_order_request = $this->validateRequest(ADD);

            $order = $this->createOrUpdateCollection($validated_order_request, compact(PAYMENT_STATUS));

            if (is_array($order)) {
                return responseWithData($order);
            }

            $this->createOrderItems($this->getUserCartItems(), $order);

            DB::commit();

            Mail::to(auth()->user()?->{EMAIL})->send(new OrderMail($order));

            $this->forgetCollectionCache($order);

            sendNotificationToAdmins(new NewOrderPlaced($order));

            $destroy_cart_items = Cart::destroy($this->getUserCartItems()->pluck(ID)->toArray());

            forgetCache(CARTS_TABLE.'_'.auth()->id());

            if ((int) Arr::last($validated_order_request->dataValues()) === 1 && $payment_status === 'succeeded') {
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

        $this->forgetCollectionCache($order);

        $update_order = $order->update([STATUS => $status_value]);

        $this->forgetCollectionCache($order);

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

        $this->forgetCollectionCache($order);

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

        $this->forgetCollectionCache($orders);

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

        $this->forgetCollectionCache($order);

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

        $this->forgetCollectionCache($orders);

        return $restored_orders;
    }

    /**
     * Validate and return the order request.
     *
     * @param string $operation
     * @param array $extra
     * @return OrderRequest
     * @throws ValidationException
     */
    final public function validateRequest(string $operation, array $extra = []): OrderRequest
    {
        $order_request = new OrderRequest($operation, ORDER_MODEL, $this->create_order_attributes);

        validateAttributes($order_request);

        return $order_request;
    }

    /**
     * Create or Update the order.
     *
     * @param FormRequest|OrderRequest $collectionRequest
     * @param array $extra
     * @return Order|RedirectResponse
     * @throws ApiErrorException|RandomException|Throwable
     */
    final public function createOrUpdateCollection(FormRequest|OrderRequest $collectionRequest, array $extra): Order|array
    {
        [$status, $address_id, $payment_method] = $this->create_order_attributes;

        [$status_value, $address_id_value, $payment_method_value] = $collectionRequest->dataValues();

        $stripe = new StripeClient(config('services.stripe.secret'));

        if ((is_null($extra[PAYMENT_STATUS]) || !$extra[PAYMENT_STATUS] === 'succeeded') && (int) $payment_method_value === 1) {
            return $this->stripePayment($stripe, $this->getUserCartItems(), $collectionRequest->data());
        }

        return Order::query()->create([
            TRACKING_NUM    => 'GR'.random_int(11111, 99999),
            NUM_ITEMS       => $this->getUserCartItems()->sum(PRODUCT_QUANTITY),
            TOTAL_COST      => userCollectionsData()[CART_MODEL][TOTAL_COST],
            $status         => $status_value,
            $payment_method => $payment_method_value,
            PAYMENT_ID      => request()?->input(PAYMENT_ID),
            USER_ID         => auth()->id(),
            $address_id     => $address_id_value,
        ]);
    }

    /**
     * Forget the order cache.
     *
     * @param Model|Order|null $model
     * @return void
     * @throws CacheInvalidArgumentException
     */
    final public function forgetCollectionCache(Model|Order $model = null): void
    {
        forgetCache(ORDERS_PAGINATION_CACHE_KEY, $model, STATUS);
        forgetCache(USER_ORDERS_PAGINATION_CACHE_KEY);
        forgetCache(ORDER_DETAILS);
    }

    /**
     * Ensure the cart is not empty,
     * and get the user's cart items.
     *
     * @return LazyCollection
     * @throws Throwable
     */
    private function getUserCartItems(): LazyCollection
    {
        $user_cart_items = userCollectionsData()[CART_MODEL][ITEMS];

        if ($user_cart_items->isEmpty()) {
            throw ValidationException::withMessages([
                'You have already placed this '.ORDER_MODEL.'. <br> Please check your '.CART_MODEL.' or add some '.PRODUCTS_TABLE.' to it.',
            ])->status(Response::HTTP_BAD_REQUEST);
        }

        return $user_cart_items;
    }

    /**
     * Process the Stripe payment and redirect to the Stripe checkout page.
     *
     * @param StripeClient $stripe
     * @param LazyCollection $userCartItems
     * @param array $orderData
     * @return array
     * @throws ApiErrorException
     */
    private function stripePayment(StripeClient $stripe, LazyCollection $userCartItems, array $orderData): array
    {
        $line_items = $userCartItems->map(static fn(Cart $cartItem) =>
        [
            'price_data' => [
                'currency'     => 'egp',
                'product_data' => [NAME => $cartItem->{PRODUCT_MODEL}->{NAME}],
                'unit_amount'  => (int) $cartItem->{PRODUCT_MODEL}->{NEW_PRICE} * 100,
            ],
            QUANTITY => (int) $cartItem->{PRODUCT_QUANTITY},
        ])->toArray();

        $place_order_attributes = [
            ...$orderData,
            PAYMENT_STATUS => 'succeeded',
        ];

        $stripe_session = $this->stripeCheckout($stripe, $line_items, $place_order_attributes);

        return [STATUS => 'stripe_session_created', 'redirect_to' => $stripe_session->url];
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
                PRODUCT_ID          => $cart_item->{PRODUCT_MODEL}->{ID},
            ]);
        });
    }
}
