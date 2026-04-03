<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutCoinRedemptionTest extends TestCase
{
    use RefreshDatabase;

    /** Shared valid checkout form fields (excluding payment-specific ones). */
    private array $basePayload = [
        'name'     => 'Test Customer',
        'email'    => 'customer@example.com',
        'phone'    => '1712345678',
        'address'  => '123 Test Street',
        'division' => 'Dhaka',
        'district' => 'Dhaka City',
        'delivery' => 'standard',   // shipping = 60
        'payment'  => 'cod',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Category::factory()->create(); // ProductFactory requires a category
    }

    private function buildCart(Product $product, int $price = 500, int $quantity = 1): array
    {
        return [
            $product->id => [
                'title'    => $product->title,
                'price'    => $price,
                'quantity' => $quantity,
            ],
        ];
    }

    public function test_coins_are_redeemed_and_balance_decreases_on_checkout(): void
    {
        $user    = User::factory()->create(['coin_balance' => 200]);
        $product = Product::factory()->create(['stock_quantity' => 10, 'paperback_price' => 500]);

        // subtotal = 500, shipping = 60, total = 560, coinsToRedeem = min(200, 560) = 200
        // finalTotal = 360
        $this->actingAs($user)
            ->withSession(['cart' => $this->buildCart($product, 500)])
            ->post(route('checkout.store'), array_merge($this->basePayload, [
                'redeem_coins' => '1',
            ]))
            ->assertRedirect();

        $this->assertEquals(0, $user->fresh()->coin_balance);

        $this->assertDatabaseHas('orders', [
            'user_id'      => $user->id,
            'total_amount' => 360,
        ]);

        $this->assertDatabaseHas('coin_ledger', [
            'user_id' => $user->id,
            'type'    => 'debit',
            'amount'  => 200,
        ]);
    }

    public function test_order_total_is_not_discounted_when_redeem_coins_is_absent(): void
    {
        $user    = User::factory()->create(['coin_balance' => 200]);
        $product = Product::factory()->create(['stock_quantity' => 10, 'paperback_price' => 500]);

        // subtotal = 500, shipping = 60, total = 560, no redemption
        $this->actingAs($user)
            ->withSession(['cart' => $this->buildCart($product, 500)])
            ->post(route('checkout.store'), $this->basePayload)
            ->assertRedirect();

        $this->assertEquals(200, $user->fresh()->coin_balance);

        $this->assertDatabaseHas('orders', [
            'user_id'      => $user->id,
            'total_amount' => 560,
        ]);

        $this->assertDatabaseMissing('coin_ledger', ['user_id' => $user->id]);
    }

    public function test_coin_redemption_is_capped_at_the_order_total(): void
    {
        // User has far more coins than the order is worth
        $user    = User::factory()->create(['coin_balance' => 5000]);
        $product = Product::factory()->create(['stock_quantity' => 10, 'paperback_price' => 200]);

        // subtotal = 200, shipping = 60, total = 260, coinsToRedeem = min(5000, 260) = 260
        // finalTotal = 0
        $this->actingAs($user)
            ->withSession(['cart' => $this->buildCart($product, 200)])
            ->post(route('checkout.store'), array_merge($this->basePayload, [
                'redeem_coins' => '1',
            ]))
            ->assertRedirect();

        $this->assertEquals(5000 - 260, $user->fresh()->coin_balance);

        $this->assertDatabaseHas('orders', [
            'user_id'      => $user->id,
            'total_amount' => 0,
        ]);
    }
}
