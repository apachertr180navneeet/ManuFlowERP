<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StockReconciliation;
use App\Models\StockReconciliationItem;
use App\Models\Item;
use App\Models\PurchaseBatch;
use App\Models\StockLedger;

class StockReconciliationController extends Controller
{
    protected function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        $query = StockReconciliation::withCount('items');

        $companyId = $this->getCompanyId();
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('reference_no', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%");
            });
        }

        $reconciliations = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.stock_reconciliation._table', compact('reconciliations'))->render(),
                'pagination' => view('admin.stock_reconciliation._pagination', compact('reconciliations'))->render(),
            ]);
        }

        return view('admin.stock_reconciliation.index', compact('reconciliations'));
    }

    public function create()
    {
        $companyId = $this->getCompanyId();
        $items = Item::forCompany()->where('status', 'active')->orderBy('item_type')->orderBy('item_name')->get();
        $lastRef = StockReconciliation::withTrashed()->where('company_id', $companyId)->latest('id')->value('reference_no');
        $nextRef = $lastRef ? 'SR-' . str_pad(((int) substr($lastRef, 3)) + 1, 5, '0', STR_PAD_LEFT) : 'SR-00001';

        return view('admin.stock_reconciliation.create', compact('items', 'nextRef'));
    }

    public function getItemStock(Request $request)
    {
        try {
            $companyId = $this->getCompanyId();
            $itemId = $request->item_id;

            $systemQty = PurchaseBatch::where('item_id', $itemId)
                ->where('company_id', $companyId)
                ->sum('balance_qty');

            // Get latest FIFO rate
            $batches = PurchaseBatch::where('item_id', $itemId)
                ->where('company_id', $companyId)
                ->where('balance_qty', '>', 0)
                ->orderBy('purchase_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $rate = 0;
            if ($batches->isNotEmpty()) {
                $totalQty = $batches->sum('balance_qty');
                $totalCost = 0;
                foreach ($batches as $batch) {
                    if ($batch->purchase_id) {
                        $purchaseItem = \App\Models\PurchaseItem::where('purchase_id', $batch->purchase_id)
                            ->where('item_id', $itemId)
                            ->first();
                        $rate = $purchaseItem ? $purchaseItem->rate : 0;
                        $totalCost += $batch->balance_qty * $rate;
                    }
                }
                $rate = $totalQty > 0 ? round($totalCost / $totalQty, 2) : 0;
            }

            return response()->json([
                'success' => true,
                'system_qty' => $systemQty,
                'rate' => $rate,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred.']);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'reconciliation_date' => 'required|date',
            'reference_no'       => 'required|max:255|unique:stock_reconciliations,reference_no',
            'status'             => 'required|in:Draft,Posted',
        ]);

        $companyId = $this->getCompanyId();

        try {
            DB::beginTransaction();

            $items = json_decode($request->items_json, true) ?? [];

            if (empty($items)) {
                return back()->with('error', 'At least one item is required.')->withInput();
            }

            $reconciliation = StockReconciliation::create([
                'company_id'          => $companyId,
                'reconciliation_date' => $request->reconciliation_date,
                'reference_no'        => $request->reference_no,
                'notes'               => $request->notes,
                'status'              => $request->status,
                'created_by'          => Auth::id(),
            ]);

            foreach ($items as $item) {
                $reconciliation->items()->create([
                    'item_id'           => $item['item_id'],
                    'system_qty'        => $item['system_qty'],
                    'physical_qty'      => $item['physical_qty'],
                    'difference_qty'    => $item['difference_qty'],
                    'rate'              => $item['rate'] ?? 0,
                    'adjustment_amount' => $item['adjustment_amount'] ?? 0,
                    'remarks'           => $item['remarks'] ?? '',
                ]);
            }

            if ($request->status == 'Posted') {
                $this->postReconciliation($reconciliation, $companyId);
            }

            DB::commit();

            $msg = $request->status == 'Posted'
                ? 'Reconciliation posted and stock adjusted successfully!'
                : 'Reconciliation saved as draft.';

            return redirect()->route('admin.stock-reconciliation.index')->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred.')->withInput();
        }
    }

    protected function postReconciliation(StockReconciliation $reconciliation, $companyId)
    {
        foreach ($reconciliation->items as $item) {
            $diff = $item->difference_qty;
            if ($diff == 0) continue;

            if ($diff > 0) {
                // Excess stock found - add to system
                $batch = PurchaseBatch::create([
                    'company_id'   => $companyId,
                    'purchase_id'  => null,
                    'item_id'      => $item->item_id,
                    'received_qty' => $diff,
                    'consumed_qty' => 0,
                    'balance_qty'  => $diff,
                    'purchase_date'=> $reconciliation->reconciliation_date,
                ]);

                $lastLedger = StockLedger::where('item_id', $item->item_id)
                    ->where('company_id', $companyId)
                    ->orderBy('id', 'desc')
                    ->value('balance_qty') ?? 0;

                StockLedger::create([
                    'company_id'       => $companyId,
                    'item_id'          => $item->item_id,
                    'transaction_type' => 'Reconciliation In',
                    'reference_id'     => $reconciliation->id,
                    'batch_id'         => $batch->id,
                    'qty_in'           => $diff,
                    'qty_out'          => 0,
                    'balance_qty'      => $lastLedger + $diff,
                    'transaction_date' => $reconciliation->reconciliation_date,
                ]);
            } else {
                // Shortage - consume from FIFO batches
                $remainingQty = abs($diff);
                $batches = PurchaseBatch::where('item_id', $item->item_id)
                    ->where('company_id', $companyId)
                    ->where('balance_qty', '>', 0)
                    ->orderBy('purchase_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();

                foreach ($batches as $batch) {
                    if ($remainingQty <= 0) break;

                    $consumeQty = min($batch->balance_qty, $remainingQty);

                    $batch->consumed_qty += $consumeQty;
                    $batch->balance_qty -= $consumeQty;
                    $batch->save();

                    $lastLedger = StockLedger::where('item_id', $item->item_id)
                        ->where('company_id', $companyId)
                        ->orderBy('id', 'desc')
                        ->value('balance_qty') ?? 0;

                    StockLedger::create([
                        'company_id'       => $companyId,
                        'item_id'          => $item->item_id,
                        'transaction_type' => 'Reconciliation Out',
                        'reference_id'     => $reconciliation->id,
                        'batch_id'         => $batch->id,
                        'qty_in'           => 0,
                        'qty_out'          => $consumeQty,
                        'balance_qty'      => $lastLedger - $consumeQty,
                        'transaction_date' => $reconciliation->reconciliation_date,
                    ]);

                    $remainingQty -= $consumeQty;
                }
            }
        }

        $reconciliation->status = 'Posted';
        $reconciliation->save();
    }

    public function show(StockReconciliation $stockReconciliation)
    {
        $this->authorizeAccess($stockReconciliation);
        $stockReconciliation->load('items.item');
        return view('admin.stock_reconciliation.show', compact('stockReconciliation'));
    }

    public function destroy(StockReconciliation $stockReconciliation)
    {
        $this->authorizeAccess($stockReconciliation);
        try {
            DB::beginTransaction();

            if ($stockReconciliation->status == 'Posted') {
                $stockReconciliation->load('items');

                // Collect all reconciliation ledger data before deletion
                $allLedgers = StockLedger::where('reference_id', $stockReconciliation->id)
                    ->whereIn('transaction_type', ['Reconciliation In', 'Reconciliation Out'])
                    ->get();

                // Delete all reconciliation ledger entries
                StockLedger::where('reference_id', $stockReconciliation->id)
                    ->whereIn('transaction_type', ['Reconciliation In', 'Reconciliation Out'])
                    ->delete();

                // Restore batch balances for negative differences
                foreach ($allLedgers as $entry) {
                    if ($entry->transaction_type === 'Reconciliation Out' && $entry->batch_id) {
                        $batch = PurchaseBatch::find($entry->batch_id);
                        if ($batch) {
                            $batch->consumed_qty -= $entry->qty_out;
                            $batch->balance_qty += $entry->qty_out;
                            $batch->save();
                        }
                    }
                }

                // Remove PurchaseBatch entries created for positive differences using batch_id
                foreach ($allLedgers as $entry) {
                    if ($entry->transaction_type === 'Reconciliation In' && $entry->batch_id) {
                        PurchaseBatch::where('id', $entry->batch_id)
                            ->where('company_id', $stockReconciliation->company_id)
                            ->delete();
                    }
                }
            }

            $stockReconciliation->items()->delete();
            $stockReconciliation->delete();

            DB::commit();
            return redirect()->route('admin.stock-reconciliation.index')->with('success', 'Reconciliation deleted successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred.');
        }
    }

    public function toggleStatus(StockReconciliation $stockReconciliation)
    {
        $this->authorizeAccess($stockReconciliation);
        try {
            if ($stockReconciliation->status == 'Posted') {
                return response()->json(['success' => false, 'message' => 'Posted reconciliations cannot be toggled.']);
            }

            DB::beginTransaction();

            $companyId = $this->getCompanyId() ?? $stockReconciliation->company_id;

            $stockReconciliation->load('items');
            $this->postReconciliation($stockReconciliation, $companyId);
            DB::commit();
            return response()->json(['success' => true, 'status' => 'Posted']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred.']);
        }
    }

    protected function authorizeAccess(StockReconciliation $stockReconciliation)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $stockReconciliation->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
