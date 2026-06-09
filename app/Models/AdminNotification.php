<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Casts;

#[Table('admin_notifications')]
#[Fillable(['order_id', 'title', 'message', 'type', 'is_read', 'data'])]
#[Casts([
    'is_read' => 'boolean',
    'data' => 'array',
])]
class AdminNotification extends Model
{
    // Explicit declarations for standard Eloquent support
    protected $table = 'admin_notifications';
    protected $fillable = ['order_id', 'title', 'message', 'type', 'is_read', 'data'];
    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
    ];

    /**
     * Get the order associated with the notification.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
