<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Fiction',
            'Mystery',
            'Science Fiction',
            'Fantasy',
            'Romance',
            'Horror',
            'Thriller',
            'Historical Fiction',
            'Adventure',
            'Biography',
            'History',
            'Self-Help',
            'Philosophy',
            'Religion',
            'Poetry',
            'Drama',
            'Travel',
            'Cooking',
            'Art',
            'Music',
            'Sports',
            'Science',
            'Technology',
            'Business',
            'Finance',
            'Economics',
            'Politics',
            'Psychology',
            'Education',
            'Health',
            'Comics & Graphic Novels',
            'Children',
            'Young Adult',
            'Bangla Literature',
            'Uncategorized',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
