<?php

namespace Tests\Unit\Services;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReviewService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReviewService();
        Category::factory()->create(); // ProductFactory needs at least one category
    }

    public function test_is_verified_purchase_returns_true_for_completed_order(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();
        $order   = Order::factory()->for($user)->completed()->create();

        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'price'      => 500,
        ]);

        $this->assertTrue($this->service->isVerifiedPurchase($user, $product));
    }

    public function test_is_verified_purchase_returns_false_for_pending_order(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();
        // OrderFactory defaults to 'pending' status
        $order   = Order::factory()->for($user)->create();

        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'price'      => 500,
        ]);

        $this->assertFalse($this->service->isVerifiedPurchase($user, $product));
    }

    public function test_is_verified_purchase_returns_false_when_no_order_exists(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();

        $this->assertFalse($this->service->isVerifiedPurchase($user, $product));
    }

    public function test_approve_sets_review_status_to_approved(): void
    {
        $review = Review::factory()->create(['status' => 'pending']);

        $this->service->approve($review);

        $this->assertDatabaseHas('reviews', [
            'id'     => $review->id,
            'status' => 'approved',
        ]);
    }

    public function test_reject_sets_review_status_to_rejected(): void
    {
        $review = Review::factory()->create(['status' => 'pending']);

        $this->service->reject($review);

        $this->assertDatabaseHas('reviews', [
            'id'     => $review->id,
            'status' => 'rejected',
        ]);
    }
}
