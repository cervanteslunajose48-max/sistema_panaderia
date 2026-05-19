<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::table('supplies', function (Blueprint $table) {
        //     // Check if supplier_id column exists before adding
        //     if (!Schema::hasColumn('supplies', 'supplier_id')) {
        //         $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
        //     }

        //     // Check if status column exists before adding
        //     if (!Schema::hasColumn('supplies', 'status')) {
        //         $table->boolean('status')->default(true);
        //     }
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplies', function (Blueprint $table) {
            if (Schema::hasColumn('supplies', 'supplier_id')) {
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            }

            if (Schema::hasColumn('supplies', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
