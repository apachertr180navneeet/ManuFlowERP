<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('units', 'sub_unit')) {
                $columns[] = 'sub_unit';
            }
            if (Schema::hasColumn('units', 'subunit_value')) {
                $columns[] = 'subunit_value';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('units', function (Blueprint $table) {
            if (!Schema::hasColumn('units', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('company_id')->constrained('units')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('units', 'conversion_value')) {
                $table->decimal('conversion_value', 12, 2)->nullable()->after('unit_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            if (Schema::hasColumn('units', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn(['parent_id', 'conversion_value']);
            }
        });

        Schema::table('units', function (Blueprint $table) {
            if (!Schema::hasColumn('units', 'sub_unit')) {
                $table->string('sub_unit')->nullable()->after('unit_name');
            }
            if (!Schema::hasColumn('units', 'subunit_value')) {
                $table->decimal('subunit_value', 12, 2)->nullable()->after('sub_unit');
            }
        });
    }
};
