<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('manufacturing_cost_sheets', 'unit_weight')) {
            Schema::table('manufacturing_cost_sheets', function (Blueprint $table) {
                $table->decimal('unit_weight', 10, 2)->nullable()->after('qty');
                $table->decimal('total_weight', 10, 2)->nullable()->after('unit_weight');
                $table->decimal('overall_mfg_weight', 10, 2)->nullable()->after('total_weight');
            });
        }

        if (!Schema::hasColumn('manufacturing_cost_sheet_expenses', 'overall_amount')) {
            Schema::table('manufacturing_cost_sheet_expenses', function (Blueprint $table) {
                $table->decimal('overall_amount', 10, 2)->nullable()->after('amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manufacturing_cost_sheets', function (Blueprint $table) {
            $table->dropColumn(['unit_weight', 'total_weight', 'overall_mfg_weight']);
        });

        Schema::table('manufacturing_cost_sheet_expenses', function (Blueprint $table) {
            $table->dropColumn('overall_amount');
        });
    }
};
