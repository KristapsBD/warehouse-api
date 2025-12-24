<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    /**
     * Create new order
     *
     * @param array $items
     * @return Order
     * @throws Exception
     */
    public function createOrder(array $items): Order
    {
         // Create DB transaction
        return DB::transaction(function () use ($items) {

            $totalAmount = 0;
            $orderItemsData = [];

            // Create order header
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'total_amount' => 0,
            ]);

            // Process each product
            foreach ($items as $item) {
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
    }
}
