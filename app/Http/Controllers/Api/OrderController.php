<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

/**
 * Class OrderController
 *
 * Handles merchant order creation for adding funds to their wallet.
 * Orders created here will have a `pending_payment` status until processed by an admin.
 *
 * @package App\Http\Controllers\Api
 */
class OrderController extends Controller
{
    /**
     * @var OrderService Service layer handling order business logic.
     */
    protected OrderService $orderService;

    /**
     * OrderController constructor.
     *
     * @param OrderService $orderService Service for handling order creation and related logic.
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Create a new order for the authenticated merchant.
     *
     * This method validates the incoming order data, associates it with
     * the authenticated user, and persists it via the OrderService.
     * The created order will typically be in a pending state until reviewed/processed.
     *
     * @param StoreOrderRequest $request Validated order creation request.
     *
     * @return JsonResponse JSON response containing a success message and the created order data.
     *
     * @throws \Throwable If order creation fails at the service or persistence layer.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->create(auth()->user(), $request->validated());

        return response()->json([
            'message' => 'Order created successfully.',
            'order'   => $order,
        ]);
    }
}
