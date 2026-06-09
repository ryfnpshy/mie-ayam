<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Casts;

#[Table('orders')]
#[Fillable([
    'order_number',
    'customer_name',
    'customer_wa',
    'customer_address',
    'distance_km',
    'shipping_cost',
    'total_price',
    'status',
    'payment_proof_status'
])]
#[Casts([
    'distance_km' => 'float',
    'shipping_cost' => 'integer',
    'total_price' => 'integer',
])]
class Order extends Model
{
    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the reviews associated with the order.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
