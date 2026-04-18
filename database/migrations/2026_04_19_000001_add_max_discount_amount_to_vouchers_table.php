<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->decimal('max_discount_amount', 10, 2)->nullable()->after('discount_value');
        });

        // Cap the existing FIRST15 voucher at ৳100
        DB::table('vouchers')->where('code', 'FIRST15')->update([
            'max_discount_amount' => 100,
        ]);
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('max_discount_amount');
        });
    }
};
