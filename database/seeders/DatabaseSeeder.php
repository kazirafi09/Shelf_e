<?php

namespace Database\Seeders;

use App\Models\User;
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
            
            Product::create([
                'category_id' => $faker->randomElement($categoryIds),
                'title' => ucwords($title),
                'author' => $faker->name(),
                'slug' => Str::slug($title) . '-' . $faker->numberBetween(100, 999),
                'description' => $faker->paragraph(),
                // Generate a random price between 300 and 3000 BDT
                'price' => $faker->randomFloat(2, 300, 3000), 
                'stock_quantity' => $faker->numberBetween(0, 50),
                // Generate a random rating between 3.5 and 5.0
                'rating' => $faker->randomFloat(1, 3.5, 5.0), 
            ]);
        }
    }
}