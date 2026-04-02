<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Disable foreign key checks to allow truncation
        Schema::disableForeignKeyConstraints();

        // 2. Wipe the existing products
        Product::truncate();

        Schema::enableForeignKeyConstraints();

        // 3. Create 100 fresh books using the factory
        Product::factory()->count(100)->create();
    }
}
