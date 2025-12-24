<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Show all products.
     */
    public function index(): AnonymousResourceCollection
    {
        $products = Product::all();

        return ProductResource::collection($products);
    }
}
