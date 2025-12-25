<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Handle events after product is modified
     *
     * @return void
     */
    private function clearProductCache(): void
    {
        // Invalidate all product cache
        Cache::forever('products_global_timestamp', now()->timestamp);
    }

    /**
     * Handle the Product "created" event
     *
     * @param  Product  $product
     * @return void
     */
    public function created(Product $product): void
    {
        $this->clearProductCache();
    }

    /**
     * Handle the Product "updated" event
     *
     * @param  Product  $product
     * @return void
     */
    public function updated(Product $product): void
    {
        $this->clearProductCache();
    }

    /**
     * Handle the Product "deleted" event
     *
     * @param  Product  $product
     * @return void
     */
    public function deleted(Product $product): void
    {
        $this->clearProductCache();
    }
}
