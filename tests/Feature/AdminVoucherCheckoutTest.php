<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * End-to-end test: admin creates a custom voucher, customer redeems it at checkout.
 *
 * Covers the full loop that the user asked about:
 *  1. Admin hits POST /admin/vouchers with a custom code.
 *  2. Voucher is persisted and marked active.
 *  3. A separate customer applies that exact code at /checkout.
 *  4. Discount is calculated, order total reflects it, voucher_usages row is created,
 *     and used_count on the voucher is incremented.
 */
class AdminVoucherCheckoutTest extends TestCase
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

    /** @test */
    public function admin_can_create_a_percentage_voucher_and_a_customer_redeems_it_at_checkout(): void
    {
        // ── 1. Admin creates a custom voucher via the admin panel ─────────────
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.vouchers.store'), [
                'code'              => 'SAVE20',
                'description'       => 'Custom admin-created voucher — 20% off',
                'discount_type'     => 'percentage',
                'discount_value'    => 20,
                'min_order_amount'  => 500,
                'max_uses'          => 100,
                'max_uses_per_user' => 1,
                'is_active'         => 1,
            ])
            ->assertRedirect(route('admin.vouchers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('vouchers', [
            'code'           => 'SAVE20',
            'discount_type'  => 'percentage',
            'discount_value' => 20,
            'is_active'      => true,
        ]);

        $voucher = Voucher::where('code', 'SAVE20')->firstOrFail();

        // ── 2. A customer applies that voucher at checkout ────────────────────
        // subtotal = 2000, shipping (Dhaka, <1500 free threshold not met because 2000 ≥ 1500) = free
        // Actually 2000 ≥ 1500 so shipping = 0.
        // discount = round(2000 × 0.20) = 400
        // expected total = 2000 + 0 – 400 = 1600
        $product  = Product::factory()->create(['paperback_price' => 2000, 'stock_quantity' => 5]);
        $customer = User::factory()->create(['email' => 'shopper@example.com']);

        $this->actingAs($customer)
            ->withSession(['cart' => $this->cartWith($product, 2000)])
            ->post(route('checkout.store'), array_merge($this->basePayload, [
                'coupon_code' => 'SAVE20',
            ]))
            ->assertRedirect();

        // ── 3. Order record reflects the discount ─────────────────────────────
        $this->assertDatabaseHas('orders', [
            'user_id'         => $customer->id,
            'subtotal'        => 2000,
            'shipping_cost'   => 0,
            'discount_amount' => 400,
            'coupon_code'     => 'SAVE20',
            'total_amount'    => 1600,
        ]);

        // ── 4. voucher_usages row exists and used_count incremented ───────────
        $this->assertDatabaseHas('voucher_usages', [
            'voucher_id' => $voucher->id,
            'user_id'    => $customer->id,
        ]);
        $this->assertSame(1, (int) $voucher->fresh()->used_count);
    }

    /** @test */
    public function admin_can_create_a_fixed_amount_voucher_and_customer_redeems_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.vouchers.store'), [
                'code'              => 'FLAT100',
                'discount_type'     => 'fixed',
                'discount_value'    => 100,
                'max_uses_per_user' => 1,
                'is_active'         => 1,
            ])
            ->assertRedirect(route('admin.vouchers.index'));

        // subtotal = 800, shipping (Dhaka, <1500) = 60, discount = 100
        // expected total = 800 + 60 – 100 = 760
        $product  = Product::factory()->create(['paperback_price' => 800, 'stock_quantity' => 5]);
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->withSession(['cart' => $this->cartWith($product, 800)])
            ->post(route('checkout.store'), array_merge($this->basePayload, [
                'coupon_code' => 'FLAT100',
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id'         => $customer->id,
            'subtotal'        => 800,
            'shipping_cost'   => 60,
            'discount_amount' => 100,
            'coupon_code'     => 'FLAT100',
            'total_amount'    => 760,
        ]);
    }

    /** @test */
    public function custom_voucher_is_rejected_when_min_order_amount_not_met(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.vouchers.store'), [
                'code'              => 'BIG25',
                'discount_type'     => 'percentage',
                'discount_value'    => 25,
                'min_order_amount'  => 1000, // needs 1000+
                'max_uses_per_user' => 1,
                'is_active'         => 1,
            ]);

        $product  = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 5]);
        $customer = User::factory()->create();

        $response = $this->actingAs($customer)
            ->withSession(['cart' => $this->cartWith($product, 500)])
            ->post(route('checkout.store'), array_merge($this->basePayload, [
                'coupon_code' => 'BIG25',
            ]));

        $response->assertSessionHasErrors('coupon_code');
        $this->assertDatabaseMissing('orders', ['user_id' => $customer->id]);
    }

    /** @test */
    public function custom_voucher_is_rejected_when_inactive(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.vouchers.store'), [
                'code'              => 'DISABLED',
                'discount_type'     => 'percentage',
                'discount_value'    => 10,
                'max_uses_per_user' => 1,
                // is_active omitted — controller defaults to true, so flip it manually after
            ]);

        Voucher::where('code', 'DISABLED')->update(['is_active' => false]);

        $product  = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 5]);
        $customer = User::factory()->create();

        $response = $this->actingAs($customer)
            ->withSession(['cart' => $this->cartWith($product, 500)])
            ->post(route('checkout.store'), array_merge($this->basePayload, [
                'coupon_code' => 'DISABLED',
            ]));

        $response->assertSessionHasErrors('coupon_code');
    }

    /** @test */
    public function custom_voucher_per_user_limit_is_enforced_across_orders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.vouchers.store'), [
                'code'              => 'ONCE',
                'discount_type'     => 'percentage',
                'discount_value'    => 10,
                'max_uses_per_user' => 1,
                'is_active'         => 1,
            ]);

        $product  = Product::factory()->create(['paperback_price' => 500, 'stock_quantity' => 20]);
        $customer = User::factory()->create();

        // First order succeeds
        $this->actingAs($customer)
            ->withSession(['cart' => $this->cartWith($product, 500)])
            ->post(route('checkout.store'), array_merge($this->basePayload, [
                'coupon_code' => 'ONCE',
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id'     => $customer->id,
            'coupon_code' => 'ONCE',
        ]);

        // Second attempt must be blocked
        $response = $this->actingAs($customer)
            ->withSession(['cart' => $this->cartWith($product, 500, 2)])
            ->post(route('checkout.store'), array_merge($this->basePayload, [
                'coupon_code' => 'ONCE',
            ]));

        $response->assertSessionHasErrors('coupon_code');
        $this->assertDatabaseCount('orders', 1);
    }
}
