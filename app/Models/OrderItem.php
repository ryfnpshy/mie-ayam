<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Casts;

#[Table('order_items')]
#[Fillable(['order_id', 'menu_id', 'quantity', 'spiciness_level', 'notes'])]
#[Casts([
    'quantity' => 'integer',
    'spiciness_level' => 'integer',
])]
class OrderItem extends Model
{
    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the menu associated with the item.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the toppings (add-ons) selected for this item.
     */
    public function addOns(): BelongsToMany
    {
        return $this->belongsToMany(AddOn::class, 'order_item_add_ons', 'order_item_id', 'addon_id');
    }
}
