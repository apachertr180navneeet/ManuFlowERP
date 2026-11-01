<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CostSheet;
use App\Models\CostSheetExpense;
use App\Models\ItemAssignment;
use App\Models\Item;
use App\Models\PurchaseBatch;
use App\Models\StockLedger;
use App\Models\Manufacturing;
use App\Models\Expense;
use App\Exports\CostSheetExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class CostSheetController extends Controller
{
    protected function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        try {
            $query = CostSheet::with('product');
            if ($companyId = $this->getCompanyId()) {
                $query->where('company_id', $companyId);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('bom_no', 'like', "%{$s}%")
                      ->orWhereHas('product', function ($qv) use ($s) {
                          $qv->where('item_name', 'like', "%{$s}%");
                      });
                });
            }

            if ($request->filled('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            $costSheets = $query->latest()->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.cost_sheet._table', compact('costSheets'))->render(),
                    'pagination' => view('admin.cost_sheet._pagination', compact('costSheets'))->render(),
                ]);
            }

            return view('admin.cost_sheet.index', compact('costSheets'));
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred.');
        }
    }

    public function create()
    {
        $companyId = $this->getCompanyId();
        $finishedItems = Item::forCompany()->where('item_type', 'Finished')->where('status', 'active')->orderBy('item_name')->get();
        $linkedIds = CostSheet::where('company_id', $companyId)->whereNotNull('manufacturing_id')->pluck('manufacturing_id');
        $manufacturings = Manufacturing::with('finishedItem')->where('company_id', $companyId)->whereNotIn('id', $linkedIds)->latest()->get();
        $expenses = Expense::forCompany()->where('status', 'active')->orderBy('expense_name')->get();
        $lastBom = CostSheet::withTrashed()->where('company_id', $companyId)->latest('id')->value('bom_no');
        $nextBomNo = $lastBom ? 'CS-' . str_pad(((int) substr($lastBom, 3)) + 1, 5, '0', STR_PAD_LEFT) : 'CS-00001';

        return view('admin.cost_sheet.create', compact('finishedItems', 'manufacturings', 'expenses', 'nextBomNo'));
    }

    public function getBom(Request $request)
    {
        try {
            $companyId = $this->getCompanyId();

            $productId = $request->product_id;
            $qty = $request->qty;
            $bomItems = collect();

            if ($request->manufacturing_id) {
                $manufacturing = Manufacturing::with('details.rawMaterial.unit')->find($request->manufacturing_id);
                if ($manufacturing) {
                    $productId = $manufacturing->finished_item_id;
                    $qty = $manufacturing->production_qty;

                    if ($manufacturing->details->isNotEmpty()) {
                        $bomItems = $manufacturing->details->map(function ($detail) {
                            $itemAssignment = new \stdClass();
                            $itemAssignment->raw_material_id = $detail->raw_material_id;
                            $itemAssignment->rawMaterial = $detail->rawMaterial;
                            $itemAssignment->quantity = $detail->required_qty;
                            $itemAssignment->unit_name = $detail->rawMaterial->unit->unit_name ?? '';
                            return $itemAssignment;
                        });
                    }
                }
            }

            if ($bomItems->isEmpty()) {
                $bomItems = ItemAssignment::with('rawMaterial.unit')
                    ->where('company_id', $companyId)
                    ->where('finished_item_id', $productId)
                    ->where('status', 'active')
                    ->get();
            }

            if ($bomItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No BOM defined for this product.']);
            }

            $unitName = '';
            $items = [];
            $stockErrors = [];

            foreach ($bomItems as $bom) {
                $requiredQty = $bom->quantity * $qty;
                $unitName = $bom->unit_name ?? '';

                // Get FIFO batches
                $batches = PurchaseBatch::where('item_id', $bom->raw_material_id)
                    ->where('company_id', $companyId)
                    ->where('balance_qty', '>', 0)
                    ->orderBy('purchase_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();

                $availableStock = $batches->sum('balance_qty');
                $fifoRate = 0;

                // Calculate FIFO weighted average rate
                $remaining = $requiredQty;
                $totalCost = 0;
                $totalConsumed = 0;

                foreach ($batches as $batch) {
                    if ($remaining <= 0) break;
                    $consume = min($batch->balance_qty, $remaining);
                    $rate = 0;
                    if ($batch->purchase_id) {
                        $purchaseItem = \App\Models\PurchaseItem::where('purchase_id', $batch->purchase_id)
                            ->where('item_id', $bom->raw_material_id)
                            ->first();
                        $rate = $purchaseItem ? $purchaseItem->rate : 0;
                    }
                    $totalCost += $consume * $rate;
                    $totalConsumed += $consume;
                    $remaining -= $consume;
                }

                $fifoRate = $totalConsumed > 0 ? round($totalCost / $totalConsumed, 2) : 0;

                // Fallback to latest purchase rate if no stock available
                if ($fifoRate == 0) {
                    $latestPurchaseItem = \App\Models\PurchaseItem::where('item_id', $bom->raw_material_id)
                        ->whereHas('purchase', function ($q) use ($companyId) {
                            $q->where('company_id', $companyId);
                        })
                        ->latest('id')
                        ->first();
                    $fifoRate = $latestPurchaseItem ? (float) $latestPurchaseItem->rate : 0;
                }

                $items[] = [
                    'raw_material_id'   => $bom->raw_material_id,
                    'raw_material_name' => $bom->rawMaterial->item_name ?? 'Unknown',
                    'bom_qty'           => $bom->quantity,
                    'required_qty'      => $requiredQty,
                    'unit_name'         => $unitName,
                    'fifo_rate'         => $fifoRate,
                    'amount'            => round($requiredQty * $fifoRate, 2),
                    'available_stock'   => $availableStock,
                    'sufficient'        => $availableStock >= $requiredQty,
                ];

                if ($availableStock < $requiredQty) {
                    $stockErrors[] = "Insufficient stock available for {$bom->rawMaterial->item_name}. Required: {$requiredQty} {$unitName}, Available: {$availableStock} {$unitName}.";
                }
            }

            return response()->json([
                'success' => true,
                'items' => $items,
                'stock_errors' => $stockErrors,
                'has_errors' => count($stockErrors) > 0,
                'unit_name' => $unitName,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred.']);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'bom_no'          => 'required|max:255|unique:manufacturing_cost_sheets,bom_no',
            'date'            => 'required|date',
            'product_id'      => 'required|exists:items,id',
            'qty'             => 'required|numeric|min:0.01',
            'profit_percent'  => 'required|numeric|min:0',
            'status'          => 'required|in:Draft,Final',
            'manufacturing_id' => 'nullable|exists:manufacturings,id',
        ]);

        $companyId = $this->getCompanyId();

        try {
            DB::beginTransaction();

            // Validate BOM and stock
            $bomItems = ItemAssignment::with('rawMaterial')
                ->where('company_id', $companyId)
                ->where('finished_item_id', $request->product_id)
                ->where('status', 'active')
                ->get();

            if ($bomItems->isEmpty()) {
                return back()->with('error', 'No BOM defined for this product.')->withInput();
            }

            $rawMaterialItems = json_decode($request->items_json, true) ?? [];
            $expenses = json_decode($request->expenses_json, true) ?? [];

            if (empty($rawMaterialItems)) {
                return back()->with('error', 'At least one raw material item is required.')->withInput();
            }

            // Calculate costs
            $rawMaterialCost = array_sum(array_column($rawMaterialItems, 'amount'));
            $expenseCost = array_sum(array_column($expenses, 'amount'));
            $totalCost = $rawMaterialCost + $expenseCost;
            $profitPercent = $request->profit_percent;
            $profitAmount = round($totalCost * $profitPercent / 100, 2);
            $sellingPrice = $totalCost + $profitAmount;
            $unitPrice = $request->qty > 0 ? round($sellingPrice / $request->qty, 2) : 0;
            $unitWeight = $request->input('unit_weight', 0);
            $overallMfgWeight = $request->input('overall_mfg_weight', 0);
            $totalWeight = $unitWeight * $request->qty;

            $costSheet = CostSheet::create([
                'company_id'       => $companyId,
                'manufacturing_id' => $request->manufacturing_id,
                'bom_no'           => $request->bom_no,
                'date'             => $request->date,
                'product_id'       => $request->product_id,
                'qty'              => $request->qty,
                'unit_weight'      => $unitWeight,
                'total_weight'     => $totalWeight,
                'overall_mfg_weight' => $overallMfgWeight,
                'raw_material_cost' => $rawMaterialCost,
                'expense_cost'     => $expenseCost,
                'total_cost'       => $totalCost,
                'profit_percent'   => $profitPercent,
                'profit_amount'    => $profitAmount,
                'selling_price'    => $sellingPrice,
                'unit_price'       => $unitPrice,
                'status'           => $request->status,
                'created_by'       => Auth::id(),
            ]);

            // Save raw material items
            foreach ($rawMaterialItems as $item) {
                $costSheet->items()->create([
                    'raw_material_id' => $item['raw_material_id'],
                    'required_qty'    => $item['required_qty'],
                    'unit_name'       => $item['unit_name'] ?? '',
                    'fifo_rate'       => $item['fifo_rate'] ?? 0,
                    'amount'          => $item['amount'] ?? 0,
                ]);
            }

            // Save expenses
            foreach ($expenses as $exp) {
                if (!empty($exp['expense_name']) && ($exp['amount'] ?? 0) > 0) {
                    $costSheet->expenses()->create([
                        'expense_name'   => $exp['expense_name'],
                        'amount'         => $exp['amount'],
                        'overall_amount' => $exp['overall_amount'] ?? null,
                    ]);
                }
            }

            // If Final, deduct stock using FIFO
            if ($request->status == 'Final') {
                foreach ($rawMaterialItems as $item) {
                    $remainingQty = $item['required_qty'];

                    $batches = PurchaseBatch::where('item_id', $item['raw_material_id'])
                        ->where('company_id', $companyId)
                        ->where('balance_qty', '>', 0)
                        ->orderBy('purchase_date', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();

                    foreach ($batches as $batch) {
                        if ($remainingQty <= 0) break;

                        $consumeQty = min($batch->balance_qty, $remainingQty);

                        // Get rate from purchase item
                        $rate = 0;
                        if ($batch->purchase_id) {
                            $purchaseItem = \App\Models\PurchaseItem::where('purchase_id', $batch->purchase_id)
                                ->where('item_id', $item['raw_material_id'])
                                ->first();
                            $rate = $purchaseItem ? $purchaseItem->rate : 0;
                        }

                        $batch->consumed_qty += $consumeQty;
                        $batch->balance_qty -= $consumeQty;
                        $batch->save();

                        // Stock ledger entry
                        $ledger = StockLedger::where('item_id', $item['raw_material_id'])
                            ->where('company_id', $companyId)
                            ->orderBy('id', 'desc')
                            ->value('balance_qty') ?? 0;

                        StockLedger::create([
                            'company_id'       => $companyId,
                            'item_id'          => $item['raw_material_id'],
                            'transaction_type' => 'Manufacturing Out',
                            'reference_id'     => $costSheet->id,
                            'batch_id'         => $batch->id,
                            'qty_in'           => 0,
                            'qty_out'          => $consumeQty,
                            'balance_qty'      => $ledger - $consumeQty,
                            'transaction_date' => $request->date,
                        ]);

                        $remainingQty -= $consumeQty;
                    }
                }

                // Finished goods stock in
                $fgLedger = StockLedger::where('item_id', $request->product_id)
                    ->where('company_id', $companyId)
                    ->orderBy('id', 'desc')
                    ->value('balance_qty') ?? 0;

                StockLedger::create([
                    'company_id'       => $companyId,
                    'item_id'          => $request->product_id,
                    'transaction_type' => 'Manufacturing In',
                    'reference_id'     => $costSheet->id,
                    'batch_id'         => null,
                    'qty_in'           => $request->qty,
                    'qty_out'          => 0,
                    'balance_qty'      => $fgLedger + $request->qty,
                    'transaction_date' => $request->date,
                ]);
            }

            DB::commit();

            $msg = $request->status == 'Final'
                ? 'Cost sheet saved and stock deducted successfully!'
                : 'Cost sheet saved as draft.';

            return redirect()->route('admin.cost-sheet.index')->with('success', $msg);
        } catch (\Throwable $e) {
            dd($e);
            DB::rollBack();
            \Log::error('CostSheet Store Error: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            return back()->with('error', 'An error occurred. ' . $e->getMessage())->withInput();
        }
    }

    public function edit(CostSheet $costSheet)
    {
        $this->authorizeAccess($costSheet);

        if ($costSheet->status == 'Final') {
            return redirect()->route('admin.cost-sheet.index')->with('error', 'Final cost sheets cannot be edited.');
        }

        $costSheet->load('items.rawMaterial', 'expenses');

        $companyId = $this->getCompanyId() ?? $costSheet->company_id;
        $finishedItems = Item::forCompany()->where('item_type', 'Finished')->where('status', 'active')->orderBy('item_name')->get();
        $linkedIds = CostSheet::where('company_id', $companyId)->whereNotNull('manufacturing_id')->where('id', '!=', $costSheet->id)->pluck('manufacturing_id');
        $manufacturings = Manufacturing::with('finishedItem')->where('company_id', $companyId)->whereNotIn('id', $linkedIds)->latest()->get();
        $expenses = Expense::forCompany()->where('status', 'active')->orderBy('expense_name')->get();

        return view('admin.cost_sheet.edit', compact('costSheet', 'finishedItems', 'manufacturings', 'expenses'));
    }

    public function update(Request $request, CostSheet $costSheet)
    {
        $this->authorizeAccess($costSheet);

        if ($costSheet->status == 'Final') {
            return redirect()->route('admin.cost-sheet.index')->with('error', 'Final cost sheets cannot be edited.');
        }

        $request->validate([
            'date'            => 'required|date',
            'product_id'      => 'required|exists:items,id',
            'qty'             => 'required|numeric|min:0.01',
            'profit_percent'  => 'required|numeric|min:0',
            'status'          => 'required|in:Draft,Final',
            'manufacturing_id' => 'nullable|exists:manufacturings,id',
        ]);

        $companyId = $this->getCompanyId() ?? $costSheet->company_id;

        try {
            DB::beginTransaction();

            $rawMaterialItems = json_decode($request->items_json, true) ?? [];
            $expenses = json_decode($request->expenses_json, true) ?? [];

            if (empty($rawMaterialItems)) {
                return back()->with('error', 'At least one raw material item is required.')->withInput();
            }

            $rawMaterialCost = array_sum(array_column($rawMaterialItems, 'amount'));
            $expenseCost = array_sum(array_column($expenses, 'amount'));
            $totalCost = $rawMaterialCost + $expenseCost;
            $profitPercent = $request->profit_percent;
            $profitAmount = round($totalCost * $profitPercent / 100, 2);
            $sellingPrice = $totalCost + $profitAmount;
            $unitPrice = $request->qty > 0 ? round($sellingPrice / $request->qty, 2) : 0;

            $unitWeight = $request->input('unit_weight', 0);
            $overallMfgWeight = $request->input('overall_mfg_weight', 0);
            $totalWeight = $unitWeight * $request->qty;

            $costSheet->update([
                'manufacturing_id' => $request->manufacturing_id,
                'date'             => $request->date,
                'product_id'       => $request->product_id,
                'qty'              => $request->qty,
                'unit_weight'      => $unitWeight,
                'total_weight'     => $totalWeight,
                'overall_mfg_weight' => $overallMfgWeight,
                'raw_material_cost' => $rawMaterialCost,
                'expense_cost'     => $expenseCost,
                'total_cost'       => $totalCost,
                'profit_percent'   => $profitPercent,
                'profit_amount'    => $profitAmount,
                'selling_price'    => $sellingPrice,
                'unit_price'       => $unitPrice,
                'status'           => $request->status,
            ]);

            // Replace items
            $costSheet->items()->delete();
            foreach ($rawMaterialItems as $item) {
                $costSheet->items()->create([
                    'raw_material_id' => $item['raw_material_id'],
                    'required_qty'    => $item['required_qty'],
                    'unit_name'       => $item['unit_name'] ?? '',
                    'fifo_rate'       => $item['fifo_rate'] ?? 0,
                    'amount'          => $item['amount'] ?? 0,
                ]);
            }

            // Replace expenses
            $costSheet->expenses()->delete();
            foreach ($expenses as $exp) {
                if (!empty($exp['expense_name']) && ($exp['amount'] ?? 0) > 0) {
                    $costSheet->expenses()->create([
                        'expense_name'   => $exp['expense_name'],
                        'amount'         => $exp['amount'],
                        'overall_amount' => $exp['overall_amount'] ?? null,
                    ]);
                }
            }

            // If Final, deduct stock using FIFO
            if ($request->status == 'Final') {
                foreach ($rawMaterialItems as $item) {
                    $remainingQty = $item['required_qty'];
                    $batches = PurchaseBatch::where('item_id', $item['raw_material_id'])
                        ->where('company_id', $companyId)
                        ->where('balance_qty', '>', 0)
                        ->orderBy('purchase_date', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();

                    foreach ($batches as $batch) {
                        if ($remainingQty <= 0) break;
                        $consumeQty = min($batch->balance_qty, $remainingQty);

                        $rate = 0;
                        if ($batch->purchase_id) {
                            $purchaseItem = \App\Models\PurchaseItem::where('purchase_id', $batch->purchase_id)
                                ->where('item_id', $item['raw_material_id'])
                                ->first();
                            $rate = $purchaseItem ? $purchaseItem->rate : 0;
                        }

                        $batch->consumed_qty += $consumeQty;
                        $batch->balance_qty -= $consumeQty;
                        $batch->save();

                        $ledger = StockLedger::where('item_id', $item['raw_material_id'])
                            ->where('company_id', $companyId)
                            ->orderBy('id', 'desc')
                            ->value('balance_qty') ?? 0;

                        StockLedger::create([
                            'company_id'       => $companyId,
                            'item_id'          => $item['raw_material_id'],
                            'transaction_type' => 'Manufacturing Out',
                            'reference_id'     => $costSheet->id,
                            'batch_id'         => $batch->id,
                            'qty_in'           => 0,
                            'qty_out'          => $consumeQty,
                            'balance_qty'      => $ledger - $consumeQty,
                            'transaction_date' => $request->date,
                        ]);

                        $remainingQty -= $consumeQty;
                    }
                }

                $fgLedger = StockLedger::where('item_id', $request->product_id)
                    ->where('company_id', $companyId)
                    ->orderBy('id', 'desc')
                    ->value('balance_qty') ?? 0;

                StockLedger::create([
                    'company_id'       => $companyId,
                    'item_id'          => $request->product_id,
                    'transaction_type' => 'Manufacturing In',
                    'reference_id'     => $costSheet->id,
                    'batch_id'         => null,
                    'qty_in'           => $request->qty,
                    'qty_out'          => 0,
                    'balance_qty'      => $fgLedger + $request->qty,
                    'transaction_date' => $request->date,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.cost-sheet.index')->with('success', 'Cost sheet updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred.')->withInput();
        }
    }

    public function destroy(CostSheet $costSheet)
    {
        $this->authorizeAccess($costSheet);
        try {
            DB::beginTransaction();

            if ($costSheet->status == 'Final') {
                // Collect ledger data before deletion for batch restoration
                $manufacturingOutLedgers = StockLedger::where('reference_id', $costSheet->id)
                    ->where('transaction_type', 'Manufacturing Out')
                    ->get();

                // Delete manufacturing ledger entries
                StockLedger::where('reference_id', $costSheet->id)
                    ->whereIn('transaction_type', ['Manufacturing Out', 'Manufacturing In'])
                    ->delete();

                // Restore batch balances from collected data
                foreach ($manufacturingOutLedgers as $entry) {
                    if ($entry->batch_id) {
                        $batch = PurchaseBatch::find($entry->batch_id);
                        if ($batch) {
                            $batch->consumed_qty -= $entry->qty_out;
                            $batch->balance_qty += $entry->qty_out;
                            $batch->save();
                        }
                    }
                }
            }

            $costSheet->items()->delete();
            $costSheet->expenses()->delete();
            $costSheet->delete();

            DB::commit();
            return redirect()->route('admin.cost-sheet.index')->with('success', 'Cost sheet deleted successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred.');
        }
    }

    public function toggleStatus(CostSheet $costSheet)
    {
        $this->authorizeAccess($costSheet);
        try {
            DB::beginTransaction();

            $newStatus = $costSheet->status === 'Draft' ? 'Final' : 'Draft';

            if ($newStatus == 'Final') {
                $companyId = $this->getCompanyId() ?? $costSheet->company_id;
                $items = $costSheet->items;

                foreach ($items as $item) {
                    $batches = PurchaseBatch::where('item_id', $item->raw_material_id)
                        ->where('company_id', $companyId)
                        ->where('balance_qty', '>', 0)
                        ->orderBy('purchase_date', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();

                    $remainingQty = $item->required_qty;
                    foreach ($batches as $batch) {
                        if ($remainingQty <= 0) break;
                        $consumeQty = min($batch->balance_qty, $remainingQty);

                        $rate = 0;
                        if ($batch->purchase_id) {
                            $purchaseItem = \App\Models\PurchaseItem::where('purchase_id', $batch->purchase_id)
                                ->where('item_id', $item->raw_material_id)
                                ->first();
                            $rate = $purchaseItem ? $purchaseItem->rate : 0;
                        }

                        $batch->consumed_qty += $consumeQty;
                        $batch->balance_qty -= $consumeQty;
                        $batch->save();

                        $ledger = StockLedger::where('item_id', $item->raw_material_id)
                            ->where('company_id', $companyId)
                            ->orderBy('id', 'desc')
                            ->value('balance_qty') ?? 0;

                        StockLedger::create([
                            'company_id'       => $companyId,
                            'item_id'          => $item->raw_material_id,
                            'transaction_type' => 'Manufacturing Out',
                            'reference_id'     => $costSheet->id,
                            'batch_id'         => $batch->id,
                            'qty_in'           => 0,
                            'qty_out'          => $consumeQty,
                            'balance_qty'      => $ledger - $consumeQty,
                            'transaction_date' => $costSheet->date,
                        ]);

                        $remainingQty -= $consumeQty;
                    }
                }

                // FG stock in
                $fgLedger = StockLedger::where('item_id', $costSheet->product_id)
                    ->where('company_id', $companyId)
                    ->orderBy('id', 'desc')
                    ->value('balance_qty') ?? 0;

                StockLedger::create([
                    'company_id'       => $companyId,
                    'item_id'          => $costSheet->product_id,
                    'transaction_type' => 'Manufacturing In',
                    'reference_id'     => $costSheet->id,
                    'batch_id'         => null,
                    'qty_in'           => $costSheet->qty,
                    'qty_out'          => 0,
                    'balance_qty'      => $fgLedger + $costSheet->qty,
                    'transaction_date' => $costSheet->date,
                ]);
            } elseif ($newStatus == 'Draft') {
                // Reverse stock deduction
                $manufacturingOutLedgers = StockLedger::where('reference_id', $costSheet->id)
                    ->where('transaction_type', 'Manufacturing Out')
                    ->get();

                StockLedger::where('reference_id', $costSheet->id)
                    ->whereIn('transaction_type', ['Manufacturing Out', 'Manufacturing In'])
                    ->delete();

                foreach ($manufacturingOutLedgers as $entry) {
                    if ($entry->batch_id) {
                        $batch = PurchaseBatch::find($entry->batch_id);
                        if ($batch) {
                            $batch->consumed_qty -= $entry->qty_out;
                            $batch->balance_qty += $entry->qty_out;
                            $batch->save();
                        }
                    }
                }
            }

            $costSheet->status = $newStatus;
            $costSheet->save();

            DB::commit();
            return response()->json(['success' => true, 'status' => $costSheet->status]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred.']);
        }
    }

    public function show(CostSheet $costSheet)
    {
        $this->authorizeAccess($costSheet);
        $costSheet->load('product', 'items.rawMaterial', 'expenses');
        return view('admin.cost_sheet.show', compact('costSheet'));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new CostSheetExport($request), 'cost-sheets-' . date('d-m-Y') . '.xlsx');
    }

    public function exportPdf(CostSheet $costSheet)
    {
        $this->authorizeAccess($costSheet);
        $costSheet->load('product', 'items.rawMaterial', 'expenses');

        $pdf = Pdf::loadView('admin.cost_sheet.pdf', compact('costSheet'));
        return $pdf->download('cost-sheet-' . $costSheet->bom_no . '.pdf');
    }

    protected function authorizeAccess(CostSheet $costSheet)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $costSheet->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
