<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'description', 'discount_type', 'discount_value',
        'min_order_amount', 'max_uses', 'max_uses_per_user',
        'used_count', 'expires_at', 'is_active', 'is_announced',
    ];

    protected $casts = [
        'expires_at'    => 'datetime',
        'is_active'     => 'boolean',
        'is_announced'  => 'boolean',
    ];

    public function usages()
    {
        return $this->hasMany(VoucherUsage::class);
    }

    /** Calculate the discount amount (in BDT) for a given subtotal. */
    public function calculateDiscount(int|float $subtotal): int
    {
        if ($this->discount_type === 'percentage') {
            return (int) round($subtotal * ($this->discount_value / 100));
        }

        return (int) min($this->discount_value, $subtotal);
    }

    /** Is this voucher currently usable (active, not expired, not over global limit)? */
    public function isUsable(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /** Has a specific user already exhausted their per-user limit for this voucher? */
    public function hasBeenUsedByUser(int $userId): bool
    {
        $timesUsed = VoucherUsage::where('voucher_id', $this->id)
            ->where('user_id', $userId)
            ->count();

        return $timesUsed >= $this->max_uses_per_user;
    }

    /** Scope: only active, non-expired vouchers marked for announcement. */
    public function scopeAnnounced($query)
    {
        return $query->where('is_announced', true)
                     ->where('is_active', true)
                     ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }
}
