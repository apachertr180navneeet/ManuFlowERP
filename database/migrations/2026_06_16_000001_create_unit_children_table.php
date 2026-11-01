<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->string('child_name');
            $table->decimal('conversion_value', 12, 2);
            $table->timestamps();
        });

        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['sub_unit', 'subunit_value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_children');

        Schema::table('units', function (Blueprint $table) {
            $table->string('sub_unit')->nullable()->after('unit_name');
            $table->decimal('subunit_value', 12, 2)->nullable()->after('sub_unit');
        });
    }
};
