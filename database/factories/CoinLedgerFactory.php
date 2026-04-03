<?php

namespace Database\Factories;

use App\Models\CoinLedger;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CoinLedger>
 */
class CoinLedgerFactory extends Factory
{
    public function definition(): array
    {
        $amount = fake()->numberBetween(1, 500);

        return [
            'user_id'      => User::factory(),
            'type'         => fake()->randomElement(['credit', 'debit']),
            'amount'       => $amount,
            'description'  => fake()->sentence(),
            'balance_after' => fake()->numberBetween(0, 5000),
        ];
    }
}
