<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class BackfillCategoryPivot extends Command
{
    protected $signature = 'app:backfill-category-pivot';

    protected $description = 'Populate the category_product pivot from products.category_id for any product missing pivot entries';

    public function handle(): void
    {
        $products = Product::whereNotNull('category_id')
            ->whereDoesntHave('categories')
            ->get();

        if ($products->isEmpty()) {
            $this->info('No products need backfilling — pivot is already in sync.');
            return;
        }

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $product->categories()->sync([$product->category_id]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Backfilled {$products->count()} product(s).");
    }
}
