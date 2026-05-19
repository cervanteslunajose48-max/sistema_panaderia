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
        Schema::create('product_transformations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('target_variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->decimal('source_quantity', 10, 2);
            $table->decimal('target_quantity', 10, 2);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_transformations');
    }
};
