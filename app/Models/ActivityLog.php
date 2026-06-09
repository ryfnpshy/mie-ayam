<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Casts;

#[Table('activity_logs')]
#[Fillable(['activity_type', 'description', 'payload'])]
#[Casts([
    'payload' => 'array',
])]
class ActivityLog extends Model
{
    // Explicit declarations for standard Eloquent support
    protected $table = 'activity_logs';
    protected $fillable = ['activity_type', 'description', 'payload'];
    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * Create an audit log entry.
     */
    public static function log(string $type, string $description, ?array $payload = null): self
    {
        return self::create([
            'activity_type' => $type,
            'description' => $description,
            'payload' => $payload,
        ]);
    }
}
