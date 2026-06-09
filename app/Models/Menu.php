<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Table;

use Illuminate\Support\Facades\Storage;

#[Table('menus')]
class Menu extends Model
{
    protected $fillable = [
        'name', 'description', 'image_path', 'price',
        'is_available', 'is_spicy_variant_enabled', 'stock',
    ];

    protected $casts = [
        'price'                    => 'integer',
        'is_available'             => 'boolean',
        'is_spicy_variant_enabled' => 'boolean',
        'stock'                    => 'integer',
    ];
    /**
     * Get the reviews for the menu.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Calculate average rating.
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Get image url fallback.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            // Check if it's a full URL (external) or a path
            if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
                return $this->image_path;
            }
            
            // If it's a relative path, use Storage
            return Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($this->image_path);
        }
        
        return asset('images/default-menu.jpg');
    }
}
