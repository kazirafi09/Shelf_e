<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinShippingReward extends Model
{
    protected $fillable = [
        'user_id',
        'coins_spent',
        'shipping_discount',
        'used',
        'order_id',
    ];

    protected $casts = [
        'used' => 'boolean',
    ];

    // The four redeemable shipping discount tiers
    public const TIERS = [
        ['coins' => 100, 'discount' => 10],
        ['coins' => 200, 'discount' => 20],
        ['coins' => 300, 'discount' => 30],
        ['coins' => 400, 'discount' => 40],
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
