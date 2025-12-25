<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * Show all products
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        // Add caching
        $cacheKey = 'products_list';

        $products = Cache::remember($cacheKey, 3600, function () {
            // If cache empty, fetch from DB
            return Product::all();
        });

        return ProductResource::collection($products);
    }
}
