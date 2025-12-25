<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds
     *
     * @return void
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'MacBook Pro',
                'short_description' => 'M3 Chip, 16GB RAM',
                'price' => 1999.99,
                'quantity' => 10,
            ],
            [
                'name' => 'GeForce RTX 5090 GPU',
                'short_description' => 'RTX 5090 Limited Edition',
                'price' => 1599.00,
                'quantity' => 2,
            ],
            [
                'name' => 'Logitech Mouse',
                'short_description' => 'MX Master 4 Wireless',
                'price' => 49.50,
                'quantity' => 50,
            ],
            [
                'name' => 'Dell UltraSharp Monitor',
                'short_description' => '27-inch 4K USB-C Hub',
                'price' => 549.00,
                'quantity' => 25,
            ],
            [
                'name' => 'Keychron K2 Keyboard',
                'short_description' => 'Wireless Mechanical Keyboard',
                'price' => 89.99,
                'quantity' => 30,
            ],
            [
                'name' => 'USB-C Cable (2m)',
                'short_description' => 'Braided Fast Charging Cable',
                'price' => 12.99,
                'quantity' => 200,
            ],
            [
                'name' => 'Screen Cleaning Kit',
                'short_description' => 'Spray and Microfiber Cloth',
                'price' => 9.50,
                'quantity' => 150,
            ],
            [
                'name' => 'Legacy Printer',
                'short_description' => 'Old model - Discontinued',
                'price' => 199.00,
                'quantity' => 0,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['name' => $product['name']], // Check if name exists
                $product
            );
        }
    }
}
