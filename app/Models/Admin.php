<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Casts;

#[Table('admins')]
#[Fillable(['username', 'password'])]
#[Hidden(['password', 'remember_token'])]
#[Casts([
    'password' => 'hashed',
])]
class Admin extends Authenticatable
{
    // Authenticated admin user
}
