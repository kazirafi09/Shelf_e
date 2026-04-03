<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductPreview;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductPreview>
 */
class ProductPreviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'type'       => fake()->randomElement(['image', 'video']),
            'path'       => fake()->filePath(),
            'sort_order' => fake()->numberBetween(0, 255),
        ];
    }
}
