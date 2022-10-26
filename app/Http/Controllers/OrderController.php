<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Services\CreateOrderService;
use App\Services\UpdateOrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    /**
     * Search order
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $query = Order::query()->with(Order::LOAD_WITH);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        if ($request->has('min_date')) {
            $query->byMinDate($request->input('min_date'));
        }

        if ($request->has('max_date')) {
            $query->byMaxDate($request->input('max_date'));
        }

        return $query->paginate($request->input('limit', 50));
    }

    /**
     * Get an order
     *
     * @param Order $order
     * @return Order
     */
    public function getOrder(Order $order)
    {
        return $order->load(Order::LOAD_WITH);
    }

    /**
     * Create an order
     *
     * @param CreateOrderRequest $request
     * @param CreateOrderService $createOrderService
     * @return Order
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createOrder(CreateOrderRequest $request, CreateOrderService $createOrderService)
    {
        return $createOrderService->handle($request);
    }

    /**
     * Update an order
     *
     * @param UpdateOrderRequest $request
     * @param UpdateOrderService $updateOrderService
     * @param Order $order
     * @return Order
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateOrder(UpdateOrderRequest $request, UpdateOrderService $updateOrderService, Order $order)
    {
        return $updateOrderService->handle($request, $order);
    }

    /**
     * Delete an order
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteOrder(Order $order)
    {
        $order->books()->detach();
        $order->delete();

        return response()->json(['message' => 'Order was deleted']);
    }
}
