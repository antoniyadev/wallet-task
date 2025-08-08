<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class OrderController
 *
 * Handles admin operations for managing orders.
 * Allows listing orders, retrieving available statuses, and updating order status.
 *
 * @package App\Http\Controllers\Api\Admin
 */
class OrderController extends Controller
{
    /**
     * Service layer for order-related business logic.
     *
     * @var OrderService
     */
    protected OrderService $orderService;

    /**
     * OrderController constructor.
     *
     * @param OrderService $orderService The service instance handling order operations.
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a paginated list of orders for admin users.
     *
     * Authorizes the current user to view any order.
     * Includes the related user for each order.
     *
     * @return JsonResponse Paginated list of orders with user relation.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::with('user')->latest()->paginate(10);

        return response()->json($orders);
    }

    /**
     * Retrieve available order status options.
     *
     * @return JsonResponse Array of status keys, labels, and optional colors.
     */
    public function statuses(): JsonResponse
    {
        return response()->json($this->orderService->getStatusOptions());
    }

    /**
     * Update the status of a specific order.
     *
     * @param Request $request The incoming HTTP request containing the new status.
     * @param Order   $order   The order instance to update (resolved via route model binding).
     *
     * @return JsonResponse Success message after updating the order status.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable When the update fails in the service layer.
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $this->orderService->updateStatus($order, $request->input('status'));

        return response()->json(['message' => 'Order status updated']);
    }
}
