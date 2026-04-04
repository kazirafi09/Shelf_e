<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete()->after('id');
            $table->string('image_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->string('image_path')->nullable(false)->change();
        });
    }
};
