<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;

class ReviewService
{
    public function isVerifiedPurchase(User $user, Product $product): bool
    {
        return Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->exists();
    }

    public function approve(Review $review): void
    {
        $review->update(['status' => 'approved']);
    }

    public function reject(Review $review): void
    {
        $review->update(['status' => 'rejected']);
    }
}
