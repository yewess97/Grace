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
     * @return RedirectResponse|Application|Factory|View
     * @throws Throwable
     */
    final public function orderDetails(): RedirectResponse|Application|Factory|View
    {
        return $this->orderService->getOrderDetailsData();
    }

    /**
     * Store an order.
     *
     * @return Response|RedirectResponse
     * @throws ValidationException|Throwable
     */
    final public function store(): Response|RedirectResponse
    {
        $create_order = $this->orderService->createOrder();

        if ($create_order instanceof RedirectResponse) {
            return $create_order;
        }

        return responseSuccess();
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
     * @throws ValidationException
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
     */
    final public function destroy(Order $order): Response
    {
        $this->orderService->deleteOrder($order);

        return responseSuccess();
    }

    /**
     * Delete the selected orders.
     *
     * @param Order $orders
     * @return Response
     */
    final public function destroyMultiple(Order $orders): Response
    {
        $this->orderService->deleteMultipleOrders($orders);

        return responseSuccess();
    }

    /**
     * Restore a specified order.
     *
     * @param Order $order
     * @return Response
     */
    final public function restore(Order $order): Response
    {
        $this->orderService->restoreOrder($order);

        return responseSuccess();
    }

    /**
     * Restore the selected orders.
     *
     * @param Order $orders
     * @return Response
     */
    final public function restoreMultiple(Order $orders): Response
    {
        $this->orderService->restoreMultipleOrders($orders);

        return responseSuccess();
    }
}
