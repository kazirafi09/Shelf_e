<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Author>
 */
class AuthorFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name'       => $name,
            'slug'       => Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'bio'        => fake()->optional()->paragraphs(2, true),
            'photo_path' => fake()->optional()->imageUrl(200, 200, 'people'),
        ];
    }
}
