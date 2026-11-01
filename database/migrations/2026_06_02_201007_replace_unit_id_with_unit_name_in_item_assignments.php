<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_assignments', function (Blueprint $table) {
            $table->dropColumn('unit_id');
            $table->string('unit_name', 100)->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('item_assignments', function (Blueprint $table) {
            $table->dropColumn('unit_name');
            $table->unsignedBigInteger('unit_id')->nullable()->after('quantity');
        });
    }
};
