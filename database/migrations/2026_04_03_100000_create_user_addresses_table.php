<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label', 50);       // e.g. Home, Work, Other
            $table->string('name');
            $table->string('email');
            $table->string('address');
            $table->string('division', 50);
            $table->string('district', 100);
            $table->string('postal_code', 20)->nullable();
            $table->string('phone', 20);       // local part, e.g. 1712345678
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
