<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     *  Show order by id
     *
     * @param  Order  $order
     * @return OrderResource
     */
    public function show(Order $order): OrderResource
    {
        return new OrderResource($order->load('items'));    }

    /**
     * Create new order
     *
     * @param  StoreOrderRequest  $request
     * @return JsonResponse
     *
     * @throws InvalidArgumentException
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
                'order' => new OrderResource($order->load('items')),
            ], 201);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'error' => 'Order failed',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
