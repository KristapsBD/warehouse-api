<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'MacBook Pro',
                'short_description' => 'M3 Chip, 16GB RAM',
                'price' => 1999.99,
                'quantity' => 10
            ],
            [
                'name' => 'Logitech Mouse',
                'short_description' => 'MX Master 4 Wireless',
                'price' => 49.50,
                'quantity' => 50
            ],
            [
                'name' => 'GeForce RTX 5090 GPU',
                'short_description' => 'RTX 5090 Limited Edition',
                'price' => 1599.00,
                'quantity' => 2
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
