<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Foreign key to categories table, indexed for fast searching
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            // Book details based on your UI
            $table->string('title');
            $table->string('author');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Decimal format is ideal for currency (e.g., 999.99)
            $table->decimal('price', 10, 2);

            // Inventory management
            $table->integer('stock_quantity')->default(0);

            // To display the star ratings (e.g., 4.9)
            $table->decimal('rating', 2, 1)->nullable();

            // For the book cover images
            $table->string('image_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
