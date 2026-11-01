<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->date('reconciliation_date');
            $table->string('reference_no');
            $table->text('notes')->nullable();
            $table->string('status')->default('Draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_reconciliation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reconciliation_id')->constrained('stock_reconciliations')->cascadeOnDelete();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->decimal('system_qty', 10, 2)->default(0);
            $table->decimal('physical_qty', 10, 2)->default(0);
            $table->decimal('difference_qty', 10, 2)->default(0);
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('adjustment_amount', 12, 2)->default(0);
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_reconciliation_items');
        Schema::dropIfExists('stock_reconciliations');
    }
};
