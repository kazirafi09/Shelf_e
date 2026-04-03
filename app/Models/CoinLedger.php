<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinLedger extends Model
{
    use HasFactory;

    protected $table = 'coin_ledger';

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'description',
        'balance_after',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
