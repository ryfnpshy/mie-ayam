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
     * Determine which storage disk to use.
     * Returns 'supabase' if S3 credentials are configured, 'public' otherwise.
     */
    public static function storageDisk(): string
    {
        $key = config('filesystems.disks.supabase.key');
        $secret = config('filesystems.disks.supabase.secret');
        $bucket = config('filesystems.disks.supabase.bucket');

        if (!empty($key) && !empty($secret) && !empty($bucket)) {
            return 'supabase';
        }

        return 'public';
    }

    /**
     * Get image URL, resolving from the correct disk based on stored path prefix.
     * Paths stored as 'supabase:menus/file.jpg' or 'local:menus/file.jpg'.
     * Legacy paths (no prefix) assume supabase if credentials exist, else local.
     */
    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return asset('images/default-menu.jpg');
        }

        // Full external URL stored directly
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }

        // Prefixed path: 'supabase:path' or 'local:path'
        if (str_starts_with($this->image_path, 'supabase:')) {
            $path = substr($this->image_path, strlen('supabase:'));
            return Storage::disk('supabase')->url($path);
        }

        if (str_starts_with($this->image_path, 'local:')) {
            $path = substr($this->image_path, strlen('local:'));
            return Storage::disk('public')->url($path);
        }

        // Legacy path without prefix: resolve via current active disk
        $disk = self::storageDisk();
        return Storage::disk($disk)->url($this->image_path);
    }
}
