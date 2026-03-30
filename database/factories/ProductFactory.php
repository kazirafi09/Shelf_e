<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        // Randomly decide if a book has paperback, hardcover, or both
        $hasPaperback = $this->faker->boolean(80); // 80% chance
        $hasHardcover = $this->faker->boolean(60); // 60% chance
        
        // Ensure at least one format exists
        if (!$hasPaperback && !$hasHardcover) { $hasPaperback = true; }

        return [
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id ?? 1,
            'title' => $title,
            'author' => $this->faker->name(),
            'slug' => \Illuminate\Support\Str::slug($title),
            'description' => $this->faker->paragraphs(3, true),
            'synopsis' => $this->faker->paragraph(),
            // Assign prices only if the format exists, otherwise null
            'paperback_price' => $hasPaperback ? $this->faker->randomFloat(2, 300, 800) : null,
            'hardcover_price' => $hasHardcover ? $this->faker->randomFloat(2, 900, 2500) : null,
            'stock_quantity' => $this->faker->numberBetween(0, 50),
            'rating' => $this->faker->randomFloat(1, 3, 5),
            'image_path' => null, // Or a placeholder path
        ];
    }
}
