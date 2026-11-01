<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Manufacturing;
use App\Models\ManufacturingDetail;
use App\Models\ItemAssignment;
use App\Models\Item;
use App\Models\PurchaseBatch;
use App\Models\StockLedger;
use App\Models\PurchaseItem;
use Exception;

class ManufacturingController extends Controller
{
    protected function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        try {
            $query = Manufacturing::with('finishedItem');
            if ($companyId = $this->getCompanyId()) {
                $query->where('company_id', $companyId);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('production_no', 'like', "%{$s}%")
                      ->orWhereHas('finishedItem', function ($qv) use ($s) {
                          $qv->where('item_name', 'like', "%{$s}%");
                      });
                });
            }

            $manufacturings = $query->latest()->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.manufacturing._table', compact('manufacturings'))->render(),
                    'pagination' => view('admin.manufacturing._pagination', compact('manufacturings'))->render(),
                ]);
            }

            return view('admin.manufacturing.index', compact('manufacturings'));
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred.');
        }
    }

    public function create()
    {
        $companyId = $this->getCompanyId();
        $finishedItems = Item::forCompany()->where('item_type', 'Finished')->where('status', 'active')->orderBy('item_name')->get();
        $maxNum = Manufacturing::withTrashed()
            ->where('production_no', 'like', 'MNF-%')
            ->selectRaw('COALESCE(MAX(CAST(SUBSTRING(production_no, 5) AS UNSIGNED)), 0) as max_num')
            ->value('max_num');
        $nextProdNo = 'MNF-' . str_pad($maxNum + 1, 5, '0', STR_PAD_LEFT);

        return view('admin.manufacturing.create', compact('finishedItems', 'nextProdNo'));
    }

    public function getBom(Request $request)
    {
        try {
            $companyId = $this->getCompanyId();
            $finishedItemId = $request->finished_item_id;
            $productionQty = $request->production_qty;

            $bomItems = ItemAssignment::with('rawMaterial')
                ->where('company_id', $companyId)
                ->where('finished_item_id', $finishedItemId)
                ->where('status', 'active')
                ->get();

            if ($bomItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No BOM defined for this finished item.']);
            }

            $requirements = [];
            $stockErrors = [];

            foreach ($bomItems as $bom) {
                $requiredQty = $bom->quantity * $productionQty;
                $availableStock = PurchaseBatch::where('item_id', $bom->raw_material_id)
                    ->where('company_id', $companyId)
                    ->where('balance_qty', '>', 0)
                    ->sum('balance_qty');

                $requirements[] = [
                    'raw_material_id'   => $bom->raw_material_id,
                    'raw_material_name' => $bom->rawMaterial->item_name ?? 'Unknown',
                    'raw_material_code' => $bom->rawMaterial->short_code ?? '',
                    'consumption_per_unit' => $bom->quantity,
                    'unit_name'         => $bom->unit_name ?? '',
                    'required_qty'      => $requiredQty,
                    'available_stock'   => $availableStock,
                    'sufficient'        => $availableStock >= $requiredQty,
                ];

                if ($availableStock < $requiredQty) {
                    $stockErrors[] = "Insufficient stock for {$bom->rawMaterial->item_name}. Required: {$requiredQty} {$bom->unit_name}, Available: {$availableStock} {$bom->unit_name}.";
                }
            }

            return response()->json([
                'success' => true,
                'requirements' => $requirements,
                'stock_errors' => $stockErrors,
                'has_errors' => count($stockErrors) > 0,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred.']);
        }
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();

        $request->validate([
            'finished_item_id'   => 'required|exists:items,id',
            'production_qty'     => 'required|numeric|min:0.01',
            'production_date'    => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $bomItems = ItemAssignment::with('rawMaterial')
                ->where('company_id', $companyId)
                ->where('finished_item_id', $request->finished_item_id)
                ->where('status', 'active')
                ->get();

            if ($bomItems->isEmpty()) {
                return back()->with('error', 'No BOM defined for this finished item.')->withInput();
            }

            $requirements = [];
            foreach ($bomItems as $bom) {
                $requiredQty = $bom->quantity * $request->production_qty;
                $availableStock = PurchaseBatch::where('item_id', $bom->raw_material_id)
                    ->where('company_id', $companyId)
                    ->where('balance_qty', '>', 0)
                    ->sum('balance_qty');

                if ($availableStock < $requiredQty) {
                    DB::rollBack();
                    return back()->with('error', "Insufficient stock for {$bom->rawMaterial->item_name}. Required: {$requiredQty} {$bom->unit_name}, Available: {$availableStock} {$bom->unit_name}.")->withInput();
                }

                $requirements[] = [
                    'raw_material_id' => $bom->raw_material_id,
                    'required_qty'    => $requiredQty,
                    'unit_name'       => $bom->unit_name ?? '',
                ];
            }

            $maxNum = Manufacturing::withTrashed()
                ->where('production_no', 'like', 'MNF-%')
                ->selectRaw('COALESCE(MAX(CAST(SUBSTRING(production_no, 5) AS UNSIGNED)), 0) as max_num')
                ->value('max_num');
            $nextProdNo = 'MNF-' . str_pad($maxNum + 1, 5, '0', STR_PAD_LEFT);

            $manufacturing = Manufacturing::create([
                'company_id'       => $companyId,
                'production_no'    => $nextProdNo,
                'finished_item_id' => $request->finished_item_id,
                'production_qty'   => $request->production_qty,
                'production_date'  => $request->production_date,
                'status'           => 'completed',
                'created_by'       => Auth::id(),
            ]);

            foreach ($requirements as $req) {
                $totalConsumed = 0;

                // FIFO consumption from oldest batches
                $batches = PurchaseBatch::where('item_id', $req['raw_material_id'])
                    ->where('company_id', $companyId)
                    ->where('balance_qty', '>', 0)
                    ->orderBy('purchase_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();

                $remainingQty = $req['required_qty'];

                foreach ($batches as $batch) {
                    if ($remainingQty <= 0) break;

                    $consumeFromBatch = min($batch->balance_qty, $remainingQty);
                    $batch->consumed_qty += $consumeFromBatch;
                    $batch->balance_qty -= $consumeFromBatch;
                    $batch->save();

                    // Stock ledger: Manufacturing Out (raw material consumption)
                    $ledger = StockLedger::where('item_id', $req['raw_material_id'])
                        ->where('company_id', $companyId)
                        ->orderBy('id', 'desc')
                        ->value('balance_qty') ?? 0;

                    StockLedger::create([
                        'company_id'       => $companyId,
                        'item_id'          => $req['raw_material_id'],
                        'transaction_type' => 'Manufacturing Out',
                        'reference_id'     => $manufacturing->id,
                        'batch_id'         => $batch->id,
                        'qty_in'           => 0,
                        'qty_out'          => $consumeFromBatch,
                        'balance_qty'      => $ledger - $consumeFromBatch,
                        'transaction_date' => $request->production_date,
                    ]);

                    $totalConsumed += $consumeFromBatch;
                    $remainingQty -= $consumeFromBatch;
                }

                $manufacturing->details()->create([
                    'raw_material_id' => $req['raw_material_id'],
                    'required_qty'    => $req['required_qty'],
                    'consumed_qty'    => $totalConsumed,
                ]);
            }

            // Stock ledger: Manufacturing In (finished goods increase)
            $fgLedger = StockLedger::where('item_id', $request->finished_item_id)
                ->where('company_id', $companyId)
                ->orderBy('id', 'desc')
                ->value('balance_qty') ?? 0;

            StockLedger::create([
                'company_id'       => $companyId,
                'item_id'          => $request->finished_item_id,
                'transaction_type' => 'Manufacturing In',
                'reference_id'     => $manufacturing->id,
                'batch_id'         => null,
                'qty_in'           => $request->production_qty,
                'qty_out'          => 0,
                'balance_qty'      => $fgLedger + $request->production_qty,
                'transaction_date' => $request->production_date,
            ]);

            // PurchaseBatch entry for finished goods (so it's available for dispatch & stock report)
            PurchaseBatch::create([
                'company_id'    => $companyId,
                'item_id'       => $request->finished_item_id,
                'purchase_id'   => null,
                'purchase_date' => $request->production_date,
                'received_qty'  => $request->production_qty,
                'consumed_qty'  => 0,
                'balance_qty'   => $request->production_qty,
                'lot_no'        => $nextProdNo,
            ]);

            DB::commit();

            return redirect()->route('admin.manufacturing.index')->with('success', 'Manufacturing completed successfully!');
        } catch (\Throwable $e) {
            dd($e->getMessage());
            DB::rollBack();
            return back()->with('error', 'An error occurred.')->withInput();
        }
    }

    public function show(Manufacturing $manufacturing)
    {
        $this->authorizeAccess($manufacturing);
        $manufacturing->load('finishedItem', 'details.rawMaterial');
        return view('admin.manufacturing.show', compact('manufacturing'));
    }

    public function destroy(Manufacturing $manufacturing)
    {
        $this->authorizeAccess($manufacturing);
        try {
            DB::beginTransaction();

            // Collect ledger entries and batch data BEFORE deleting anything
            $outLedgers = StockLedger::where('reference_id', $manufacturing->id)
                ->where('transaction_type', 'Manufacturing Out')
                ->get();

            // Also get Manufacturing In to reverse finished goods
            $inLedgers = StockLedger::where('reference_id', $manufacturing->id)
                ->where('transaction_type', 'Manufacturing In')
                ->get();

            // Restore batch balances from collected ledger data
            foreach ($outLedgers as $entry) {
                if ($entry->batch_id) {
                    $batch = PurchaseBatch::find($entry->batch_id);
                    if ($batch) {
                        $batch->consumed_qty -= $entry->qty_out;
                        $batch->balance_qty += $entry->qty_out;
                        $batch->save();
                    }
                }
            }

            // Now delete stock ledger entries
            StockLedger::where('reference_id', $manufacturing->id)
                ->whereIn('transaction_type', ['Manufacturing Out', 'Manufacturing In'])
                ->delete();

            $manufacturing->details()->delete();
            $manufacturing->delete();

            DB::commit();
            return redirect()->route('admin.manufacturing.index')->with('success', 'Manufacturing record deleted successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred.');
        }
    }

    public function stockReport(Request $request)
    {
        $companyId = $this->getCompanyId();

        $items = Item::forCompany()->where('status', 'active')->orderBy('item_type')->orderBy('item_name')->get();

        $query = PurchaseBatch::with('item')
            ->where('company_id', $companyId);

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }
        if ($request->filled('item_type')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('item_type', $request->item_type);
            });
        }

        $batches = $query->orderBy('item_id')->orderBy('purchase_date')->get();

        // Item-wise stock summary
        $itemWiseStock = $batches->groupBy('item_id')->map(function ($batchGroup) {
            $item = $batchGroup->first()->item;
            return [
                'item_id'      => $batchGroup->first()->item_id,
                'item_name'    => $item->item_name ?? 'Unknown',
                'short_code'   => $item->short_code ?? '',
                'item_type'    => $item->item_type ?? '',
                'total_received' => $batchGroup->sum('received_qty'),
                'total_consumed' => $batchGroup->sum('consumed_qty'),
                'balance_qty'    => $batchGroup->sum('balance_qty'),
            ];
        })->values();

        return view('admin.manufacturing.stock_report', compact('items', 'batches', 'itemWiseStock'));
    }

    public function itemPriceReport(Request $request)
    {
        $companyId = $this->getCompanyId();

        $query = PurchaseItem::with('item', 'purchase')
            ->whereHas('purchase', function ($q) use ($companyId) {
                if ($companyId) {
                    $q->where('company_id', $companyId);
                }
                $q->where('purchase_status', 'Completed');
            });

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }
        if ($request->filled('item_type')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('item_type', $request->item_type);
            });
        }
        if ($request->filled('from_date')) {
            $query->whereHas('purchase', function ($q) use ($request) {
                $q->whereDate('purchase_date', '>=', $request->from_date);
            });
        }
        if ($request->filled('to_date')) {
            $query->whereHas('purchase', function ($q) use ($request) {
                $q->whereDate('purchase_date', '<=', $request->to_date);
            });
        }

        $purchaseItems = $query->orderBy('item_id')->orderBy('id', 'desc')->get();

        // Group by item and by month
        $priceData = [];
        $months = [];

        foreach ($purchaseItems as $pi) {
            $monthKey = $pi->purchase->purchase_date->format('Y-m');
            $monthLabel = $pi->purchase->purchase_date->format('M Y');
            $itemId = $pi->item_id;

            if (!in_array($monthKey, $months)) {
                $months[$monthKey] = $monthLabel;
            }

            if (!isset($priceData[$itemId])) {
                $priceData[$itemId] = [
                    'item_name' => $pi->item->item_name ?? $pi->item_name,
                    'item_type' => $pi->item->item_type ?? '',
                    'short_code' => $pi->item->short_code ?? '',
                    'months' => [],
                    'avg_rate' => 0,
                    'total_qty' => 0,
                ];
            }

            if (!isset($priceData[$itemId]['months'][$monthKey])) {
                $priceData[$itemId]['months'][$monthKey] = ['qty' => 0, 'total' => 0, 'rate' => 0];
            }

            $priceData[$itemId]['months'][$monthKey]['qty'] += $pi->quantity;
            $priceData[$itemId]['months'][$monthKey]['total'] += $pi->amount;
            $priceData[$itemId]['total_qty'] += $pi->quantity;
        }

        // Calculate rates per month
        foreach ($priceData as $itemId => &$data) {
            $totalAmount = 0;
            $totalQty = 0;
            foreach ($data['months'] as $mKey => &$mData) {
                $mData['rate'] = $mData['qty'] > 0 ? round($mData['total'] / $mData['qty'], 2) : 0;
                $totalAmount += $mData['total'];
                $totalQty += $mData['qty'];
            }
            $data['avg_rate'] = $totalQty > 0 ? round($totalAmount / $totalQty, 2) : 0;
        }
        unset($data);

        // Sort months chronologically
        ksort($months);

        $items = Item::forCompany()->where('status', 'active')->orderBy('item_type')->orderBy('item_name')->get();

        return view('admin.manufacturing.item_price_report', compact('priceData', 'months', 'items'));
    }

    protected function authorizeAccess(Manufacturing $manufacturing)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $manufacturing->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
