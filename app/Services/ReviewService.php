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
        $this->syncProductRating($review->product_id);
    }

    public function reject(Review $review): void
    {
        $review->update(['status' => 'rejected']);
        $this->syncProductRating($review->product_id);
    }

    private function syncProductRating(int $productId): void
    {
        $avg = Review::where('product_id', $productId)
            ->where('status', 'approved')
            ->avg('rating');

        Product::where('id', $productId)->update([
            'rating' => $avg ? round($avg, 1) : 0,
        ]);
    }
}
