<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for CartController: add, remove, increment, decrement.
 *
 * Key security assertion: prices are always sourced from the database when an
 * item is added, never from user-supplied input.  The cart session is treated
 * as a convenience cache, not as a source of truth for pricing.
 *
 * NOTE: All session assertions are called on the TestResponse instance because
 * in PHPUnit 11 / Laravel 12, $this->assertSessionHas() is not available on
 * the TestCase base class.
 */
class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // ProductFactory resolves category_id via Category::inRandomOrder()->first()
        Category::factory()->create();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build the cart session payload exactly as CartController::add() would.
     */
    private function cartSession(Product $product, int $price, int $quantity = 1): array
    {
        return [
            $product->id => [
                'title'      => $product->title,
                'author'     => $product->author,
                'quantity'   => $quantity,
                'price'      => $price,
                'image_path' => $product->image_path,
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Adding items
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function adding_a_paperback_stores_the_database_price_not_a_user_value(): void
    {
        $product = Product::factory()->create([
            'paperback_price' => 450,
            'stock_quantity'  => 10,
        ]);

        $this->post(route('cart.add', $product->id), [
            'format'   => 'paperback',
            'quantity' => 1,
        ])
        ->assertRedirect()
        ->assertSessionHas('cart', function (array $cart) use ($product) {
            return isset($cart[$product->id])
                && $cart[$product->id]['price'] == 450;
        });
    }

    /** @test */
    public function adding_a_hardcover_stores_the_hardcover_price(): void
    {
        $product = Product::factory()->create([
            'hardcover_price' => 1200,
            'stock_quantity'  => 5,
        ]);

        $this->post(route('cart.add', $product->id), [
            'format'   => 'hardcover',
            'quantity' => 1,
        ])
        ->assertRedirect()
        ->assertSessionHas('cart', function (array $cart) use ($product) {
            return isset($cart[$product->id])
                && $cart[$product->id]['price'] == 1200;
        });
    }

    /** @test */
    public function cannot_add_an_unavailable_format_to_the_cart(): void
    {
        // Book has no hardcover edition
        $product = Product::factory()->create([
            'paperback_price' => 400,
            'hardcover_price' => null,
            'stock_quantity'  => 5,
        ]);

        $this->post(route('cart.add', $product->id), [
            'format'   => 'hardcover',
            'quantity' => 1,
        ])
        ->assertRedirect()
        ->assertSessionMissing('cart');
    }

    /** @test */
    public function cannot_add_an_out_of_stock_book(): void
    {
        $product = Product::factory()->create([
            'paperback_price' => 300,
            'stock_quantity'  => 0,
        ]);

        $this->post(route('cart.add', $product->id), [
            'format'   => 'paperback',
            'quantity' => 1,
        ])
        ->assertRedirect()
        ->assertSessionMissing('cart');
    }

    /** @test */
    public function cannot_add_more_copies_than_available_stock(): void
    {
        $product = Product::factory()->create([
            'paperback_price' => 300,
            'stock_quantity'  => 2,
        ]);

        $this->post(route('cart.add', $product->id), [
            'format'   => 'paperback',
            'quantity' => 5,
        ])
        ->assertRedirect()
        ->assertSessionMissing('cart');
    }

    /** @test */
    public function adding_the_same_book_twice_increments_the_quantity(): void
    {
        $product = Product::factory()->create([
            'paperback_price' => 500,
            'stock_quantity'  => 10,
        ]);

        // Simulate a cart that already has one copy of the book
        $existingCart = $this->cartSession($product, 500, 1);

        $this->withSession(['cart' => $existingCart])
             ->post(route('cart.add', $product->id), [
                 'format'   => 'paperback',
                 'quantity' => 1,
             ])
             ->assertRedirect()
             ->assertSessionHas('cart', function (array $cart) use ($product) {
                 return $cart[$product->id]['quantity'] === 2;
             });
    }

    /** @test */
    public function cart_subtotal_is_price_multiplied_by_quantity(): void
    {
        // The checkout GET view receives $subtotal and $total from the controller.
        // We confirm 3 × 600 = 1 800 (subtotal) and 1 800 + 60 = 1 860 (total).
        $product = Product::factory()->create([
            'paperback_price' => 600,
            'stock_quantity'  => 10,
        ]);

        $response = $this->withSession(['cart' => $this->cartSession($product, 600, 3)])
                         ->get(route('checkout.index'));

        $response->assertOk();
        $response->assertViewHas('subtotal', 1800);
        // Checkout preview defaults to inside-Dhaka shipping (60) because Dhaka is
        // the pre-selected division.  The real cost is recalculated server-side on
        // POST checkout.store based on the division the customer actually submits.
        $response->assertViewHas('total', 1860); // 1800 + 60
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Sale price logic
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function adding_a_book_on_active_sale_uses_the_sale_price(): void
    {
        $product = Product::factory()->create([
            'paperback_price' => 500,
            'stock_quantity'  => 10,
            'sale_price'      => 350,
            'sale_ends_at'    => now()->addDay(),
        ]);

        $this->post(route('cart.add', $product->id), [
            'format'   => 'paperback',
            'quantity' => 1,
        ])
        ->assertRedirect()
        ->assertSessionHas('cart', function (array $cart) use ($product) {
            return isset($cart[$product->id])
                && $cart[$product->id]['price'] == 350;
        });
    }

    /** @test */
    public function adding_a_book_whose_sale_has_expired_uses_the_regular_format_price(): void
    {
        $product = Product::factory()->create([
            'paperback_price' => 500,
            'stock_quantity'  => 10,
            'sale_price'      => 350,
            'sale_ends_at'    => now()->subHour(), // Sale ended an hour ago
        ]);

        $this->post(route('cart.add', $product->id), [
            'format'   => 'paperback',
            'quantity' => 1,
        ])
        ->assertRedirect()
        ->assertSessionHas('cart', function (array $cart) use ($product) {
            return isset($cart[$product->id])
                && $cart[$product->id]['price'] == 500;
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Increment / Decrement
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function incrementing_a_cart_item_increases_its_quantity_by_one(): void
    {
        $product = Product::factory()->create([
            'paperback_price' => 500,
            'stock_quantity'  => 5,
        ]);

        $this->withSession(['cart' => $this->cartSession($product, 500, 2)])
             ->post(route('cart.increment', $product->id))
             ->assertRedirect()
             ->assertSessionHas('cart', function (array $cart) use ($product) {
                 return $cart[$product->id]['quantity'] === 3;
             });
    }

    /** @test */
    public function incrementing_past_available_stock_is_blocked(): void
    {
        $product = Product::factory()->create([
            'paperback_price' => 500,
            'stock_quantity'  => 2,
        ]);

        // Cart already has the maximum allowed quantity
        $this->withSession(['cart' => $this->cartSession($product, 500, 2)])
             ->post(route('cart.increment', $product->id))
             ->assertRedirect()
             ->assertSessionHas('error')
             // Quantity must remain unchanged at 2
             ->assertSessionHas('cart', function (array $cart) use ($product) {
                 return $cart[$product->id]['quantity'] === 2;
             });
    }

    /** @test */
    public function decrementing_a_multi_quantity_item_reduces_quantity_by_one(): void
    {
        $product = Product::factory()->create([
            'paperback_price' => 500,
            'stock_quantity'  => 5,
        ]);

        $this->withSession(['cart' => $this->cartSession($product, 500, 3)])
             ->post(route('cart.decrement', $product->id))
             ->assertRedirect()
             ->assertSessionHas('cart', function (array $cart) use ($product) {
                 return $cart[$product->id]['quantity'] === 2;
             });
    }

    /** @test */
    public function decrementing_a_single_quantity_item_removes_it_from_the_cart(): void
    {
        $product = Product::factory()->create([
            'paperback_price' => 500,
            'stock_quantity'  => 5,
        ]);

        $this->withSession(['cart' => $this->cartSession($product, 500, 1)])
             ->post(route('cart.decrement', $product->id))
             ->assertRedirect()
             ->assertSessionHas('cart', function (array $cart) use ($product) {
                 return ! isset($cart[$product->id]);
             });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Remove
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function removing_an_item_clears_it_from_the_cart(): void
    {
        $product = Product::factory()->create([
            'paperback_price' => 500,
            'stock_quantity'  => 5,
        ]);

        $this->withSession(['cart' => $this->cartSession($product, 500, 2)])
             ->post(route('cart.remove', $product->id))
             ->assertRedirect()
             ->assertSessionHas('success')
             ->assertSessionHas('cart', function (array $cart) use ($product) {
                 return ! isset($cart[$product->id]);
             });
    }

    /** @test */
    public function removing_a_non_existent_item_does_not_error(): void
    {
        $product = Product::factory()->create([
            'paperback_price' => 500,
            'stock_quantity'  => 5,
        ]);

        // Cart has product, we try to remove a different ID
        $this->withSession(['cart' => $this->cartSession($product, 500)])
             ->post(route('cart.remove', 999999))
             ->assertRedirect()
             // Original item must still be present
             ->assertSessionHas('cart', function (array $cart) use ($product) {
                 return isset($cart[$product->id]);
             });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Buy Now shortcut
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function buy_now_action_redirects_directly_to_checkout(): void
    {
        $product = Product::factory()->create([
            'paperback_price' => 500,
            'stock_quantity'  => 5,
        ]);

        $this->post(route('cart.add', $product->id), [
            'format'      => 'paperback',
            'quantity'    => 1,
            'action_type' => 'buy_now',
        ])->assertRedirect(route('checkout.index'));
    }
}
