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
        $page = request()->get('page', 1);
        $version = Cache::rememberForever('products_global_timestamp', fn() => now()->timestamp);
        $cacheKey = "products_list_v{$version}_page_{$page}";

        $products = Cache::remember($cacheKey, 3600, function () {
            // If cache empty, fetch from DB
            return Product::paginate(100);
        });

        return ProductResource::collection($products);
    }
}
