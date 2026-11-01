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
        Schema::create('job_work_inward_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_work_inward_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('qty', 15, 2);
            $table->string('unit')->nullable();
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('job_work_inward_id')->references('id')->on('job_work_inwards')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_work_inward_items');
    }
};
