<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Handle events after product is modified
     */
    private function clearProductCache(): void
    {
        // Invalidate all product cache
        Cache::put('products_global_timestamp', now()->timestamp);
    }

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->clearProductCache();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->clearProductCache();
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->clearProductCache();
    }
}
