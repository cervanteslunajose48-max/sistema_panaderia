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
        Schema::table('inventory_movements', function (Blueprint $table) {
            // Make existing columns nullable
            $table->unsignedBigInteger('supply_id')->nullable()->change();
            $table->unsignedBigInteger('warehouse_id')->nullable()->change();

            // Add missing columns if they don't exist
            if (!Schema::hasColumn('inventory_movements', 'product_variant_id')) {
                $table->unsignedBigInteger('product_variant_id')->nullable()->after('warehouse_id');
            }

            if (!Schema::hasColumn('inventory_movements', 'description')) {
                $table->string('description')->nullable();
            }

            if (!Schema::hasColumn('inventory_movements', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_movements', 'product_variant_id')) {
                $table->dropColumn('product_variant_id');
            }
            if (Schema::hasColumn('inventory_movements', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('inventory_movements', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
