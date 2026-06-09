<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
// use Illuminate\Database\Eloquent\Attributes\Casts; // Casts attribute is not available in some versions

#[Table('add_ons')]
#[Fillable(['name', 'description', 'price', 'is_available'])]
class AddOn extends Model
{
    protected $casts = [
        'price' => 'integer',
        'is_available' => 'boolean',
    ];

    // represents menu toppings like Pangsit, Bakso, Ceker, Sayap
}
