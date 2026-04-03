<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    private const DIVISIONS = [
        'Dhaka', 'Chittagong', 'Rajshahi', 'Khulna',
        'Barisal', 'Sylhet', 'Rangpur', 'Mymensingh',
    ];

    private const DISTRICTS = [
        'Dhaka', 'Gazipur', 'Narayanganj', 'Chittagong', 'Cox\'s Bazar',
        'Rajshahi', 'Khulna', 'Barisal', 'Sylhet', 'Rangpur',
        'Comilla', 'Mymensingh', 'Bogura', 'Jessore', 'Dinajpur',
    ];

    public function definition(): array
    {
        $deliveryMethod = fake()->randomElement(['regular', 'express']);
        $shippingCost   = $deliveryMethod === 'express' ? 150.00 : 60.00;
        $subtotal       = fake()->randomFloat(2, 200, 5000);
        $totalAmount    = $subtotal + $shippingCost;

        return [
            'user_id'         => User::factory(),
            'name'            => fake()->name(),
            'email'           => fake()->safeEmail(),
            'phone'           => fake()->numerify('01#########'),
            'address'         => fake()->streetAddress(),
            'division'        => fake()->randomElement(self::DIVISIONS),
            'district'        => fake()->randomElement(self::DISTRICTS),
            'postal_code'     => fake()->numerify('####'),
            'delivery_method' => $deliveryMethod,
            'payment_method'  => 'cod',
            'subtotal'        => $subtotal,
            'shipping_cost'   => $shippingCost,
            'total_amount'    => $totalAmount,
            'status'          => 'pending',
        ];
    }

    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
