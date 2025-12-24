<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Show all products.
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
