<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->date('dispatch_date');
            $table->string('challan_no')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_mobile')->nullable();
            $table->string('transport_name');
            $table->string('dispatch_status')->default('Pending');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('status')->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('dispatch_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_order_id')->constrained()->cascadeOnDelete();
            $table->string('lot_no')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->decimal('qty', 10, 2)->default(0);
            $table->decimal('weight', 10, 2)->default(0);
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_order_items');
        Schema::dropIfExists('dispatch_orders');
    }
};
