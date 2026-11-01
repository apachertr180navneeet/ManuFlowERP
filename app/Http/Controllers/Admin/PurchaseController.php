<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseBatch;
use App\Models\StockLedger;
use App\Models\Vendor;
use App\Models\Item;

class PurchaseController extends Controller
{
    protected function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        try {
            $query = Purchase::with('vendor');
            if ($companyId = $this->getCompanyId()) {
                $query->where('company_id', $companyId);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('challan_no', 'like', "%{$s}%")
                      ->orWhere('bno', 'like', "%{$s}%")
                      ->orWhereHas('vendor', function ($qv) use ($s) {
                          $qv->where('vendor_name', 'like', "%{$s}%")
                             ->orWhere('firm_name', 'like', "%{$s}%");
                      });
                });
            }

            $purchases = $query->latest()->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.purchase._table', compact('purchases'))->render(),
                    'pagination' => view('admin.purchase._pagination', compact('purchases'))->render(),
                ]);
            }

            return view('admin.purchase.index', compact('purchases'));
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred.');
        }
    }

    public function create()
    {
        $companyId = $this->getCompanyId();
        $vendors = Vendor::forCompany($companyId)->where('status', 'active')->orderBy('vendor_name')->get();
        $items = Item::forCompany()->whereIn('item_type', ['Raw Material', 'Finished'])->where('status', 'active')->orderBy('item_name')->get();

        return view('admin.purchase.create', compact('vendors', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id'          => 'required|exists:vendors,id',
            'purchase_date'      => 'required|date',
            'bno'                => 'required|max:255',
            'challan_no'         => 'required|max:255',
            'invoice_no'         => 'nullable|max:255',
            'transport'          => 'nullable|max:255',
            'lr_no'              => 'nullable|max:255',
            'subtotal'           => 'required|numeric|min:0',
            'gst_percentage'     => 'required|numeric|min:0|max:100',
            'discount'           => 'required|numeric|min:0',
            'transport_charges'  => 'required|numeric|min:0',
            'other_charges'      => 'required|numeric|min:0',
            'notes'              => 'nullable|max:1000',
            'items'              => 'required|array|min:1',
            'items.*.item_id'    => 'nullable|exists:items,id',
            'items.*.item_name'  => 'required|max:255',
            'items.*.unit'       => 'required|max:50',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.rate'       => 'required|numeric|min:0',
            'items.*.amount'     => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            $subtotal = $request->subtotal;
            $gst = $request->gst_percentage;
            $taxAmount = $subtotal * ($gst / 100);
            $discount = $request->discount;
            $transportCharges = $request->transport_charges;
            $otherCharges = $request->other_charges;
            $totalAmount = $subtotal + $taxAmount - $discount + $transportCharges + $otherCharges;

            $purchase = Purchase::create([
                'company_id'        => $this->getCompanyId(),
                'vendor_id'         => $request->vendor_id,
                'purchase_date'     => $request->purchase_date,
                'invoice_no'        => $request->invoice_no ?? $request->challan_no,
                'bno'               => $request->bno,
                'challan_no'        => $request->challan_no,
                'transport'         => $request->transport,
                'lr_no'             => $request->lr_no,
                'subtotal'          => $subtotal,
                'gst_percentage'    => $gst,
                'tax_amount'        => round($taxAmount, 2),
                'discount'          => $discount,
                'transport_charges' => $transportCharges,
                'other_charges'     => $otherCharges,
                'total_amount'      => round($totalAmount, 2),
                'purchase_status'   => 'Completed',
                'notes'             => $request->notes,
                'status'            => 'active',
                'created_by'        => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                $purchaseItem = $purchase->items()->create([
                    'item_id'    => $item['item_id'] ?? null,
                    'item_name'  => $item['item_name'],
                    'unit'       => $item['unit'],
                    'quantity'   => $item['quantity'],
                    'rate'       => $item['rate'],
                    'amount'     => $item['amount'],
                    'created_by' => Auth::id(),
                ]);

                // Create purchase batch for FIFO tracking
                if ($item['item_id']) {
                    PurchaseBatch::create([
                        'company_id'   => $purchase->company_id,
                        'purchase_id'  => $purchase->id,
                        'item_id'      => $item['item_id'],
                        'received_qty' => $item['quantity'],
                        'consumed_qty' => 0,
                        'balance_qty'  => $item['quantity'],
                        'purchase_date'=> $request->purchase_date,
                    ]);

                    // Stock ledger entry for purchase
                    $ledger = StockLedger::where('item_id', $item['item_id'])
                        ->where('company_id', $purchase->company_id)
                        ->orderBy('id', 'desc')
                        ->value('balance_qty') ?? 0;

                    StockLedger::create([
                        'company_id'       => $purchase->company_id,
                        'item_id'          => $item['item_id'],
                        'transaction_type' => 'Purchase',
                        'reference_id'     => $purchase->id,
                        'batch_id'         => null,
                        'qty_in'           => $item['quantity'],
                        'qty_out'          => 0,
                        'balance_qty'      => $ledger + $item['quantity'],
                        'transaction_date' => $request->purchase_date,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.purchase.index')->with('success', 'Purchase created successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred.')->withInput();
        }
    }

    public function edit(Purchase $purchase)
    {
        $this->authorizeAccess($purchase);
        $purchase->load('items');

        $companyId = $this->getCompanyId() ?? $purchase->company_id;
        $vendors = Vendor::forCompany($companyId)->where('status', 'active')->orderBy('vendor_name')->get();
        $items = Item::forCompany()->whereIn('item_type', ['Raw Material', 'Finished'])->where('status', 'active')->orderBy('item_name')->get();

        return view('admin.purchase.edit', compact('purchase', 'vendors', 'items'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $this->authorizeAccess($purchase);

        $request->validate([
            'vendor_id'          => 'required|exists:vendors,id',
            'purchase_date'      => 'required|date',
            'bno'                => 'required|max:255',
            'challan_no'         => 'required|max:255',
            'invoice_no'         => 'nullable|max:255',
            'transport'          => 'nullable|max:255',
            'lr_no'              => 'nullable|max:255',
            'subtotal'           => 'required|numeric|min:0',
            'gst_percentage'     => 'required|numeric|min:0|max:100',
            'discount'           => 'required|numeric|min:0',
            'transport_charges'  => 'required|numeric|min:0',
            'other_charges'      => 'required|numeric|min:0',
            'notes'              => 'nullable|max:1000',
            'items'              => 'required|array|min:1',
            'items.*.item_id'    => 'nullable|exists:items,id',
            'items.*.item_name'  => 'required|max:255',
            'items.*.unit'       => 'required|max:50',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.rate'       => 'required|numeric|min:0',
            'items.*.amount'     => 'required|numeric|min:0',
        ]);

        try {
            // Prevent editing if any batches have been consumed by manufacturing
            $consumedBatches = PurchaseBatch::where('purchase_id', $purchase->id)
                ->where('consumed_qty', '>', 0)
                ->exists();

            if ($consumedBatches) {
                return back()->with('error', 'Cannot edit this purchase because some items have been consumed by manufacturing or dispatch. Reverse those records first.')->withInput();
            }

            DB::beginTransaction();
            $subtotal = $request->subtotal;
            $gst = $request->gst_percentage;
            $taxAmount = $subtotal * ($gst / 100);
            $discount = $request->discount;
            $transportCharges = $request->transport_charges;
            $otherCharges = $request->other_charges;
            $totalAmount = $subtotal + $taxAmount - $discount + $transportCharges + $otherCharges;

            $purchase->update([
                'vendor_id'         => $request->vendor_id,
                'purchase_date'     => $request->purchase_date,
                'invoice_no'        => $request->invoice_no ?? $request->challan_no,
                'bno'               => $request->bno,
                'challan_no'        => $request->challan_no,
                'transport'         => $request->transport,
                'lr_no'             => $request->lr_no,
                'subtotal'          => $subtotal,
                'gst_percentage'    => $gst,
                'tax_amount'        => round($taxAmount, 2),
                'discount'          => $discount,
                'transport_charges' => $transportCharges,
                'other_charges'     => $otherCharges,
                'total_amount'      => round($totalAmount, 2),
                'notes'             => $request->notes,
            ]);

            // Delete existing batch and ledger records
            PurchaseBatch::where('purchase_id', $purchase->id)->delete();
            StockLedger::where('reference_id', $purchase->id)->where('transaction_type', 'Purchase')->delete();
            $purchase->items()->delete();

            // Recreate items, batches, and ledger entries
            foreach ($request->items as $item) {
                $purchase->items()->create([
                    'item_id'    => $item['item_id'] ?? null,
                    'item_name'  => $item['item_name'],
                    'unit'       => $item['unit'],
                    'quantity'   => $item['quantity'],
                    'rate'       => $item['rate'],
                    'amount'     => $item['amount'],
                    'created_by' => Auth::id(),
                ]);

                // Recreate purchase batch for FIFO tracking
                if ($item['item_id']) {
                    PurchaseBatch::create([
                        'company_id'    => $purchase->company_id,
                        'purchase_id'   => $purchase->id,
                        'item_id'       => $item['item_id'],
                        'received_qty'  => $item['quantity'],
                        'consumed_qty'  => 0,
                        'balance_qty'   => $item['quantity'],
                        'purchase_date' => $request->purchase_date,
                    ]);

                    // Recreate stock ledger entry
                    $ledger = StockLedger::where('item_id', $item['item_id'])
                        ->where('company_id', $purchase->company_id)
                        ->orderBy('id', 'desc')
                        ->value('balance_qty') ?? 0;

                    StockLedger::create([
                        'company_id'       => $purchase->company_id,
                        'item_id'          => $item['item_id'],
                        'transaction_type' => 'Purchase',
                        'reference_id'     => $purchase->id,
                        'batch_id'         => null,
                        'qty_in'           => $item['quantity'],
                        'qty_out'          => 0,
                        'balance_qty'      => $ledger + $item['quantity'],
                        'transaction_date' => $request->purchase_date,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.purchase.index')->with('success', 'Purchase updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred.')->withInput();
        }
    }

    public function toggleStatus(Purchase $purchase)
    {
        $this->authorizeAccess($purchase);
        try {
            $purchase->status = $purchase->status === 'active' ? 'inactive' : 'active';
            $purchase->save();
            return response()->json(['success' => true, 'status' => $purchase->status]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred.']);
        }
    }

    public function destroy(Purchase $purchase)
    {
        $this->authorizeAccess($purchase);
        try {
            DB::beginTransaction();

            // Check if any batches from this purchase have been partially consumed
            $consumedBatches = PurchaseBatch::where('purchase_id', $purchase->id)
                ->where('consumed_qty', '>', 0)
                ->exists();

            if ($consumedBatches) {
                DB::rollBack();
                return back()->with('error', 'Cannot delete this purchase because some items have been consumed by manufacturing or dispatch. Reverse those records first.');
            }

            PurchaseBatch::where('purchase_id', $purchase->id)->delete();
            StockLedger::where('reference_id', $purchase->id)->where('transaction_type', 'Purchase')->delete();
            $purchase->items()->delete();
            $purchase->delete();
            DB::commit();
            return redirect()->route('admin.purchase.index')->with('success', 'Purchase deleted successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred.');
        }
    }

    protected function authorizeAccess(Purchase $purchase)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $purchase->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
