<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manufacturing_cost_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('bom_no')->unique();
            $table->date('date');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->decimal('qty', 10, 2)->default(0);
            $table->decimal('raw_material_cost', 12, 2)->default(0);
            $table->decimal('expense_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->decimal('profit_percent', 5, 2)->default(0);
            $table->decimal('profit_amount', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->string('status')->default('Draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('manufacturing_cost_sheet_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_sheet_id')->constrained('manufacturing_cost_sheets')->cascadeOnDelete();
            $table->unsignedBigInteger('raw_material_id')->nullable();
            $table->decimal('required_qty', 10, 2)->default(0);
            $table->string('unit_name')->nullable();
            $table->decimal('fifo_rate', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('manufacturing_cost_sheet_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_sheet_id')->constrained('manufacturing_cost_sheets')->cascadeOnDelete();
            $table->string('expense_name');
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manufacturing_cost_sheet_expenses');
        Schema::dropIfExists('manufacturing_cost_sheet_items');
        Schema::dropIfExists('manufacturing_cost_sheets');
    }
};
