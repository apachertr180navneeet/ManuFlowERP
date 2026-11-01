<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'measure_in')) {
                $table->dropColumn('measure_in');
            }
            if (!Schema::hasColumn('items', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('item_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'unit_id')) {
                $table->dropColumn('unit_id');
            }
            if (!Schema::hasColumn('items', 'measure_in')) {
                $table->string('measure_in')->nullable()->after('item_type');
            }
        });
    }
};
