<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Only these fields can be populated via request inputs.
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'division',
        'district',
        'postal_code',
        'delivery_method',
        'payment_method',
        'subtotal',
        'shipping_cost',
        'total_amount',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Optional: Add the inverse relationship to User if you need it
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
