<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Make user_id nullable so guests can place orders
            $table->unsignedBigInteger('user_id')->nullable()->change();
            
            // Add shipping info columns if you don't have them already
            $table->string('name')->after('user_id');
            $table->string('email')->after('name');
            $table->string('phone')->after('email');
            $table->text('shipping_address')->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
