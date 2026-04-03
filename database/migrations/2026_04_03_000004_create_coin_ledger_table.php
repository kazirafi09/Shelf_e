<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('coin_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['credit', 'debit']);
            $table->unsignedInteger('amount');
            $table->string('description');
            $table->unsignedInteger('balance_after');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coin_ledger');
    }
};
