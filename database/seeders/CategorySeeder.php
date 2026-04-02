<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fakeCategories = [
            'Science Fiction',
            'Mystery & Thriller',
            'Biography & Memoir',
            'Self-Help & Wellness',
            'Cookbooks & Food',
            'Graphic Novels',
            'Poetry',
            'History & Politics',
            'Business & Economics',
            'Travel Guides'
        ];

        foreach ($fakeCategories as $categoryName) {
            // This prevents adding duplicates if you run the seeder twice
            Category::firstOrCreate([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName)
            ]);
        }
    }
}
