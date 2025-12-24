<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Show all products.
     */
    public function index(): JsonResponse
    {
        $products = Product::all();

        return response()->json($products);
    }
}
