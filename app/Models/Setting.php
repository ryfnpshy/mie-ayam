<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('settings')]
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set/update a setting value by key.
     */
    public static function set(string $key, mixed $value): self
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Update the menu version timestamp to trigger client-side auto-updates.
     */
    public static function touchMenuVersion(): void
    {
        self::set('menu_last_updated', now()->timestamp);
    }
}
