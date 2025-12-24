<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreOrderRequest;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * Show order by id.
     */
    public function show(Order $order): JsonResponse
    {
        return response()->json($order->load('items'));
    }

    /**
     * Create new order.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            // Create order
            $order = $this->orderService->createOrder(
                $request->validated()['products']
            );

            return response()->json([
                'message' => 'Order created successfully',
                'order' => new OrderResource($order->load('items'))
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Order failed',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
