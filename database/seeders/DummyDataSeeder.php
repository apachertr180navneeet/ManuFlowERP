<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Company;
use App\Models\User;
use App\Models\Unit;
use App\Models\Vendor;
use App\Models\Customer;
use App\Models\Salesman;
use App\Models\Item;
use App\Models\ItemAssignment;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseBatch;
use App\Models\StockLedger;
use App\Models\Manufacturing;
use App\Models\ManufacturingDetail;
use App\Models\CostSheet;
use App\Models\CostSheetItem;
use App\Models\CostSheetExpense;
use App\Models\DispatchOrder;
use App\Models\DispatchOrderItem;
use App\Models\Expense;
use App\Models\JobWorker;
use App\Models\JobWorkerAssign;
use App\Models\JobWorkerAssignItem;
use App\Models\JobWorkInward;
use App\Models\JobWorkInwardItem;
use App\Models\StockReconciliation;
use App\Models\StockReconciliationItem;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. COMPANIES & USERS
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        $company1 = Company::updateOrCreate(
            ['email' => 'contact@apexmanufacturing.com'],
            [
                'company_name' => 'Apex Manufacturing Industries Pvt Ltd',
                'owner_name'   => 'Rajesh Patel',
                'mobile'       => '9876543210',
                'gst_number'   => '27AABCA1234D1ZM',
                'address'      => 'Plot 42, Bhosari Industrial MIDC',
                'city'         => 'Pune',
                'state'        => 'Maharashtra',
                'pin_code'     => '411026',
                'username'     => 'apex_company',
                'password'     => Hash::make('password123'),
                'status'       => 'active',
                'created_by'   => $adminId,
            ]
        );

        $company2 = Company::updateOrCreate(
            ['email' => 'contact@novaprecision.com'],
            [
                'company_name' => 'Nova Precision Dynamics Ltd',
                'owner_name'   => 'Anil Mehta',
                'mobile'       => '9876543211',
                'gst_number'   => '24AAACN5678E1ZQ',
                'address'      => 'GIDC Phase 2, Vatva Industrial Estate',
                'city'         => 'Ahmedabad',
                'state'        => 'Gujarat',
                'pin_code'     => '382445',
                'username'     => 'nova_company',
                'password'     => Hash::make('password123'),
                'status'       => 'active',
                'created_by'   => $adminId,
            ]
        );

        // Update admin to be connected to Company 1 by default
        if ($admin) {
            $admin->update(['company_id' => $company1->id]);
        }

        // Company 1 user for login
        User::updateOrCreate(
            ['username' => 'apex_company'],
            [
                'company_id'   => $company1->id,
                'first_name'   => 'Rajesh',
                'last_name'    => 'Patel',
                'full_name'    => 'Rajesh Patel',
                'slug'         => 'rajesh-patel-apex',
                'email'        => 'rajesh@apexmanufacturing.com',
                'phone'        => '9876543210',
                'password'     => Hash::make('password123'),
                'role'         => 'user',
                'address'      => 'Plot 42, Bhosari Industrial MIDC',
                'city'         => 'Pune',
                'state'        => 'Maharashtra',
                'country'      => 'India',
                'country_code' => 91,
                'zipcode'      => '411026',
                'status'       => 'active',
            ]
        );

        // Company 2 user for login
        User::updateOrCreate(
            ['username' => 'nova_company'],
            [
                'company_id'   => $company2->id,
                'first_name'   => 'Anil',
                'last_name'    => 'Mehta',
                'full_name'    => 'Anil Mehta',
                'slug'         => 'anil-mehta-nova',
                'email'        => 'anil@novaprecision.com',
                'phone'        => '9876543211',
                'password'     => Hash::make('password123'),
                'role'         => 'user',
                'address'      => 'GIDC Phase 2, Vatva Industrial Estate',
                'city'         => 'Ahmedabad',
                'state'        => 'Gujarat',
                'country'      => 'India',
                'country_code' => 91,
                'zipcode'      => '382445',
                'status'       => 'active',
            ]
        );

        // 2. UNITS (Company 1)
        $unitPcs = Unit::updateOrCreate(
            ['company_id' => $company1->id, 'unit_name' => 'Pieces (Pcs)'],
            ['conversion_value' => 1, 'status' => 'active', 'created_by' => $adminId]
        );

        $unitKg = Unit::updateOrCreate(
            ['company_id' => $company1->id, 'unit_name' => 'Kilogram (Kg)'],
            ['conversion_value' => 1, 'status' => 'active', 'created_by' => $adminId]
        );

        $unitGm = Unit::updateOrCreate(
            ['company_id' => $company1->id, 'unit_name' => 'Gram (gm)'],
            ['parent_id' => $unitKg->id, 'conversion_value' => 1000, 'status' => 'active', 'created_by' => $adminId]
        );

        $unitMeter = Unit::updateOrCreate(
            ['company_id' => $company1->id, 'unit_name' => 'Meter (m)'],
            ['conversion_value' => 1, 'status' => 'active', 'created_by' => $adminId]
        );

        $unitMm = Unit::updateOrCreate(
            ['company_id' => $company1->id, 'unit_name' => 'Millimeter (mm)'],
            ['parent_id' => $unitMeter->id, 'conversion_value' => 1000, 'status' => 'active', 'created_by' => $adminId]
        );

        $unitLiter = Unit::updateOrCreate(
            ['company_id' => $company1->id, 'unit_name' => 'Liter (Ltr)'],
            ['conversion_value' => 1, 'status' => 'active', 'created_by' => $adminId]
        );

        $unitBox = Unit::updateOrCreate(
            ['company_id' => $company1->id, 'unit_name' => 'Box'],
            ['conversion_value' => 1, 'status' => 'active', 'created_by' => $adminId]
        );

        // 3. EXPENSES MASTER
        $expenseHeads = [
            'Factory Electricity & Power',
            'Factory Rent & Premises',
            'Machine Maintenance & Spares',
            'Employee Refreshments & Pantry',
            'Local Freight & Logistics',
            'Tooling & Consumables',
        ];
        foreach ($expenseHeads as $head) {
            Expense::updateOrCreate(
                ['company_id' => $company1->id, 'expense_name' => $head],
                ['description' => "Operational expense for {$head}", 'status' => 'active', 'created_by' => $adminId]
            );
        }

        // 4. VENDORS (Suppliers)
        $vendorTata = Vendor::updateOrCreate(
            ['company_id' => $company1->id, 'vendor_name' => 'Tata Steel Industrial Solutions'],
            [
                'firm_name'   => 'Tata Steel Ltd',
                'mobile'      => '9822112233',
                'email'       => 'orders@tatasteelindustrial.com',
                'gst_number'  => '27AAACT2727Q1ZT',
                'address'     => 'Tata Centre, 43 Jawaharlal Nehru Road',
                'city'        => 'Pune',
                'state'       => 'Maharashtra',
                'pin_code'    => '411001',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        $vendorFasteners = Vendor::updateOrCreate(
            ['company_id' => $company1->id, 'vendor_name' => 'Precision Fasteners & Screws Co'],
            [
                'firm_name'   => 'Fasteners Direct India',
                'mobile'      => '9822334455',
                'email'       => 'sales@precisionfasteners.in',
                'gst_number'  => '27AABCP5678H1ZR',
                'address'     => 'Plot 88, Ambad MIDC',
                'city'        => 'Nashik',
                'state'       => 'Maharashtra',
                'pin_code'    => '422010',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        $vendorCoatings = Vendor::updateOrCreate(
            ['company_id' => $company1->id, 'vendor_name' => 'Berger Industrial Coatings'],
            [
                'firm_name'   => 'Berger Paints India Ltd',
                'mobile'      => '9822556677',
                'email'       => 'industrial@bergercoatings.com',
                'gst_number'  => '27AABCB9012J1ZS',
                'address'     => 'Wagle Industrial Estate',
                'city'        => 'Thane',
                'state'       => 'Maharashtra',
                'pin_code'    => '400604',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        $vendorLube = Vendor::updateOrCreate(
            ['company_id' => $company1->id, 'vendor_name' => 'Bharat Petroleum Lubricants'],
            [
                'firm_name'   => 'BPCL Industrial Supply',
                'mobile'      => '9822778899',
                'email'       => 'support@bpcllubricants.com',
                'gst_number'  => '27AABCB3456K1ZT',
                'address'     => 'Mahul, Trombay',
                'city'        => 'Mumbai',
                'state'       => 'Maharashtra',
                'pin_code'    => '400074',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        // 5. CUSTOMERS (Buyers)
        $custMahindra = Customer::updateOrCreate(
            ['company_id' => $company1->id, 'customer_name' => 'Mahindra Auto Components'],
            [
                'firm_name'   => 'Mahindra & Mahindra Ltd',
                'mobile'      => '9811223344',
                'email'       => 'purchase@mahindraauto.com',
                'location'    => 'Chakan Industrial Corridor, Pune',
                'gst_number'  => '27AAACM1111A1Z1',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        $custBajaj = Customer::updateOrCreate(
            ['company_id' => $company1->id, 'customer_name' => 'Bajaj Mechanical Works'],
            [
                'firm_name'   => 'Bajaj Auto Supply Ancillary',
                'mobile'      => '9811445566',
                'email'       => 'procurement@bajajmechanical.in',
                'location'    => 'Waluj MIDC, Aurangabad',
                'gst_number'  => '27AAACB2222B1Z2',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        $custLT = Customer::updateOrCreate(
            ['company_id' => $company1->id, 'customer_name' => 'Larsen & Toubro Heavy Fabrication'],
            [
                'firm_name'   => 'L&T Heavy Engineering Division',
                'mobile'      => '9811667788',
                'email'       => 'materials@ltheavy.com',
                'location'    => 'Powai Works, Mumbai',
                'gst_number'  => '27AAACL3333C1Z3',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        // 6. SALESMEN
        $salesman1 = Salesman::updateOrCreate(
            ['company_id' => $company1->id, 'salesman_name' => 'Rahul Sharma'],
            [
                'mobile'       => '9822011223',
                'email'        => 'rahul.sharma@apexmanufacturing.com',
                'joining_date' => '2025-01-15',
                'address'      => 'Kothrud',
                'city'         => 'Pune',
                'state'        => 'Maharashtra',
                'pin_code'     => '411038',
                'status'       => 'active',
                'created_by'   => $adminId,
            ]
        );

        $salesman2 = Salesman::updateOrCreate(
            ['company_id' => $company1->id, 'salesman_name' => 'Pooja Deshmukh'],
            [
                'mobile'       => '9822044556',
                'email'        => 'pooja.deshmukh@apexmanufacturing.com',
                'joining_date' => '2025-03-01',
                'address'      => 'Andheri East',
                'city'         => 'Mumbai',
                'state'        => 'Maharashtra',
                'pin_code'     => '400069',
                'status'       => 'active',
                'created_by'   => $adminId,
            ]
        );

        // 7. ITEMS (Raw Materials & Finished Goods)
        // Raw Materials
        $rmSteel = Item::updateOrCreate(
            ['company_id' => $company1->id, 'short_code' => 'RM-CRS-2MM'],
            [
                'item_name'   => 'Cold Rolled Steel Sheet 2mm',
                'item_type'   => 'Raw Material',
                'unit_id'     => $unitKg->id,
                'size'        => '2mm x 4ft x 8ft',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        $rmRod = Item::updateOrCreate(
            ['company_id' => $company1->id, 'short_code' => 'RM-SS-12MM'],
            [
                'item_name'   => 'Stainless Steel Rod 12mm',
                'item_type'   => 'Raw Material',
                'unit_id'     => $unitMeter->id,
                'size'        => '12mm x 3m',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        $rmBolt = Item::updateOrCreate(
            ['company_id' => $company1->id, 'short_code' => 'RM-BLT-M8'],
            [
                'item_name'   => 'Industrial Hex Bolt M8',
                'item_type'   => 'Raw Material',
                'unit_id'     => $unitPcs->id,
                'size'        => 'M8 x 25mm',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        $rmPowder = Item::updateOrCreate(
            ['company_id' => $company1->id, 'short_code' => 'RM-PDR-BLK'],
            [
                'item_name'   => 'Epoxy Powder Coating Black',
                'item_type'   => 'Raw Material',
                'unit_id'     => $unitKg->id,
                'size'        => '25kg bag',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        $rmOil = Item::updateOrCreate(
            ['company_id' => $company1->id, 'short_code' => 'RM-OIL-46'],
            [
                'item_name'   => 'Hydraulic Fluid ISO 46',
                'item_type'   => 'Raw Material',
                'unit_id'     => $unitLiter->id,
                'size'        => '20L can',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        $rmBox = Item::updateOrCreate(
            ['company_id' => $company1->id, 'short_code' => 'RM-BOX-L'],
            [
                'item_name'   => 'Corrugated Packaging Box L',
                'item_type'   => 'Raw Material',
                'unit_id'     => $unitBox->id,
                'size'        => '40x30x20 cm',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        // Finished Goods
        $fgBracket = Item::updateOrCreate(
            ['company_id' => $company1->id, 'short_code' => 'FG-BRK-200'],
            [
                'item_name'   => 'Heavy Duty Mounting Bracket 200mm',
                'item_type'   => 'Finished',
                'unit_id'     => $unitPcs->id,
                'size'        => '200x150x50 mm',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        $fgShaft = Item::updateOrCreate(
            ['company_id' => $company1->id, 'short_code' => 'FG-SFT-250'],
            [
                'item_name'   => 'Precision Steel Shaft 12x250',
                'item_type'   => 'Finished',
                'unit_id'     => $unitPcs->id,
                'size'        => 'Dia 12mm x 250mm',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        $fgEnclosure = Item::updateOrCreate(
            ['company_id' => $company1->id, 'short_code' => 'FG-ENC-MOD'],
            [
                'item_name'   => 'Modular Control Panel Enclosure',
                'item_type'   => 'Finished',
                'unit_id'     => $unitPcs->id,
                'size'        => '400x300x150 mm',
                'status'      => 'active',
                'created_by'  => $adminId,
            ]
        );

        // 8. BILL OF MATERIALS (BOM) / ITEM ASSIGNMENTS
        // Bracket BOM
        ItemAssignment::updateOrCreate(
            ['company_id' => $company1->id, 'finished_item_id' => $fgBracket->id, 'raw_material_id' => $rmSteel->id],
            ['quantity' => 1.80, 'unit_name' => $unitKg->unit_name, 'status' => 'active', 'created_by' => $adminId]
        );
        ItemAssignment::updateOrCreate(
            ['company_id' => $company1->id, 'finished_item_id' => $fgBracket->id, 'raw_material_id' => $rmBolt->id],
            ['quantity' => 4.00, 'unit_name' => $unitPcs->unit_name, 'status' => 'active', 'created_by' => $adminId]
        );
        ItemAssignment::updateOrCreate(
            ['company_id' => $company1->id, 'finished_item_id' => $fgBracket->id, 'raw_material_id' => $rmPowder->id],
            ['quantity' => 0.15, 'unit_name' => $unitKg->unit_name, 'status' => 'active', 'created_by' => $adminId]
        );

        // Shaft BOM
        ItemAssignment::updateOrCreate(
            ['company_id' => $company1->id, 'finished_item_id' => $fgShaft->id, 'raw_material_id' => $rmRod->id],
            ['quantity' => 0.30, 'unit_name' => $unitMeter->unit_name, 'status' => 'active', 'created_by' => $adminId]
        );
        ItemAssignment::updateOrCreate(
            ['company_id' => $company1->id, 'finished_item_id' => $fgShaft->id, 'raw_material_id' => $rmOil->id],
            ['quantity' => 0.05, 'unit_name' => $unitLiter->unit_name, 'status' => 'active', 'created_by' => $adminId]
        );

        // Enclosure BOM
        ItemAssignment::updateOrCreate(
            ['company_id' => $company1->id, 'finished_item_id' => $fgEnclosure->id, 'raw_material_id' => $rmSteel->id],
            ['quantity' => 6.50, 'unit_name' => $unitKg->unit_name, 'status' => 'active', 'created_by' => $adminId]
        );
        ItemAssignment::updateOrCreate(
            ['company_id' => $company1->id, 'finished_item_id' => $fgEnclosure->id, 'raw_material_id' => $rmBolt->id],
            ['quantity' => 12.00, 'unit_name' => $unitPcs->unit_name, 'status' => 'active', 'created_by' => $adminId]
        );
        ItemAssignment::updateOrCreate(
            ['company_id' => $company1->id, 'finished_item_id' => $fgEnclosure->id, 'raw_material_id' => $rmPowder->id],
            ['quantity' => 0.80, 'unit_name' => $unitKg->unit_name, 'status' => 'active', 'created_by' => $adminId]
        );
        ItemAssignment::updateOrCreate(
            ['company_id' => $company1->id, 'finished_item_id' => $fgEnclosure->id, 'raw_material_id' => $rmBox->id],
            ['quantity' => 1.00, 'unit_name' => $unitBox->unit_name, 'status' => 'active', 'created_by' => $adminId]
        );

        // 9. PURCHASES, BATCHES & STOCK LEDGER
        // Purchase 1: Steel Sheets & Rods from Tata Steel
        $purchase1 = Purchase::updateOrCreate(
            ['company_id' => $company1->id, 'invoice_no' => 'TATA-INV-2026-001'],
            [
                'vendor_id'         => $vendorTata->id,
                'purchase_date'     => '2026-01-10',
                'bno'               => 'BNO-1001',
                'challan_no'        => 'CH-9001',
                'transport'         => 'VRL Logistics',
                'lr_no'             => 'LR-VRL-5541',
                'subtotal'          => 56500.00,
                'tax_amount'        => 10170.00,
                'total_amount'      => 66670.00,
                'purchase_status'   => 'Completed',
                'discount'          => 0,
                'transport_charges' => 1200.00,
                'other_charges'     => 0,
                'notes'             => 'Bulk raw material intake batch 1',
                'status'            => 'active',
                'created_by'        => $adminId,
            ]
        );

        PurchaseItem::updateOrCreate(
            ['purchase_id' => $purchase1->id, 'item_id' => $rmSteel->id],
            [
                'item_name'  => $rmSteel->item_name,
                'unit'       => $unitKg->unit_name,
                'quantity'   => 500.00,
                'rate'       => 65.00,
                'amount'     => 32500.00,
                'created_by' => $adminId,
            ]
        );

        PurchaseItem::updateOrCreate(
            ['purchase_id' => $purchase1->id, 'item_id' => $rmRod->id],
            [
                'item_name'  => $rmRod->item_name,
                'unit'       => $unitMeter->unit_name,
                'quantity'   => 100.00,
                'rate'       => 240.00,
                'amount'     => 24000.00,
                'created_by' => $adminId,
            ]
        );

        $batchSteel1 = PurchaseBatch::updateOrCreate(
            ['purchase_id' => $purchase1->id, 'item_id' => $rmSteel->id],
            [
                'company_id'   => $company1->id,
                'received_qty' => 500.00,
                'consumed_qty' => 220.00, // will be consumed in production
                'balance_qty'  => 280.00,
                'purchase_date'=> '2026-01-10',
            ]
        );

        $batchRod1 = PurchaseBatch::updateOrCreate(
            ['purchase_id' => $purchase1->id, 'item_id' => $rmRod->id],
            [
                'company_id'   => $company1->id,
                'received_qty' => 100.00,
                'consumed_qty' => 30.00,
                'balance_qty'  => 70.00,
                'purchase_date'=> '2026-01-10',
            ]
        );

        StockLedger::updateOrCreate(
            ['company_id' => $company1->id, 'reference_id' => $purchase1->id, 'item_id' => $rmSteel->id, 'transaction_type' => 'Purchase'],
            [
                'batch_id'         => $batchSteel1->id,
                'qty_in'           => 500.00,
                'qty_out'          => 0,
                'balance_qty'      => 500.00,
                'transaction_date' => '2026-01-10',
            ]
        );

        StockLedger::updateOrCreate(
            ['company_id' => $company1->id, 'reference_id' => $purchase1->id, 'item_id' => $rmRod->id, 'transaction_type' => 'Purchase'],
            [
                'batch_id'         => $batchRod1->id,
                'qty_in'           => 100.00,
                'qty_out'          => 0,
                'balance_qty'      => 100.00,
                'transaction_date' => '2026-01-10',
            ]
        );

        // Purchase 2: Bolts from Precision Fasteners
        $purchase2 = Purchase::updateOrCreate(
            ['company_id' => $company1->id, 'invoice_no' => 'PF-INV-2026-088'],
            [
                'vendor_id'         => $vendorFasteners->id,
                'purchase_date'     => '2026-01-12',
                'bno'               => 'BNO-1002',
                'challan_no'        => 'CH-9002',
                'transport'         => 'Navata Transport',
                'lr_no'             => 'LR-NAV-8812',
                'subtotal'          => 9000.00,
                'tax_amount'        => 1620.00,
                'total_amount'      => 10620.00,
                'purchase_status'   => 'Completed',
                'discount'          => 0,
                'transport_charges' => 350.00,
                'other_charges'     => 0,
                'notes'             => 'Hardware procurement',
                'status'            => 'active',
                'created_by'        => $adminId,
            ]
        );

        PurchaseItem::updateOrCreate(
            ['purchase_id' => $purchase2->id, 'item_id' => $rmBolt->id],
            [
                'item_name'  => $rmBolt->item_name,
                'unit'       => $unitPcs->unit_name,
                'quantity'   => 2000.00,
                'rate'       => 4.50,
                'amount'     => 9000.00,
                'created_by' => $adminId,
            ]
        );

        $batchBolt1 = PurchaseBatch::updateOrCreate(
            ['purchase_id' => $purchase2->id, 'item_id' => $rmBolt->id],
            [
                'company_id'   => $company1->id,
                'received_qty' => 2000.00,
                'consumed_qty' => 440.00,
                'balance_qty'  => 1560.00,
                'purchase_date'=> '2026-01-12',
            ]
        );

        StockLedger::updateOrCreate(
            ['company_id' => $company1->id, 'reference_id' => $purchase2->id, 'item_id' => $rmBolt->id, 'transaction_type' => 'Purchase'],
            [
                'batch_id'         => $batchBolt1->id,
                'qty_in'           => 2000.00,
                'qty_out'          => 0,
                'balance_qty'      => 2000.00,
                'transaction_date' => '2026-01-12',
            ]
        );

        // Purchase 3: Powder Coating & Oil
        $purchase3 = Purchase::updateOrCreate(
            ['company_id' => $company1->id, 'invoice_no' => 'BRG-INV-2026-302'],
            [
                'vendor_id'         => $vendorCoatings->id,
                'purchase_date'     => '2026-01-15',
                'bno'               => 'BNO-1003',
                'challan_no'        => 'CH-9003',
                'transport'         => 'Gati Cargo',
                'lr_no'             => 'LR-GAT-9910',
                'subtotal'          => 29000.00,
                'tax_amount'        => 5220.00,
                'total_amount'      => 34220.00,
                'purchase_status'   => 'Completed',
                'discount'          => 0,
                'transport_charges' => 500.00,
                'other_charges'     => 0,
                'notes'             => 'Chemicals & surface finish materials',
                'status'            => 'active',
                'created_by'        => $adminId,
            ]
        );

        PurchaseItem::updateOrCreate(
            ['purchase_id' => $purchase3->id, 'item_id' => $rmPowder->id],
            [
                'item_name'  => $rmPowder->item_name,
                'unit'       => $unitKg->unit_name,
                'quantity'   => 100.00,
                'rate'       => 180.00,
                'amount'     => 18000.00,
                'created_by' => $adminId,
            ]
        );

        PurchaseItem::updateOrCreate(
            ['purchase_id' => $purchase3->id, 'item_id' => $rmOil->id],
            [
                'item_name'  => $rmOil->item_name,
                'unit'       => $unitLiter->unit_name,
                'quantity'   => 50.00,
                'rate'       => 220.00,
                'amount'     => 11000.00,
                'created_by' => $adminId,
            ]
        );

        $batchPowder1 = PurchaseBatch::updateOrCreate(
            ['purchase_id' => $purchase3->id, 'item_id' => $rmPowder->id],
            [
                'company_id'   => $company1->id,
                'received_qty' => 100.00,
                'consumed_qty' => 23.50,
                'balance_qty'  => 76.50,
                'purchase_date'=> '2026-01-15',
            ]
        );

        $batchOil1 = PurchaseBatch::updateOrCreate(
            ['purchase_id' => $purchase3->id, 'item_id' => $rmOil->id],
            [
                'company_id'   => $company1->id,
                'received_qty' => 50.00,
                'consumed_qty' => 5.00,
                'balance_qty'  => 45.00,
                'purchase_date'=> '2026-01-15',
            ]
        );

        StockLedger::updateOrCreate(
            ['company_id' => $company1->id, 'reference_id' => $purchase3->id, 'item_id' => $rmPowder->id, 'transaction_type' => 'Purchase'],
            [
                'batch_id'         => $batchPowder1->id,
                'qty_in'           => 100.00,
                'qty_out'          => 0,
                'balance_qty'      => 100.00,
                'transaction_date' => '2026-01-15',
            ]
        );

        StockLedger::updateOrCreate(
            ['company_id' => $company1->id, 'reference_id' => $purchase3->id, 'item_id' => $rmOil->id, 'transaction_type' => 'Purchase'],
            [
                'batch_id'         => $batchOil1->id,
                'qty_in'           => 50.00,
                'qty_out'          => 0,
                'balance_qty'      => 50.00,
                'transaction_date' => '2026-01-15',
            ]
        );

        // Purchase 4: Packaging Boxes
        $purchase4 = Purchase::updateOrCreate(
            ['company_id' => $company1->id, 'invoice_no' => 'BOX-INV-2026-019'],
            [
                'vendor_id'         => $vendorTata->id,
                'purchase_date'     => '2026-01-18',
                'bno'               => 'BNO-1004',
                'challan_no'        => 'CH-9004',
                'transport'         => 'Local Tempo',
                'lr_no'             => 'LR-LOC-1011',
                'subtotal'          => 10500.00,
                'tax_amount'        => 1890.00,
                'total_amount'      => 12390.00,
                'purchase_status'   => 'Completed',
                'discount'          => 0,
                'transport_charges' => 200.00,
                'other_charges'     => 0,
                'notes'             => 'Corrugated shipping boxes',
                'status'            => 'active',
                'created_by'        => $adminId,
            ]
        );

        PurchaseItem::updateOrCreate(
            ['purchase_id' => $purchase4->id, 'item_id' => $rmBox->id],
            [
                'item_name'  => $rmBox->item_name,
                'unit'       => $unitBox->unit_name,
                'quantity'   => 300.00,
                'rate'       => 35.00,
                'amount'     => 10500.00,
                'created_by' => $adminId,
            ]
        );

        $batchBox1 = PurchaseBatch::updateOrCreate(
            ['purchase_id' => $purchase4->id, 'item_id' => $rmBox->id],
            [
                'company_id'   => $company1->id,
                'received_qty' => 300.00,
                'consumed_qty' => 20.00,
                'balance_qty'  => 280.00,
                'purchase_date'=> '2026-01-18',
            ]
        );

        StockLedger::updateOrCreate(
            ['company_id' => $company1->id, 'reference_id' => $purchase4->id, 'item_id' => $rmBox->id, 'transaction_type' => 'Purchase'],
            [
                'batch_id'         => $batchBox1->id,
                'qty_in'           => 300.00,
                'qty_out'          => 0,
                'balance_qty'      => 300.00,
                'transaction_date' => '2026-01-18',
            ]
        );

        // 10. MANUFACTURING (PRODUCTION RUNS)
        // Manufacturing 1: 50 Pcs Mounting Brackets
        $mfg1 = Manufacturing::updateOrCreate(
            ['company_id' => $company1->id, 'production_no' => 'MNF-00001'],
            [
                'finished_item_id' => $fgBracket->id,
                'production_qty'   => 50.00,
                'production_date'  => '2026-01-22',
                'status'           => 'completed',
                'created_by'       => $adminId,
            ]
        );

        ManufacturingDetail::updateOrCreate(
            ['manufacturing_id' => $mfg1->id, 'raw_material_id' => $rmSteel->id],
            ['required_qty' => 90.00, 'consumed_qty' => 90.00]
        );
        ManufacturingDetail::updateOrCreate(
            ['manufacturing_id' => $mfg1->id, 'raw_material_id' => $rmBolt->id],
            ['required_qty' => 200.00, 'consumed_qty' => 200.00]
        );
        ManufacturingDetail::updateOrCreate(
            ['manufacturing_id' => $mfg1->id, 'raw_material_id' => $rmPowder->id],
            ['required_qty' => 7.50, 'consumed_qty' => 7.50]
        );

        // Finished item stock ledger IN
        StockLedger::updateOrCreate(
            ['company_id' => $company1->id, 'reference_id' => $mfg1->id, 'item_id' => $fgBracket->id, 'transaction_type' => 'Manufacturing In'],
            [
                'qty_in'           => 50.00,
                'qty_out'          => 0,
                'balance_qty'      => 50.00,
                'transaction_date' => '2026-01-22',
            ]
        );

        // Manufacturing 2: 100 Pcs Precision Steel Shafts
        $mfg2 = Manufacturing::updateOrCreate(
            ['company_id' => $company1->id, 'production_no' => 'MNF-00002'],
            [
                'finished_item_id' => $fgShaft->id,
                'production_qty'   => 100.00,
                'production_date'  => '2026-01-25',
                'status'           => 'completed',
                'created_by'       => $adminId,
            ]
        );

        ManufacturingDetail::updateOrCreate(
            ['manufacturing_id' => $mfg2->id, 'raw_material_id' => $rmRod->id],
            ['required_qty' => 30.00, 'consumed_qty' => 30.00]
        );
        ManufacturingDetail::updateOrCreate(
            ['manufacturing_id' => $mfg2->id, 'raw_material_id' => $rmOil->id],
            ['required_qty' => 5.00, 'consumed_qty' => 5.00]
        );

        StockLedger::updateOrCreate(
            ['company_id' => $company1->id, 'reference_id' => $mfg2->id, 'item_id' => $fgShaft->id, 'transaction_type' => 'Manufacturing In'],
            [
                'qty_in'           => 100.00,
                'qty_out'          => 0,
                'balance_qty'      => 100.00,
                'transaction_date' => '2026-01-25',
            ]
        );

        // Manufacturing 3: 20 Pcs Modular Control Panel Enclosure
        $mfg3 = Manufacturing::updateOrCreate(
            ['company_id' => $company1->id, 'production_no' => 'MNF-00003'],
            [
                'finished_item_id' => $fgEnclosure->id,
                'production_qty'   => 20.00,
                'production_date'  => '2026-01-28',
                'status'           => 'completed',
                'created_by'       => $adminId,
            ]
        );

        ManufacturingDetail::updateOrCreate(
            ['manufacturing_id' => $mfg3->id, 'raw_material_id' => $rmSteel->id],
            ['required_qty' => 130.00, 'consumed_qty' => 130.00]
        );
        ManufacturingDetail::updateOrCreate(
            ['manufacturing_id' => $mfg3->id, 'raw_material_id' => $rmBolt->id],
            ['required_qty' => 240.00, 'consumed_qty' => 240.00]
        );
        ManufacturingDetail::updateOrCreate(
            ['manufacturing_id' => $mfg3->id, 'raw_material_id' => $rmPowder->id],
            ['required_qty' => 16.00, 'consumed_qty' => 16.00]
        );
        ManufacturingDetail::updateOrCreate(
            ['manufacturing_id' => $mfg3->id, 'raw_material_id' => $rmBox->id],
            ['required_qty' => 20.00, 'consumed_qty' => 20.00]
        );

        StockLedger::updateOrCreate(
            ['company_id' => $company1->id, 'reference_id' => $mfg3->id, 'item_id' => $fgEnclosure->id, 'transaction_type' => 'Manufacturing In'],
            [
                'qty_in'           => 20.00,
                'qty_out'          => 0,
                'balance_qty'      => 20.00,
                'transaction_date' => '2026-01-28',
            ]
        );

        // 11. COST SHEETS
        $costSheet1 = CostSheet::updateOrCreate(
            ['company_id' => $company1->id, 'bom_no' => 'CS-BOM-001'],
            [
                'manufacturing_id'   => $mfg1->id,
                'date'               => '2026-01-22',
                'product_id'         => $fgBracket->id,
                'qty'                => 50.00,
                'unit_weight'        => 1.95,
                'total_weight'       => 97.50,
                'overall_mfg_weight' => 100.00,
                'raw_material_cost'  => 8100.00,
                'expense_cost'       => 2400.00,
                'total_cost'         => 10500.00,
                'profit_percent'     => 25.00,
                'profit_amount'      => 2625.00,
                'selling_price'      => 13125.00,
                'unit_price'         => 262.50,
                'status'             => 'Approved',
                'created_by'         => $adminId,
            ]
        );

        CostSheetItem::updateOrCreate(
            ['cost_sheet_id' => $costSheet1->id, 'raw_material_id' => $rmSteel->id],
            [
                'required_qty' => 90.00,
                'unit_name'    => $unitKg->unit_name,
                'fifo_rate'    => 65.00,
                'amount'       => 5850.00,
            ]
        );
        CostSheetItem::updateOrCreate(
            ['cost_sheet_id' => $costSheet1->id, 'raw_material_id' => $rmBolt->id],
            [
                'required_qty' => 200.00,
                'unit_name'    => $unitPcs->unit_name,
                'fifo_rate'    => 4.50,
                'amount'       => 900.00,
            ]
        );
        CostSheetItem::updateOrCreate(
            ['cost_sheet_id' => $costSheet1->id, 'raw_material_id' => $rmPowder->id],
            [
                'required_qty' => 7.50,
                'unit_name'    => $unitKg->unit_name,
                'fifo_rate'    => 180.00,
                'amount'       => 1350.00,
            ]
        );

        CostSheetExpense::updateOrCreate(
            ['cost_sheet_id' => $costSheet1->id, 'expense_name' => 'Labor Charges'],
            ['amount' => 1500.00, 'overall_amount' => 1500.00]
        );
        CostSheetExpense::updateOrCreate(
            ['cost_sheet_id' => $costSheet1->id, 'expense_name' => 'Machine Electricity'],
            ['amount' => 900.00, 'overall_amount' => 900.00]
        );

        // 12. DISPATCH ORDERS (Sales to customers)
        // Dispatch 1: 30 Pcs Brackets to Mahindra Auto
        $disp1 = DispatchOrder::updateOrCreate(
            ['company_id' => $company1->id, 'challan_no' => 'DISP-CH-001'],
            [
                'dispatch_date'   => '2026-02-01',
                'customer_id'     => $custMahindra->id,
                'customer_mobile' => $custMahindra->mobile,
                'transport_name'  => 'Mahindra Dedicated Fleet',
                'dispatch_status' => 'Delivered',
                'total_amount'    => 10500.00,
                'status'          => 'active',
                'created_by'      => $adminId,
            ]
        );

        DispatchOrderItem::updateOrCreate(
            ['dispatch_order_id' => $disp1->id, 'item_id' => $fgBracket->id],
            [
                'lot_no' => 'LOT-BRK-2601',
                'qty'    => 30.00,
                'weight' => 58.50,
                'rate'   => 350.00,
                'amount' => 10500.00,
            ]
        );

        StockLedger::updateOrCreate(
            ['company_id' => $company1->id, 'reference_id' => $disp1->id, 'item_id' => $fgBracket->id, 'transaction_type' => 'Dispatch'],
            [
                'qty_in'           => 0,
                'qty_out'          => 30.00,
                'balance_qty'      => 20.00, // 50 in - 30 out
                'transaction_date' => '2026-02-01',
            ]
        );

        // Dispatch 2: 60 Pcs Shafts to Bajaj Mechanical
        $disp2 = DispatchOrder::updateOrCreate(
            ['company_id' => $company1->id, 'challan_no' => 'DISP-CH-002'],
            [
                'dispatch_date'   => '2026-02-03',
                'customer_id'     => $custBajaj->id,
                'customer_mobile' => $custBajaj->mobile,
                'transport_name'  => 'Speedways Freight',
                'dispatch_status' => 'Delivered',
                'total_amount'    => 19200.00,
                'status'          => 'active',
                'created_by'      => $adminId,
            ]
        );

        DispatchOrderItem::updateOrCreate(
            ['dispatch_order_id' => $disp2->id, 'item_id' => $fgShaft->id],
            [
                'lot_no' => 'LOT-SFT-2601',
                'qty'    => 60.00,
                'weight' => 45.00,
                'rate'   => 320.00,
                'amount' => 19200.00,
            ]
        );

        StockLedger::updateOrCreate(
            ['company_id' => $company1->id, 'reference_id' => $disp2->id, 'item_id' => $fgShaft->id, 'transaction_type' => 'Dispatch'],
            [
                'qty_in'           => 0,
                'qty_out'          => 60.00,
                'balance_qty'      => 40.00, // 100 in - 60 out
                'transaction_date' => '2026-02-03',
            ]
        );

        // 13. JOB WORKERS & SUBCONTRACTING
        $jw1 = JobWorker::updateOrCreate(
            ['company_id' => $company1->id, 'email' => 'contact@saisurface.com'],
            [
                'name'     => 'Sai Surface Treatments',
                'phone'    => '9833445566',
                'address'  => 'Sector 7, Bhosari Industrial Area',
                'city'     => 'Pune',
                'state'    => 'Maharashtra',
                'pin_code' => '411026',
                'status'   => 'active',
            ]
        );

        $jw2 = JobWorker::updateOrCreate(
            ['company_id' => $company1->id, 'email' => 'admin@shreemachining.in'],
            [
                'name'     => 'Shree Precision Machining Hub',
                'phone'    => '9833778899',
                'address'  => 'Chakan Phase 2',
                'city'     => 'Pune',
                'state'    => 'Maharashtra',
                'pin_code' => '410501',
                'status'   => 'active',
            ]
        );

        // Job worker assignment
        $jwAssign = JobWorkerAssign::updateOrCreate(
            ['company_id' => $company1->id, 'assign_no' => 'JW-ASN-00001'],
            [
                'date'           => '2026-02-05',
                'job_worker_id'  => $jw1->id,
                'contact_number' => $jw1->phone,
                'transport_name' => 'Internal Factory Van',
                'status'         => 'active',
            ]
        );

        $jwAssignItem = JobWorkerAssignItem::updateOrCreate(
            ['job_worker_assign_id' => $jwAssign->id, 'item_id' => $fgBracket->id],
            [
                'qty'    => 20.00,
                'unit'   => $unitPcs->unit_name,
                'rate'   => 45.00,
                'amount' => 900.00,
            ]
        );

        // Job work inward
        $jwInward = JobWorkInward::updateOrCreate(
            ['company_id' => $company1->id, 'inward_no' => 'JW-INW-00001'],
            [
                'job_worker_assign_id' => $jwAssign->id,
                'inward_date'          => '2026-02-10',
                'challan_no'           => 'SST-CH-441',
                'transport_name'       => 'Internal Factory Van',
                'lr_number'            => 'LR-INT-01',
                'remark'               => 'Galvanizing and quality check completed successfully',
                'subtotal'             => 900.00,
                'other_charges'        => 50.00,
                'discount'             => 0,
                'transport_charges'    => 0,
                'total_amount'         => 950.00,
                'status'               => 'completed',
            ]
        );

        JobWorkInwardItem::updateOrCreate(
            ['job_work_inward_id' => $jwInward->id, 'item_id' => $fgBracket->id],
            [
                'assigned_qty'  => 20.00,
                'prev_rec_qty'  => 0,
                'remaining_qty' => 0,
                'qty'           => 20.00,
                'unit'          => $unitPcs->unit_name,
                'rate'          => 45.00,
                'amount'        => 900.00,
            ]
        );

        // 14. STOCK RECONCILIATION
        $recon = StockReconciliation::updateOrCreate(
            ['company_id' => $company1->id, 'reference_no' => 'SR-2026-001'],
            [
                'reconciliation_date' => '2026-02-15',
                'notes'               => 'Monthly physical stock audit for Q1',
                'status'              => 'Approved',
                'created_by'          => $adminId,
            ]
        );

        StockReconciliationItem::updateOrCreate(
            ['reconciliation_id' => $recon->id, 'item_id' => $rmSteel->id],
            [
                'system_qty'        => 280.00,
                'physical_qty'      => 280.00,
                'difference_qty'    => 0,
                'rate'              => 65.00,
                'adjustment_amount' => 0,
                'remarks'           => 'Physical count perfectly matches system balance',
            ]
        );

        StockReconciliationItem::updateOrCreate(
            ['reconciliation_id' => $recon->id, 'item_id' => $rmBolt->id],
            [
                'system_qty'        => 1560.00,
                'physical_qty'      => 1555.00,
                'difference_qty'    => -5.00,
                'rate'              => 4.50,
                'adjustment_amount' => -22.50,
                'remarks'           => 'Minor handling loss accounted',
            ]
        );
    }
}

