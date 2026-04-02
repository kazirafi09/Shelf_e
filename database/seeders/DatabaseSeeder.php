<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create your core categories
        $categories = ['Fiction', 'Non-Fiction', 'Audiobooks', 'Kid\'s Books'];

        foreach ($categories as $categoryName) {
            Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
            ]);
        }

        // 2. Grab all category IDs so we can assign books to them
        $categoryIds = Category::pluck('id')->toArray();

        // 3. Generate 50 fake books
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $title = $faker->words(3, true); // Generates a random 3-word title

            $hasPaperback = $faker->boolean(80);
            $hasHardcover = $faker->boolean(60);
            if (!$hasPaperback && !$hasHardcover) {
                $hasPaperback = true;
            }

            Product::create([
                'category_id' => $faker->randomElement($categoryIds),
                'title' => ucwords($title),
                'author' => $faker->name(),
                'slug' => Str::slug($title) . '-' . $faker->numberBetween(100, 999),
                'description' => $faker->paragraph(),
                'paperback_price' => $hasPaperback ? $faker->randomFloat(2, 300, 800) : null,
                'hardcover_price' => $hasHardcover ? $faker->randomFloat(2, 900, 2500) : null,
                'stock_quantity' => $faker->numberBetween(0, 50),
                'rating' => $faker->randomFloat(1, 3.5, 5.0),
            ]);
        }
    }
}
