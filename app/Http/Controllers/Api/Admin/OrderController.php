<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::with('user')->latest()->get();

        return response()->json($orders);
    }

    public function statuses(): JsonResponse
    {
        return response()->json($this->orderService->getStatusOptions());
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->orderService->updateStatus($order, $request->input('status'));

        return response()->json(['message' => 'Order status updated']);
    }
}
