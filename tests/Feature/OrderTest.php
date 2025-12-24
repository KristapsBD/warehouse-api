<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    // Clean database before test
    use RefreshDatabase;

    public function test_can_create_order_successfully()
    {
        // Create user and login
        Sanctum::actingAs(User::factory()->create());

        // Create product
        $product = Product::factory()->create([
            'price' => 100.00,
            'quantity' => 10
        ]);

        // Buy products
        $payload = [
            'products' => [
                ['id' => $product->id, 'quantity' => 2]
            ]
        ];

        $response = $this->postJson('/api/orders', $payload);

        // Check API response
        $response->assertStatus(201)
                 ->assertJsonPath('message', 'Order created successfully');

        // Check database stock
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 8
        ]);

        // Check order item
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100.00
        ]);
    }

    public function test_cannot_purchase_more_than_available_stock()
    {
        Sanctum::actingAs(User::factory()->create());

        $product = Product::factory()->create(['quantity' => 5]);

        // Buy products more than available stock
        $payload = [
            'products' => [
                ['id' => $product->id, 'quantity' => 10]
            ]
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(400)
                 ->assertJsonFragment(['error' => 'Order failed']);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 5
        ]);
    }

    public function test_validation_fails_if_product_does_not_exist()
    {
        Sanctum::actingAs(User::factory()->create());

        // Buy product that does not exist
        $payload = [
            'products' => [
                ['id' => 999, 'quantity' => 1]
            ]
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['products.0.id']);
    }

    public function test_if_one_item_fails_no_stock_is_deducted()
    {
        Sanctum::actingAs(User::factory()->create());

        // Product A
        $productA = Product::factory()->create(['quantity' => 10, 'price' => 50]);
        // Product B
        $productB = Product::factory()->create(['quantity' => 1, 'price' => 50]);

        $payload = [
            'products' => [
                ['id' => $productA->id, 'quantity' => 1],
                ['id' => $productB->id, 'quantity' => 5],
            ]
        ];

        $this->postJson('/api/orders', $payload);

        // No change to A
        $this->assertDatabaseHas('products', [
            'id' => $productA->id,
            'quantity' => 10
        ]);

        // Make sure no order created
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_creating_order_clears_product_cache()
    {
        Sanctum::actingAs(User::factory()->create());
        $product = Product::factory()->create(['quantity' => 10]);

        Cache::spy();

        $this->postJson('/api/orders', [
            'products' => [['id' => $product->id, 'quantity' => 1]]
        ])->assertCreated();

        // Check if cache was cleared
        Cache::shouldHaveReceived('forget')->once()->with('products_list');
    }

    public function test_validation_fails_if_duplicate_products_are_sent()
    {
        Sanctum::actingAs(User::factory()->create());
        $product = Product::factory()->create();

        $payload = [
            'products' => [
                ['id' => $product->id, 'quantity' => 1],
                ['id' => $product->id, 'quantity' => 2], // Duplicate ID
            ]
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['products.0.id']);
    }

    public function test_guest_cannot_create_order()
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/api/orders', [
            'products' => [['id' => $product->id, 'quantity' => 1]]
        ]);

        // Unauthorized
        $response->assertStatus(401);
    }

    public function test_validation_fails_for_negative_quantity()
    {
        Sanctum::actingAs(User::factory()->create());
        $product = Product::factory()->create();

        $payload = [
            'products' => [
                ['id' => $product->id, 'quantity' => -5]
            ]
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['products.0.quantity']);
    }
}
