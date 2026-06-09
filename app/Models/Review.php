<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Casts;

#[Table('reviews')]
#[Fillable(['menu_id', 'order_id', 'rating', 'comment'])]
#[Casts([
    'rating' => 'integer',
])]
class Review extends Model
{
    /**
     * Get the menu associated with this review.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the order associated with this review.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
