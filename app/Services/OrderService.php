<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderService
{
    /**
     * Create new order
     *
     * @param  array  $items
     * @return Order
     *
     * @throws InvalidArgumentException
     */
    public function createOrder(array $items): Order
    {
        // Create DB transaction
        return DB::transaction(function () use ($items) {

            // Create order header
            $order = Order::create([
                'total_amount' => 0,
            ]);

            $orderNumber = 'ORD-'.($order->id * 3) + 10000;
            $order->order_number = $orderNumber;
            $order->save();

            $totalAmount = 0;
            $orderItemsData = [];

            // Process each product
            foreach ($items as $item) {
                // Prevent race conditions
                $product = Product::where('id', $item['id'])->lockForUpdate()->first();

                // Validate stock
                if ($product->quantity < $item['quantity']) {
                    throw new InvalidArgumentException("Product '{$product->name}' does not have enough stock (Requested: {$item['quantity']}, Available: {$product->quantity})");
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

            // Clear cache
            Cache::forget('products_list');

            return $order;
        });
    }
}
