<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manufacturing_cost_sheets', function (Blueprint $table) {
            if (!Schema::hasColumn('manufacturing_cost_sheets', 'manufacturing_id')) {
                $table->foreignId('manufacturing_id')->nullable()->after('company_id')->constrained('manufacturings')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('manufacturing_cost_sheets', function (Blueprint $table) {
            if (Schema::hasColumn('manufacturing_cost_sheets', 'manufacturing_id')) {
                $table->dropForeign(['manufacturing_id']);
                $table->dropColumn('manufacturing_id');
            }
        });
    }
};
