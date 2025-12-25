<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use InvalidArgumentException;
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
     *  Show order by id
     *
     * @param Order $order
     * @return JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        return response()->json($order->load('items'));
    }

    /**
     * Create new order
     *
     * @param StoreOrderRequest $request
     * @return JsonResponse
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
                'order' => new OrderResource($order->load('items'))
            ], 201);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'error' => 'Order failed',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
