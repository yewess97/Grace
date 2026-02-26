<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Random\RandomException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
use Stripe\Exception\ApiErrorException;
use Throwable;

class OrderController extends Controller
{
    /**
     * Order Controller Constructor.
     *
     * @param OrderService $orderService
     * @return void
     */
    final public function __construct(private readonly OrderService $orderService){}

    /**
     * Get the detailed data of a specified order.
     *
     * @return Application|Factory|View|RedirectResponse
     * @throws Throwable
     */
    final public function orderDetails(): Application|Factory|View|RedirectResponse
    {
        return $this->orderService->getOrderDetailsData();
    }

    /**
     * Store an order.
     *
     * @return Response|RedirectResponse|JsonResponse
     * @throws ValidationException|ApiErrorException|RandomException|Throwable|CacheInvalidArgumentException
     */
    final public function store(): Response|RedirectResponse|JsonResponse
    {
        $create_order = $this->orderService->createOrder();

        return $create_order instanceof RedirectResponse || $create_order instanceof JsonResponse
            ? $create_order
            : responseSuccess();
    }

    /**
     * Get the data of a specified order.
     *
     * @param Order $order
     * @return JsonResponse
     */
    final public function edit(Order $order): JsonResponse
    {
        return responseWithData([ORDER_MODEL => $order->data]);
    }

    /**
     * Update an order.
     *
     * @return Response
     * @throws ValidationException|CacheInvalidArgumentException
     */
    final public function update(): Response
    {
        $this->orderService->updateOrder();

        return responseSuccess();
    }

    /**
     * Delete a specified order.
     *
     * @param Order $order
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function destroy(Order $order): Response
    {
        $order_deleted = $this->orderService->deleteOrder($order);

        return $order_deleted
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.ORDER_MODEL.' you are trying to '.REMOVE.'/'.DELETE.' is not found!');
    }

    /**
     * Delete the selected orders.
     *
     * @param Order $orders
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function destroyMultiple(Order $orders): Response
    {
        $orders_deleted = $this->orderService->deleteMultipleOrders($orders);

        return $orders_deleted
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.ORDERS_TABLE.' (or some of them) you are trying to '.REMOVE.'/'.DELETE.' are not found!');
    }

    /**
     * Restore a specified order.
     *
     * @param Order $order
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function restore(Order $order): Response
    {
        $order_restored = $this->orderService->restoreOrder($order);

        return $order_restored
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.ORDER_MODEL.' you are trying to '.RESTORE.' is not found!');
    }

    /**
     * Restore the selected orders.
     *
     * @param Order $orders
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function restoreMultiple(Order $orders): Response
    {
        $orders_restored = $this->orderService->restoreMultipleOrders($orders);

        return $orders_restored
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.ORDERS_TABLE.' (or some of them) you are trying to '.RESTORE.' are not found!');
    }
}
