<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class OrderTest extends TestCase
{
    // Clean database before test
    use RefreshDatabase;

    public function test_can_create_order_successfully()
    {
        // Create user and login
        $user = User::factory()->create();
        Sanctum::actingAs($user);

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
        // Create user and login
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Create product
        $product = Product::factory()->create(['quantity' => 5]);

        // Buy products more than available stock
        $payload = [
            'products' => [
                ['id' => $product->id, 'quantity' => 10]
            ]
        ];

        $response = $this->postJson('/api/orders', $payload);

        // Check API response
        $response->assertStatus(400)
                 ->assertJsonFragment(['error' => 'Order failed']);

        // Check database stock
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 5
        ]);
    }

    public function test_validation_fails_if_product_does_not_exist()
    {
        // Create user and login
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Buy product that does not exist
        $payload = [
            'products' => [
                ['id' => 999, 'quantity' => 1]
            ]
        ];

        $response = $this->postJson('/api/orders', $payload);

        // Check API response
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['products.0.id']);
    }
}
