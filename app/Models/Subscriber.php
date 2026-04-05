<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = ['email', 'discount_used'];

    protected $casts = [
        'discount_used' => 'boolean',
    ];
}
