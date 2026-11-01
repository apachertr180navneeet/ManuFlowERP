<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SalesmanController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\ItemAssignmentController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\DispatchController;
use App\Http\Controllers\Admin\ManufacturingController;
use App\Http\Controllers\Admin\CostSheetController;
use App\Http\Controllers\Admin\StockReconciliationController;
use App\Http\Controllers\Web\CompanyAuthController;
use App\Http\Controllers\Web\JobWorkerController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [HomeController::class, 'index'])->name('homepage');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::name('admin.')->prefix('admin')->group(function () {
    Route::get('/', [AdminAuthController::class, 'index']);

    Route::get('login', [AdminAuthController::class, 'login'])->name('login');

    Route::post('login', [AdminAuthController::class, 'postLogin'])->name('login.post')->middleware('throttle:5,1');

    Route::middleware(['admin'])->group(function () {
    	Route::get('dashboard', [AdminAuthController::class, 'adminDashboard'])->name('dashboard');

        Route::get('change-password', [AdminAuthController::class, 'changePassword'])->name('change.password');

        Route::post('update-password', [AdminAuthController::class, 'updatePassword'])->name('update.password');

        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('profile', [AdminAuthController::class, 'adminProfile'])->name('profile');

        Route::post('profile', [AdminAuthController::class, 'updateAdminProfile'])->name('update.profile');

        Route::get('company', [CompanyController::class, 'index'])->name('company.index');
        Route::get('company/create', [CompanyController::class, 'create'])->name('company.create');
        Route::post('company', [CompanyController::class, 'store'])->name('company.store');
        Route::get('company/{company}/edit', [CompanyController::class, 'edit'])->name('company.edit');
        Route::match(['post', 'put'], 'company/{company}/update', [CompanyController::class, 'update'])->name('company.update');
        Route::post('company/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('company.toggle');

    });

});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('vendor', [VendorController::class, 'index'])->name('vendor.index');
    Route::get('vendor/create', [VendorController::class, 'create'])->name('vendor.create');
    Route::post('vendor', [VendorController::class, 'store'])->name('vendor.store');
    Route::get('vendor/{vendor}/edit', [VendorController::class, 'edit'])->name('vendor.edit');
    Route::match(['post', 'put'], 'vendor/{vendor}/update', [VendorController::class, 'update'])->name('vendor.update');
    Route::post('vendor/{vendor}/toggle-status', [VendorController::class, 'toggleStatus'])->name('vendor.toggle');
    Route::delete('vendor/{vendor}', [VendorController::class, 'destroy'])->name('vendor.destroy');
    Route::get('customer', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('customer/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('customer', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('customer/{customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::match(['post', 'put'], 'customer/{customer}/update', [CustomerController::class, 'update'])->name('customer.update');
    Route::post('customer/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customer.toggle');
    Route::delete('customer/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');
    Route::get('salesman', [SalesmanController::class, 'index'])->name('salesman.index');
    Route::get('salesman/create', [SalesmanController::class, 'create'])->name('salesman.create');
    Route::post('salesman', [SalesmanController::class, 'store'])->name('salesman.store');
    Route::get('salesman/{salesman}/edit', [SalesmanController::class, 'edit'])->name('salesman.edit');
    Route::match(['post', 'put'], 'salesman/{salesman}/update', [SalesmanController::class, 'update'])->name('salesman.update');
    Route::post('salesman/{salesman}/toggle-status', [SalesmanController::class, 'toggleStatus'])->name('salesman.toggle');
    Route::delete('salesman/{salesman}', [SalesmanController::class, 'destroy'])->name('salesman.destroy');
    Route::get('item', [ItemController::class, 'index'])->name('item.index');
    Route::get('item/create', [ItemController::class, 'create'])->name('item.create');
    Route::post('item', [ItemController::class, 'store'])->name('item.store');
    Route::get('item/{item}/edit', [ItemController::class, 'edit'])->name('item.edit');
    Route::match(['post', 'put'], 'item/{item}/update', [ItemController::class, 'update'])->name('item.update');
    Route::post('item/{item}/toggle-status', [ItemController::class, 'toggleStatus'])->name('item.toggle');
    Route::delete('item/{item}', [ItemController::class, 'destroy'])->name('item.destroy');
    Route::get('unit', [UnitController::class, 'index'])->name('unit.index');
    Route::get('unit/create', [UnitController::class, 'create'])->name('unit.create');
    Route::post('unit', [UnitController::class, 'store'])->name('unit.store');
    Route::get('unit/{unit}/edit', [UnitController::class, 'edit'])->name('unit.edit');
    Route::match(['post', 'put'], 'unit/{unit}/update', [UnitController::class, 'update'])->name('unit.update');
    Route::post('unit/{unit}/toggle-status', [UnitController::class, 'toggleStatus'])->name('unit.toggle');
    Route::delete('unit/{unit}', [UnitController::class, 'destroy'])->name('unit.destroy');
    Route::get('item-assignment', [ItemAssignmentController::class, 'index'])->name('item-assignment.index');
    Route::post('item-assignment/{itemAssignment}/toggle-status', [ItemAssignmentController::class, 'toggleStatus'])->name('item-assignment.toggle');
    Route::delete('item-assignment/{itemAssignment}', [ItemAssignmentController::class, 'destroy'])->name('item-assignment.destroy');
    Route::post('item-assignment/save-all', [ItemAssignmentController::class, 'saveAll'])->name('item-assignment.save-all');
    Route::get('item-assignment/matrix-data/{company?}', [ItemAssignmentController::class, 'getMatrixData'])->name('item-assignment.matrix');
    Route::get('purchase', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::get('purchase/create', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('purchase', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('purchase/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchase.edit');
    Route::match(['post', 'put'], 'purchase/{purchase}/update', [PurchaseController::class, 'update'])->name('purchase.update');
    Route::post('purchase/{purchase}/toggle-status', [PurchaseController::class, 'toggleStatus'])->name('purchase.toggle');
    Route::delete('purchase/{purchase}', [PurchaseController::class, 'destroy'])->name('purchase.destroy');
    Route::get('expense', [ExpenseController::class, 'index'])->name('expense.index');
    Route::get('expense/create', [ExpenseController::class, 'create'])->name('expense.create');
    Route::post('expense', [ExpenseController::class, 'store'])->name('expense.store');
    Route::get('expense/{expense}/edit', [ExpenseController::class, 'edit'])->name('expense.edit');
    Route::match(['post', 'put'], 'expense/{expense}/update', [ExpenseController::class, 'update'])->name('expense.update');
    Route::post('expense/{expense}/toggle-status', [ExpenseController::class, 'toggleStatus'])->name('expense.toggle');
    Route::delete('expense/{expense}', [ExpenseController::class, 'destroy'])->name('expense.destroy');
    Route::get('dispatch', [DispatchController::class, 'index'])->name('dispatch.index');
    Route::get('dispatch/create', [DispatchController::class, 'create'])->name('dispatch.create');
    Route::post('dispatch', [DispatchController::class, 'store'])->name('dispatch.store');
    Route::get('dispatch/{dispatchOrder}/edit', [DispatchController::class, 'edit'])->name('dispatch.edit');
    Route::match(['post', 'put'], 'dispatch/{dispatchOrder}/update', [DispatchController::class, 'update'])->name('dispatch.update');
    Route::delete('dispatch/{dispatchOrder}', [DispatchController::class, 'destroy'])->name('dispatch.destroy');
    Route::get('dispatch-reports', [DispatchController::class, 'reports'])->name('dispatch.reports');
    Route::get('manufacturing', [ManufacturingController::class, 'index'])->name('manufacturing.index');
    Route::get('manufacturing/create', [ManufacturingController::class, 'create'])->name('manufacturing.create');
    Route::post('manufacturing', [ManufacturingController::class, 'store'])->name('manufacturing.store');
    Route::post('manufacturing/get-bom', [ManufacturingController::class, 'getBom'])->name('manufacturing.get-bom');
    Route::get('manufacturing/{manufacturing}', [ManufacturingController::class, 'show'])->name('manufacturing.show');
    Route::delete('manufacturing/{manufacturing}', [ManufacturingController::class, 'destroy'])->name('manufacturing.destroy');
    Route::get('stock-report', [ManufacturingController::class, 'stockReport'])->name('manufacturing.stock');
    Route::get('item-price-report', [ManufacturingController::class, 'itemPriceReport'])->name('manufacturing.item-price');
    Route::get('cost-sheet', [CostSheetController::class, 'index'])->name('cost-sheet.index');
    Route::get('cost-sheet/create', [CostSheetController::class, 'create'])->name('cost-sheet.create');
    Route::post('cost-sheet', [CostSheetController::class, 'store'])->name('cost-sheet.store');
    Route::post('cost-sheet/get-bom', [CostSheetController::class, 'getBom'])->name('cost-sheet.get-bom');
    Route::get('cost-sheet/{costSheet}/edit', [CostSheetController::class, 'edit'])->name('cost-sheet.edit');
    Route::match(['post', 'put'], 'cost-sheet/{costSheet}/update', [CostSheetController::class, 'update'])->name('cost-sheet.update');
    Route::get('cost-sheet/{costSheet}', [CostSheetController::class, 'show'])->name('cost-sheet.show');
    Route::delete('cost-sheet/{costSheet}', [CostSheetController::class, 'destroy'])->name('cost-sheet.destroy');
    Route::post('cost-sheet/{costSheet}/toggle-status', [CostSheetController::class, 'toggleStatus'])->name('cost-sheet.toggle');
    Route::get('cost-sheet-export-excel', [CostSheetController::class, 'exportExcel'])->name('cost-sheet.export-excel');
    Route::get('cost-sheet/{costSheet}/export-pdf', [CostSheetController::class, 'exportPdf'])->name('cost-sheet.export-pdf');

    Route::get('stock-reconciliation', [StockReconciliationController::class, 'index'])->name('stock-reconciliation.index');
    Route::get('stock-reconciliation/create', [StockReconciliationController::class, 'create'])->name('stock-reconciliation.create');
    Route::post('stock-reconciliation', [StockReconciliationController::class, 'store'])->name('stock-reconciliation.store');
    Route::post('stock-reconciliation/get-item-stock', [StockReconciliationController::class, 'getItemStock'])->name('stock-reconciliation.get-item-stock');
    Route::get('stock-reconciliation/{stockReconciliation}', [StockReconciliationController::class, 'show'])->name('stock-reconciliation.show');
    Route::delete('stock-reconciliation/{stockReconciliation}', [StockReconciliationController::class, 'destroy'])->name('stock-reconciliation.destroy');
    Route::post('stock-reconciliation/{stockReconciliation}/toggle-status', [StockReconciliationController::class, 'toggleStatus'])->name('stock-reconciliation.toggle');

});

Route::name('company.')->group(function () {
    Route::get('company/login', [CompanyAuthController::class, 'login'])->name('login');
    Route::post('company/login', [CompanyAuthController::class, 'postLogin'])->name('login.post')->middleware('throttle:5,1');
});

Route::middleware(['user'])->group(function () {
    Route::get('company/dashboard', [CompanyAuthController::class, 'dashboard'])->name('company.dashboard');
    
    // Job Worker Routes
    Route::get('company/job-worker', [JobWorkerController::class, 'index'])->name('company.job-worker.index');
    Route::get('company/job-worker/create', [JobWorkerController::class, 'create'])->name('company.job-worker.create');
    Route::post('company/job-worker', [JobWorkerController::class, 'store'])->name('company.job-worker.store');
    Route::get('company/job-worker/{jobWorker}/edit', [JobWorkerController::class, 'edit'])->name('company.job-worker.edit');
    Route::match(['post', 'put'], 'company/job-worker/{jobWorker}/update', [JobWorkerController::class, 'update'])->name('company.job-worker.update');
    Route::delete('company/job-worker/{jobWorker}', [JobWorkerController::class, 'destroy'])->name('company.job-worker.destroy');
    Route::post('company/job-worker/{jobWorker}/toggle-status', [JobWorkerController::class, 'toggleStatus'])->name('company.job-worker.toggle');

    // Job Worker Assign Routes
    Route::get('company/job-worker-assign', [\App\Http\Controllers\Web\JobWorkerAssignController::class, 'index'])->name('company.job-worker-assign.index');
    Route::get('company/job-worker-assign/create', [\App\Http\Controllers\Web\JobWorkerAssignController::class, 'create'])->name('company.job-worker-assign.create');
    Route::post('company/job-worker-assign', [\App\Http\Controllers\Web\JobWorkerAssignController::class, 'store'])->name('company.job-worker-assign.store');
    Route::get('company/job-worker-assign/{jobWorkerAssign}/edit', [\App\Http\Controllers\Web\JobWorkerAssignController::class, 'edit'])->name('company.job-worker-assign.edit');
    Route::match(['post', 'put'], 'company/job-worker-assign/{jobWorkerAssign}/update', [\App\Http\Controllers\Web\JobWorkerAssignController::class, 'update'])->name('company.job-worker-assign.update');
    Route::delete('company/job-worker-assign/{jobWorkerAssign}', [\App\Http\Controllers\Web\JobWorkerAssignController::class, 'destroy'])->name('company.job-worker-assign.destroy');
    Route::post('company/job-worker-assign/{jobWorkerAssign}/toggle-status', [\App\Http\Controllers\Web\JobWorkerAssignController::class, 'toggleStatus'])->name('company.job-worker-assign.toggle');
    Route::get('company/job-worker-assign/{id}/details', [\App\Http\Controllers\Web\JobWorkInwardController::class, 'getAssignDetails'])->name('company.job-worker-assign.details');

    // Job Work Inward Routes
    Route::get('company/job-work-inward', [\App\Http\Controllers\Web\JobWorkInwardController::class, 'index'])->name('company.job-work-inward.index');
    Route::get('company/job-work-inward/create', [\App\Http\Controllers\Web\JobWorkInwardController::class, 'create'])->name('company.job-work-inward.create');
    Route::post('company/job-work-inward', [\App\Http\Controllers\Web\JobWorkInwardController::class, 'store'])->name('company.job-work-inward.store');
    Route::get('company/job-work-inward/{id}/edit', [\App\Http\Controllers\Web\JobWorkInwardController::class, 'edit'])->name('company.job-work-inward.edit');
    Route::match(['post', 'put'], 'company/job-work-inward/{id}/update', [\App\Http\Controllers\Web\JobWorkInwardController::class, 'update'])->name('company.job-work-inward.update');
    Route::delete('company/job-work-inward/{id}', [\App\Http\Controllers\Web\JobWorkInwardController::class, 'destroy'])->name('company.job-work-inward.destroy');

    Route::get('company/profile', [CompanyAuthController::class, 'profile'])->name('company.profile');
    Route::post('company/profile', [CompanyAuthController::class, 'updateProfile'])->name('company.profile.update');
    Route::get('company/change-password', [CompanyAuthController::class, 'changePassword'])->name('company.change.password');
    Route::post('company/update-password', [CompanyAuthController::class, 'updatePassword'])->name('company.password.update');
    Route::post('company/logout', [CompanyAuthController::class, 'logout'])->name('company.logout');
});


Route::get('run-migration', function () {
        try {
            Artisan::call('migrate', ['--force' => true]);
            return response(Artisan::output());
        } catch (\Throwable $e) {
            return response('Migration failed: ' . $e->getMessage(), 500);
        }
    })->name('run.migration');



