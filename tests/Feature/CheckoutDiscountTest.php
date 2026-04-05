<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for the FIRST15 one-time newsletter discount coupon at checkout.
 *
 * Business rules under test:
 *  - 15 % discount is applied to the subtotal only (shipping is excluded).
 *  - The coupon is bound to the authenticated user's account email, not the
 *    billing-address field (prevents guest spoofing).
 *  - The subscriber record is marked discount_used = true inside the same
 *    transaction that creates the order, so a rollback keeps it reusable.
 *  - A used coupon is rejected on a second attempt.
 *  - Invalid codes and guest users receive a validation error.
 *
 * NOTE: Coin-redemption scenarios are covered separately in
 *       tests\Feature\CheckoutCoinRedemptionTest.
 */
class CheckoutDiscountTest extends TestCase
{
    use RefreshDatabase;

    private array $basePayload = [
        'name'     => 'Test Customer',
        'email'    => 'customer@example.com',
        'phone'    => '1712345678',
        'address'  => '123 Test Street',
        'division' => 'Dhaka',
        'district' => 'Dhaka City',
        'payment'  => 'cod',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Category::factory()->create();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────────────────────────────────

    private function cartWith(Product $product, int $price, int $quantity = 1): array
    {
        return [
            $product->id => [
                'title'      => $product->title,
                'author'     => $product->author,
                'quantity'   => $quantity,
                'price'      => $price,
                'image_path' => null,
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Happy path
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function first15_coupon_deducts_15_percent_from_subtotal_only(): void
    {
        // subtotal = 1 000, shipping = 60, total before discount = 1 060
        // discount = round(1 000 × 0.15) = 150
        // expected final total = 1 060 – 150 = 910
        $product = Product::factory()->create(['paperback_price' => 1000, 'stock_quantity' => 5]);
        $user    = User::factory()->create(['email' => 'subscriber@shelf-e.com']);
        Subscriber::create(['email' => 'subscriber@shelf-e.com', 'discount_used' => false]);

        $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 1000)])
             ->post(route('checkout.store'), array_merge($this->basePayload, [
                 'coupon_code' => 'FIRST15',
             ]))
             ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id'         => $user->id,
            'subtotal'        => 1000,
            'shipping_cost'   => 60,
            'discount_amount' => 150,
            'coupon_code'     => 'FIRST15',
            'total_amount'    => 910,
        ]);
    }

    /** @test */
    public function coupon_is_case_insensitive(): void
    {
        $product = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 5]);
        $user    = User::factory()->create(['email' => 'reader@shelf-e.com']);
        Subscriber::create(['email' => 'reader@shelf-e.com', 'discount_used' => false]);

        $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 500)])
             ->post(route('checkout.store'), array_merge($this->basePayload, [
                 'coupon_code' => 'first15', // lowercase
             ]))
             ->assertRedirect();

        $this->assertDatabaseHas('orders', ['coupon_code' => 'FIRST15']);
    }

    /** @test */
    public function subscriber_discount_used_flag_is_set_to_true_after_redemption(): void
    {
        $product    = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 5]);
        $user       = User::factory()->create(['email' => 'once@shelf-e.com']);
        $subscriber = Subscriber::create(['email' => 'once@shelf-e.com', 'discount_used' => false]);

        $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 500)])
             ->post(route('checkout.store'), array_merge($this->basePayload, [
                 'coupon_code' => 'FIRST15',
             ]));

        $this->assertTrue($subscriber->fresh()->discount_used);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // One-time use enforcement
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function coupon_is_rejected_when_already_used(): void
    {
        $product    = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 10]);
        $user       = User::factory()->create(['email' => 'used@shelf-e.com']);
        // Subscriber record already marked as used
        Subscriber::create(['email' => 'used@shelf-e.com', 'discount_used' => true]);

        $response = $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 500)])
             ->post(route('checkout.store'), array_merge($this->basePayload, [
                 'coupon_code' => 'FIRST15',
             ]));

        $response->assertSessionHasErrors('coupon_code');
        $this->assertDatabaseMissing('orders', ['user_id' => $user->id]);
    }

    /** @test */
    public function coupon_cannot_be_applied_twice_in_two_separate_orders(): void
    {
        $product    = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 10]);
        $user       = User::factory()->create(['email' => 'twotime@shelf-e.com']);
        $subscriber = Subscriber::create(['email' => 'twotime@shelf-e.com', 'discount_used' => false]);

        // First order — succeeds
        $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 500)])
             ->post(route('checkout.store'), array_merge($this->basePayload, [
                 'coupon_code' => 'FIRST15',
             ]));

        $this->assertTrue($subscriber->fresh()->discount_used);

        // Second order — must be rejected
        $response = $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 500, 2)])
             ->post(route('checkout.store'), array_merge($this->basePayload, [
                 'coupon_code' => 'FIRST15',
             ]));

        $response->assertSessionHasErrors('coupon_code');
        $this->assertDatabaseCount('orders', 1); // only the first order exists
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Coupon tied to the authenticated account email (not billing field)
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function coupon_is_rejected_when_account_email_is_not_a_subscriber(): void
    {
        // The subscriber record belongs to a different email
        $product = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 5]);
        $user    = User::factory()->create(['email' => 'notsubscribed@shelf-e.com']);
        Subscriber::create(['email' => 'someoneelse@shelf-e.com', 'discount_used' => false]);

        $response = $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 500)])
             ->post(route('checkout.store'), array_merge($this->basePayload, [
                 'coupon_code' => 'FIRST15',
             ]));

        $response->assertSessionHasErrors('coupon_code');
        $this->assertDatabaseMissing('orders', ['user_id' => $user->id]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Guard: guests cannot use coupons
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function guest_cannot_apply_a_coupon_code(): void
    {
        $product = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 5]);
        Subscriber::create(['email' => 'guest@shelf-e.com', 'discount_used' => false]);

        $response = $this->withSession(['cart' => $this->cartWith($product, 500)])
             ->post(route('checkout.store'), array_merge($this->basePayload, [
                 'coupon_code' => 'FIRST15',
                 // guest submits billing email matching the subscriber record
                 'email' => 'guest@shelf-e.com',
             ]));

        $response->assertSessionHasErrors('coupon_code');
        $this->assertDatabaseMissing('orders', []);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Invalid codes
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function invalid_coupon_code_is_rejected_with_a_validation_error(): void
    {
        $product = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 5]);
        $user    = User::factory()->create();

        $response = $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 500)])
             ->post(route('checkout.store'), array_merge($this->basePayload, [
                 'coupon_code' => 'BOGUS99',
             ]));

        $response->assertSessionHasErrors('coupon_code');
        $this->assertDatabaseMissing('orders', ['user_id' => $user->id]);
    }

    /** @test */
    public function no_discount_is_applied_when_coupon_field_is_empty(): void
    {
        // subtotal = 500, shipping = 60, total = 560 (no discount)
        $product = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 5]);
        $user    = User::factory()->create();

        $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 500)])
             ->post(route('checkout.store'), $this->basePayload) // no coupon_code key
             ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id'         => $user->id,
            'discount_amount' => 0,
            'coupon_code'     => null,
            'total_amount'    => 560,
        ]);
    }
}
