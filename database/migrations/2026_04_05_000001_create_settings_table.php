<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('settings')->insert([
            [
                'key'        => 'announcement_text',
                'value'      => 'Free Standard Shipping on orders over ৳1000!',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'fomo_ends_at',
                'value'      => now()->addHours(24)->toIso8601String(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
