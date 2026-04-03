<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'               => User::factory(),
            'product_id'            => Product::factory(),
            'rating'                => fake()->numberBetween(1, 5),
            'title'                 => fake()->optional()->sentence(6),
            'body'                  => fake()->paragraph(),
            'status'                => fake()->randomElement(['pending', 'approved', 'rejected']),
            'is_verified_purchase'  => fake()->boolean(),
        ];
    }
}
