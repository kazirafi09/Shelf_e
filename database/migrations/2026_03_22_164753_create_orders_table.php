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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // Nullable allows Guests to order!
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            $table->string('name')->nullable();
            $table->string('phone');
            $table->text('address');
            $table->string('division');
            $table->string('district');
            $table->string('postal_code')->nullable();

            $table->string('delivery_method'); // 'regular' or 'express'
            $table->string('payment_method');  // 'cod'

            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2);
            $table->decimal('total_amount', 10, 2);

            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
