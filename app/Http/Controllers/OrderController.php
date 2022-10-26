<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController
{
    /**
     * Search order
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $query = Order::query()->with('user', 'books');

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
        return $order->load('user', 'books');
    }
}
