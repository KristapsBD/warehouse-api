<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
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
    public function store(Request $request): JsonResponse
    {
        // Input validation
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            // Create DB transaction
            $order = DB::transaction(function () use ($validated) {

                $totalAmount = 0;
                $orderItemsData = [];

                // Create order header
                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'total_amount' => 0,
                ]);

                // Process each product
                foreach ($validated['products'] as $item) {
                    // Prevent race conditions
                    $product = Product::where('id', $item['id'])->lockForUpdate()->first();

                    // Validate stock
                    if ($product->quantity < $item['quantity']) {
                        throw new Exception("Product '{$product->name}' does not have enough stock (Requested: {$item['quantity']}, Available: {$product->quantity})");
                    }

                    // Stock update
                    $product->decrement('quantity', $item['quantity']);

                    // Calculate item price
                    $lineTotal = $product->price * $item['quantity'];
                    $totalAmount += $lineTotal;

                    // Prepare data for order items
                    $orderItemsData[] = [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price, // Price at time of purchase
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Insert all items
                OrderItem::insert($orderItemsData);

                // Update order totals
                $order->update(['total_amount' => $totalAmount]);

                return $order;
            });

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load('items')
            ], 201);

        } catch (Exception $e) {
            // DB transaction rollback
            return response()->json([
                'error' => 'Order failed',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
