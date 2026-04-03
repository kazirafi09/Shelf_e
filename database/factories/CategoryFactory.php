<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $genres = [
            'Fiction', 'Non-Fiction', 'Science Fiction', 'Fantasy', 'Mystery',
            'Thriller', 'Romance', 'Horror', 'Biography', 'History',
            'Self-Help', 'Psychology', 'Philosophy', 'Science', 'Technology',
            'Children', 'Young Adult', 'Poetry', 'Drama', 'Comics',
        ];

        $name = fake()->unique()->randomElement($genres);

        return [
            'name'      => $name,
            'slug'      => Str::slug($name),
            'parent_id' => null,
        ];
    }

    /**
     * State for a sub-category that belongs to a parent category.
     */
    public function withParent(Category $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }
}
