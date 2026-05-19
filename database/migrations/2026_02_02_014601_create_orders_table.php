<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null'); // Nullable for guest checkout
            $table->foreignId('user_id')->nullable()->constrained(); // Cashier
            $table->string('status')->default('pending'); // pending, paid, completed, cancelled
            $table->decimal('total', 10, 2)->default(0);
            $table->string('type')->default('pos'); // pos, delivery, pickup
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
