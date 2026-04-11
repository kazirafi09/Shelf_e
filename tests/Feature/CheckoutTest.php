<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for the full checkout → order lifecycle.
 *
 * Covers:
 *  - Order record created with correct financial breakdown
 *  - Shipping cost varies by delivery method (standard vs. express)
 *  - Cart is emptied from the session after a successful order
 *  - Product stock is decremented inside the same transaction
 *  - An empty cart is rejected before writing anything to the database
 *  - Guest checkout is permitted (user_id = null)
 *  - Authenticated orders are linked to the user account
 *  - Order items are inserted for each cart line
 *  - IDOR: the confirmation page blocks users viewing other people's orders
 *
 * NOTE: Coin-redemption scenarios → tests\Feature\CheckoutCoinRedemptionTest
 *       FIRST15 coupon scenarios  → tests\Feature\CheckoutDiscountTest
 */
class CheckoutTest extends TestCase
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
    // Helpers
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
    // Financial calculations
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function dhaka_division_applies_inside_dhaka_shipping(): void
    {
        $product = Product::factory()->create(['paperback_price' => 800, 'stock_quantity' => 5]);
        $user    = User::factory()->create();

        // basePayload has division = Dhaka → inside_dhaka rate (default 60)
        $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 800)])
             ->post(route('checkout.store'), $this->basePayload);

        $this->assertDatabaseHas('orders', [
            'user_id'         => $user->id,
            'subtotal'        => 800,
            'shipping_cost'   => 60,
            'total_amount'    => 860,
            'delivery_method' => 'inside_dhaka',
        ]);
    }

    /** @test */
    public function non_dhaka_division_applies_outside_dhaka_shipping(): void
    {
        $product = Product::factory()->create(['paperback_price' => 800, 'stock_quantity' => 5]);
        $user    = User::factory()->create();

        $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 800)])
             ->post(route('checkout.store'), array_merge($this->basePayload, [
                 'division' => 'Chattogram',
             ]));

        $this->assertDatabaseHas('orders', [
            'user_id'         => $user->id,
            'subtotal'        => 800,
            'shipping_cost'   => 150,
            'total_amount'    => 950,
            'delivery_method' => 'outside_dhaka',
        ]);
    }

    /** @test */
    public function multi_item_order_totals_all_lines_correctly(): void
    {
        // Product A: 2 × 500 = 1 000
        // Product B: 3 × 200 = 600
        // subtotal = 1 600, division = Dhaka → free shipping (≥ ৳1500), total = 1 600
        $productA = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 10]);
        $productB = Product::factory()->create(['paperback_price' => 200, 'stock_quantity' => 10]);
        $user     = User::factory()->create();

        $cart = [
            $productA->id => ['title' => $productA->title, 'author' => $productA->author, 'quantity' => 2, 'price' => 500, 'image_path' => null],
            $productB->id => ['title' => $productB->title, 'author' => $productB->author, 'quantity' => 3, 'price' => 200, 'image_path' => null],
        ];

        $this->actingAs($user)
             ->withSession(['cart' => $cart])
             ->post(route('checkout.store'), $this->basePayload);

        $this->assertDatabaseHas('orders', [
            'user_id'       => $user->id,
            'subtotal'      => 1600,
            'shipping_cost' => 0,
            'total_amount'  => 1600,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cart lifecycle
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function cart_session_is_emptied_after_a_successful_order(): void
    {
        $product = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 5]);
        $user    = User::factory()->create();

        $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 500)])
             ->post(route('checkout.store'), $this->basePayload)
             ->assertRedirect()
             ->assertSessionMissing('cart');
    }

    /** @test */
    public function checkout_with_an_empty_cart_redirects_without_creating_an_order(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->withSession(['cart' => []]) // explicitly empty cart
             ->post(route('checkout.store'), $this->basePayload)
             ->assertRedirect();

        $this->assertDatabaseCount('orders', 0);
    }

    /** @test */
    public function checkout_without_any_cart_session_redirects_without_creating_an_order(): void
    {
        $user = User::factory()->create();

        // No withSession call — cart key is absent entirely
        $this->actingAs($user)
             ->post(route('checkout.store'), $this->basePayload)
             ->assertRedirect();

        $this->assertDatabaseCount('orders', 0);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Stock management
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function product_stock_is_decremented_after_a_successful_order(): void
    {
        $product = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 10]);
        $user    = User::factory()->create();

        $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 500, 3)])
             ->post(route('checkout.store'), $this->basePayload);

        $this->assertDatabaseHas('products', [
            'id'             => $product->id,
            'stock_quantity' => 7, // 10 − 3
        ]);
    }

    /** @test */
    public function checkout_fails_gracefully_when_stock_runs_out_between_cart_and_payment(): void
    {
        $product = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 1]);
        $user    = User::factory()->create();

        // Cart claims quantity 2, but only 1 is in stock (race condition simulation)
        $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 500, 2)])
             ->post(route('checkout.store'), $this->basePayload)
             ->assertRedirect();

        $this->assertDatabaseCount('orders', 0);

        // Stock must remain untouched (transaction rolled back)
        $this->assertDatabaseHas('products', [
            'id'             => $product->id,
            'stock_quantity' => 1,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Guest vs authenticated order ownership
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function guest_can_place_an_order_and_user_id_is_null(): void
    {
        $product = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 5]);

        $this->withSession(['cart' => $this->cartWith($product, 500)])
             ->post(route('checkout.store'), $this->basePayload)
             ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => null,
            'email'   => 'customer@example.com',
        ]);
    }

    /** @test */
    public function authenticated_order_is_linked_to_the_users_account(): void
    {
        $product = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 5]);
        $user    = User::factory()->create();

        $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 500)])
             ->post(route('checkout.store'), $this->basePayload);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Order items
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function an_order_item_is_created_for_each_cart_line(): void
    {
        $productA = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 10]);
        $productB = Product::factory()->create(['paperback_price' => 300, 'stock_quantity' => 10]);
        $user     = User::factory()->create();

        $cart = [
            $productA->id => ['title' => $productA->title, 'author' => $productA->author, 'quantity' => 1, 'price' => 500, 'image_path' => null],
            $productB->id => ['title' => $productB->title, 'author' => $productB->author, 'quantity' => 2, 'price' => 300, 'image_path' => null],
        ];

        $this->actingAs($user)
             ->withSession(['cart' => $cart])
             ->post(route('checkout.store'), $this->basePayload);

        $order = Order::where('user_id', $user->id)->firstOrFail();

        $this->assertDatabaseHas('order_items', [
            'order_id'   => $order->id,
            'product_id' => $productA->id,
            'quantity'   => 1,
            'price'      => 500,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id'   => $order->id,
            'product_id' => $productB->id,
            'quantity'   => 2,
            'price'      => 300,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Confirmation page & IDOR protection
    // (See security audit finding: OrderController::confirmation — broken guard)
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function order_owner_can_view_their_confirmation_page(): void
    {
        $user  = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
             ->get(route('order.confirmation', $order->id))
             ->assertOk();
    }

    /** @test */
    public function another_authenticated_user_cannot_view_someone_elses_confirmation(): void
    {
        $owner    = User::factory()->create();
        $intruder = User::factory()->create();
        $order    = Order::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($intruder)
             ->get(route('order.confirmation', $order->id))
             ->assertForbidden();
    }

    /** @test */
    public function unauthenticated_visitor_cannot_view_a_confirmed_order(): void
    {
        // Create the order directly via factory so this test is purely about
        // the confirmation route's authorisation logic, with no prior actingAs()
        // call that would persist into the guest GET request below.
        $owner = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $owner->id]);

        // A guest (no actingAs) must be blocked from reading order details
        $this->get(route('order.confirmation', $order->id))
             ->assertForbidden();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Successful redirect target
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function successful_checkout_redirects_to_the_confirmation_page(): void
    {
        $product = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 5]);
        $user    = User::factory()->create();

        $response = $this->actingAs($user)
             ->withSession(['cart' => $this->cartWith($product, 500)])
             ->post(route('checkout.store'), $this->basePayload);

        $order = Order::where('user_id', $user->id)->firstOrFail();

        $response->assertRedirect(route('order.confirmation', $order->id));
    }
}
