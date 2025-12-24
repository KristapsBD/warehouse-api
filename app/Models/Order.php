<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['order_number', 'total_amount'];
    // When fetching order, also fetch items
    protected $with = ['items'];

    // Order has many items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
