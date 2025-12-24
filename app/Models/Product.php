<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'short_description', 'price', 'quantity'];

    // One product can be in many order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
