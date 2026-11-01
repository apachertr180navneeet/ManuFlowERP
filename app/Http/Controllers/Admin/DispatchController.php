<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DispatchOrder;
use App\Models\DispatchOrderItem;
use App\Models\Customer;
use App\Models\Item;
use App\Models\PurchaseBatch;
use App\Models\StockLedger;

class DispatchController extends Controller
{
    protected function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        try {
            $query = DispatchOrder::with('customer');
            if ($companyId = $this->getCompanyId()) {
                $query->where('company_id', $companyId);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('challan_no', 'like', "%{$s}%")
                      ->orWhere('transport_name', 'like', "%{$s}%")
                      ->orWhereHas('customer', function ($qv) use ($s) {
                          $qv->where('customer_name', 'like', "%{$s}%");
                      });
                });
            }

            if ($request->filled('status')) {
                $query->where('dispatch_status', $request->status);
            }

            $dispatchOrders = $query->latest()->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.dispatch._table', compact('dispatchOrders'))->render(),
                    'pagination' => view('admin.dispatch._pagination', compact('dispatchOrders'))->render(),
                ]);
            }

            return view('admin.dispatch.index', compact('dispatchOrders'));
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred.');
        }
    }

    public function create()
    {
        $companyId = $this->getCompanyId();
        $customers = Customer::forCompany($companyId)->where('status', 'active')->orderBy('customer_name')->get();
        $items = Item::forCompany()->with('unit')->where('status', 'active')->orderBy('item_name')->get();

        $latestPrices = \App\Models\CostSheet::where('company_id', $companyId)
            ->where('status', 'Final')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('product_id')
            ->map(function($group) {
                return $group->first()->unit_price;
            });

        foreach ($items as $item) {
            $item->latest_price = $latestPrices[$item->id] ?? 0;
        }
        $lastChallan = DispatchOrder::withTrashed()
            ->where('company_id', $companyId)
            ->where('challan_no', 'like', 'CH-%')
            ->selectRaw('MAX(CAST(SUBSTRING(challan_no, 4) AS UNSIGNED)) as max_num')
            ->value('max_num');
        $nextChallan = $lastChallan ? 'CH-' . str_pad($lastChallan + 1, 5, '0', STR_PAD_LEFT) : 'CH-00001';

        return view('admin.dispatch.create', compact('customers', 'items', 'nextChallan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dispatch_date'      => 'required|date',
            'challan_no'         => 'required|max:255|unique:dispatch_orders,challan_no',
            'customer_id'        => 'required|exists:customers,id',
            'customer_mobile'    => 'required',
            'transport_name'     => 'required|max:255',
            'total_amount'       => 'required|numeric|min:0',
            'items'              => 'required|array|min:1',
            'items.*.item_id'    => 'required|exists:items,id',
            'items.*.qty'        => 'required|numeric|min:0.01',
            'items.*.rate'       => 'required|numeric|min:0',
            'items.*.amount'     => 'required|numeric|min:0',
        ]);

        $companyId = $this->getCompanyId();

        // FIFO stock check before processing
        $errors = [];
        foreach ($request->items as $index => $item) {
            $batchDetails = [];
            $availableQty = $this->getFifoAvailableQty($companyId, $item['item_id'], $batchDetails);

            if ($availableQty < (float) $item['qty']) {
                $itemName = Item::find($item['item_id'])->item_name ?? "Item #{$item['item_id']}";
                $errors["items.$index.qty"] = "Insufficient stock for {$itemName}. Available: {$availableQty}, Requested: {$item['qty']}";
            }
        }

        if (!empty($errors)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => $errors], 422);
            }
            return back()->withErrors($errors)->withInput();
        }

        try {
            DB::beginTransaction();

            $dispatchOrder = DispatchOrder::create([
                'company_id'      => $companyId,
                'dispatch_date'   => $request->dispatch_date,
                'challan_no'      => $request->challan_no,
                'customer_id'     => $request->customer_id,
                'customer_mobile' => $request->customer_mobile,
                'transport_name'  => $request->transport_name,
                'dispatch_status' => 'Pending',
                'total_amount'    => $request->total_amount,
                'status'          => 'active',
                'created_by'      => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                $dispatchOrder->items()->create([
                    'item_id' => $item['item_id'],
                    'qty'     => $item['qty'],
                    'rate'    => $item['rate'],
                    'amount'  => $item['amount'],
                ]);

                $this->deductStockFifo($companyId, $item['item_id'], $item['qty'], $dispatchOrder->id, $request->dispatch_date);
            }

            DB::commit();

            return redirect()->route('admin.dispatch.index')->with('success', 'Dispatch order created successfully!');
        } catch (\Throwable $e) {
            dd($e);
            DB::rollBack();
            return back()->with('error', 'An error occurred.')->withInput();
        }
    }

    public function edit(DispatchOrder $dispatchOrder)
    {
        $this->authorizeAccess($dispatchOrder);
        $dispatchOrder->load('items');

        $companyId = $this->getCompanyId() ?? $dispatchOrder->company_id;
        $customers = Customer::forCompany($companyId)->where('status', 'active')->orderBy('customer_name')->get();
        $items = Item::forCompany()->with('unit')->where('status', 'active')->orderBy('item_name')->get();

        $latestPrices = \App\Models\CostSheet::where('company_id', $companyId)
            ->where('status', 'Final')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('product_id')
            ->map(function($group) {
                return $group->first()->unit_price;
            });

        foreach ($items as $item) {
            $item->latest_price = $latestPrices[$item->id] ?? 0;
        }
        return view('admin.dispatch.edit', compact('dispatchOrder', 'customers', 'items'));
    }

    public function update(Request $request, DispatchOrder $dispatchOrder)
    {
        $this->authorizeAccess($dispatchOrder);

        $request->validate([
            'dispatch_date'      => 'required|date',
            'challan_no'         => 'required|max:255|unique:dispatch_orders,challan_no,' . $dispatchOrder->id,
            'customer_id'        => 'required|exists:customers,id',
            'customer_mobile'    => 'required',
            'transport_name'     => 'required|max:255',
            'total_amount'       => 'required|numeric|min:0',
            'items'              => 'required|array|min:1',
            'items.*.item_id'    => 'required|exists:items,id',
            'items.*.qty'        => 'required|numeric|min:0.01',
            'items.*.rate'       => 'required|numeric|min:0',
            'items.*.amount'     => 'required|numeric|min:0',
        ]);

        $companyId = $this->getCompanyId();

        // FIFO stock check — account for stock freed by reversal
        $errors = [];
        foreach ($request->items as $index => $item) {
            $batchDetails = [];
            $currentBatchQty = $this->getFifoAvailableQty($companyId, $item['item_id'], $batchDetails);

            $toBeFreed = (float) StockLedger::where('reference_id', $dispatchOrder->id)
                ->where('transaction_type', 'Dispatch Out')
                ->where('item_id', $item['item_id'])
                ->sum('qty_out');

            $availableQty = $currentBatchQty + $toBeFreed;

            if ($availableQty < (float) $item['qty']) {
                $itemName = Item::find($item['item_id'])->item_name ?? "Item #{$item['item_id']}";
                $errors["items.$index.qty"] = "Insufficient stock for {$itemName}. Available: {$availableQty}, Requested: {$item['qty']}";
            }
        }

        if (!empty($errors)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => $errors], 422);
            }
            return back()->withErrors($errors)->withInput();
        }

        try {
            DB::beginTransaction();

            // Reverse previous stock deductions
            $this->reverseStockDeductions($dispatchOrder->id);

            $dispatchOrder->update([
                'dispatch_date'   => $request->dispatch_date,
                'challan_no'      => $request->challan_no,
                'customer_id'     => $request->customer_id,
                'customer_mobile' => $request->customer_mobile,
                'transport_name'  => $request->transport_name,
                'total_amount'    => $request->total_amount,
            ]);

            $dispatchOrder->items()->delete();

            foreach ($request->items as $item) {
                $dispatchOrder->items()->create([
                    'item_id' => $item['item_id'],
                    'qty'     => $item['qty'],
                    'rate'    => $item['rate'],
                    'amount'  => $item['amount'],
                ]);

                $this->deductStockFifo($companyId, $item['item_id'], $item['qty'], $dispatchOrder->id, $request->dispatch_date);
            }

            DB::commit();

            return redirect()->route('admin.dispatch.index')->with('success', 'Dispatch order updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred.')->withInput();
        }
    }

    public function destroy(DispatchOrder $dispatchOrder)
    {
        $this->authorizeAccess($dispatchOrder);
        try {
            DB::beginTransaction();
            $this->reverseStockDeductions($dispatchOrder->id);
            $dispatchOrder->items()->delete();
            $dispatchOrder->delete();
            DB::commit();
            return redirect()->route('admin.dispatch.index')->with('success', 'Dispatch order deleted successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred.');
        }
    }

    public function reports(Request $request)
    {
        $companyId = $this->getCompanyId();

        $query = DispatchOrder::with('customer', 'items');
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('dispatch_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('dispatch_date', '<=', $request->to_date);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('dispatch_status', $request->status);
        }

        // Customer filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Transport filter
        if ($request->filled('transport_name')) {
            $query->where('transport_name', 'like', "%{$request->transport_name}%");
        }

        $reportType = $request->report_type ?? 'summary';
        $dispatchOrders = $query->latest()->paginate(50);
        $customers = Customer::forCompany($companyId)->where('status', 'active')->orderBy('customer_name')->get();

        // Aggregate data for different reports
        $customerWise = $dispatchOrders->groupBy('customer_id')->map(function ($orders, $cid) {
            $customer = $orders->first()->customer;
            return [
                'customer_name' => $customer->customer_name ?? 'Unknown',
                'total_orders'  => $orders->count(),
                'total_amount'  => $orders->sum('total_amount'),
            ];
        });

        $transportWise = $dispatchOrders->groupBy('transport_name')->map(function ($orders, $transport) {
            return [
                'transport_name' => $transport,
                'total_orders'   => $orders->count(),
                'total_amount'   => $orders->sum('total_amount'),
            ];
        });

        $statusWise = $dispatchOrders->groupBy('dispatch_status')->map(function ($orders, $status) {
            return [
                'status'       => $status,
                'total_orders' => $orders->count(),
                'total_amount' => $orders->sum('total_amount'),
            ];
        });

        return view('admin.dispatch.reports', compact(
            'dispatchOrders', 'customers', 'reportType',
            'customerWise', 'transportWise', 'statusWise'
        ));
    }

    protected function reverseStockDeductions($dispatchOrderId)
    {
        $outLedgers = StockLedger::where('reference_id', $dispatchOrderId)
            ->where('transaction_type', 'Dispatch Out')
            ->get();

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

        StockLedger::where('reference_id', $dispatchOrderId)
            ->where('transaction_type', 'Dispatch Out')
            ->delete();
    }

    protected function getFifoAvailableQty($companyId, $itemId, &$batchDetails = [])
    {
        $availableQty = 0;
        $batches = PurchaseBatch::where('item_id', $itemId)
            ->where('company_id', $companyId)
            ->where('balance_qty', '>', 0)
            ->orderBy('purchase_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($batches as $batch) {
            $availableQty += $batch->balance_qty;
            $batchDetails[] = [
                'lot_no'       => $batch->lot_no ?? "B#{$batch->id}",
                'purchase_date' => $batch->purchase_date,
                'balance_qty'  => $batch->balance_qty,
            ];
        }

        return $availableQty;
    }

    protected function deductStockFifo($companyId, $itemId, $qty, $referenceId, $transactionDate)
    {
        $remainingQty = $qty;
        $batches = PurchaseBatch::where('item_id', $itemId)
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

            $ledger = StockLedger::where('item_id', $itemId)
                ->where('company_id', $companyId)
                ->orderBy('id', 'desc')
                ->value('balance_qty') ?? 0;

            StockLedger::create([
                'company_id'       => $companyId,
                'item_id'          => $itemId,
                'transaction_type' => 'Dispatch Out',
                'reference_id'     => $referenceId,
                'batch_id'         => $batch->id,
                'qty_in'           => 0,
                'qty_out'          => $consumeQty,
                'balance_qty'      => $ledger - $consumeQty,
                'transaction_date' => $transactionDate,
            ]);

            $remainingQty -= $consumeQty;
        }
    }

    protected function authorizeAccess(DispatchOrder $dispatchOrder)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $dispatchOrder->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
