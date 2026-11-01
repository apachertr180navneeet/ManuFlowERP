<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('bno')->nullable()->after('invoice_no');
            $table->string('challan_no')->nullable()->after('bno');
            $table->string('transport')->nullable()->after('challan_no');
            $table->string('lr_no')->nullable()->after('transport');
            $table->string('purchase_status')->default('Pending')->after('total_amount');
            $table->decimal('discount', 12, 2)->default(0)->after('purchase_status');
            $table->decimal('transport_charges', 12, 2)->default(0)->after('discount');
            $table->decimal('other_charges', 12, 2)->default(0)->after('transport_charges');
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['bno', 'challan_no', 'transport', 'lr_no', 'purchase_status', 'discount', 'transport_charges', 'other_charges']);
        });
    }
};
