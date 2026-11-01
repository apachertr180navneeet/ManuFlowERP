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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('job_work_inward_items');
        Schema::dropIfExists('job_work_inwards');
        Schema::enableForeignKeyConstraints();

        Schema::create('job_work_inwards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('job_worker_assign_id');
            $table->date('inward_date');
            $table->string('inward_no');
            $table->string('challan_no')->nullable();
            $table->string('transport_name')->nullable();
            $table->string('lr_number')->nullable();
            $table->text('remark')->nullable();
            
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('other_charges', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('transport_charges', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            
            $table->string('status')->default('Completed');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('job_worker_assign_id')->references('id')->on('job_worker_assigns')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_work_inwards');
    }
};
