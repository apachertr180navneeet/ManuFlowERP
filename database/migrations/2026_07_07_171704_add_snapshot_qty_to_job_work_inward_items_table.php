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
        Schema::table('job_work_inward_items', function (Blueprint $table) {
            $table->decimal('assigned_qty', 15, 2)->default(0)->after('item_id');
            $table->decimal('prev_rec_qty', 15, 2)->default(0)->after('assigned_qty');
            $table->decimal('remaining_qty', 15, 2)->default(0)->after('prev_rec_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_work_inward_items', function (Blueprint $table) {
            $table->dropColumn(['assigned_qty', 'prev_rec_qty', 'remaining_qty']);
        });
    }
};
