<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Using nullable decimals. If null, the format is unavailable.
            $table->decimal('paperback_price', 10, 2)->nullable()->after('price');
            $table->decimal('hardcover_price', 10, 2)->nullable()->after('paperback_price');

            // Note: You can eventually drop the original 'price' column once data is migrated,
            // or keep it as a "default/display" price.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
