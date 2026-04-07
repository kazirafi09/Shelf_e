<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 8, 2)->unsigned();
            $table->decimal('min_order_amount', 10, 2)->unsigned()->default(0);
            $table->unsignedInteger('max_uses')->nullable()->comment('null = unlimited');
            $table->unsignedInteger('max_uses_per_user')->default(1);
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_announced')->default(false);
            $table->timestamps();
        });

        // Seed the legacy FIRST15 voucher into the new system
        DB::table('vouchers')->insert([
            'code'              => 'FIRST15',
            'description'       => 'Newsletter subscriber welcome discount — 15% off your first order.',
            'discount_type'     => 'percentage',
            'discount_value'    => 15,
            'min_order_amount'  => 0,
            'max_uses'          => null,
            'max_uses_per_user' => 1,
            'used_count'        => 0,
            'expires_at'        => null,
            'is_active'         => true,
            'is_announced'      => false,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
