<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->decimal('received_qty', 10, 2)->default(0);
            $table->decimal('consumed_qty', 10, 2)->default(0);
            $table->decimal('balance_qty', 10, 2)->default(0);
            $table->date('purchase_date');
            $table->timestamps();
        });

        Schema::create('manufacturings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('production_no')->unique();
            $table->unsignedBigInteger('finished_item_id')->nullable();
            $table->decimal('production_qty', 10, 2)->default(0);
            $table->date('production_date');
            $table->string('status')->default('completed');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('manufacturing_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturing_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('raw_material_id')->nullable();
            $table->decimal('required_qty', 10, 2)->default(0);
            $table->decimal('consumed_qty', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('stock_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('transaction_type'); // Purchase / Manufacturing In / Manufacturing Out
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->decimal('qty_in', 10, 2)->default(0);
            $table->decimal('qty_out', 10, 2)->default(0);
            $table->decimal('balance_qty', 10, 2)->default(0);
            $table->date('transaction_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ledgers');
        Schema::dropIfExists('manufacturing_details');
        Schema::dropIfExists('manufacturings');
        Schema::dropIfExists('purchase_batches');
    }
};
