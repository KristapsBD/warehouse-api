<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the order item resource into array
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'quantity' => (int) $this->quantity,
            'price_at_purchase' => (float) $this->price,
            'total' => (float) ($this->price * $this->quantity),
        ];
    }
}
