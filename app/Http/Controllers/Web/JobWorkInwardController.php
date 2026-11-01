<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobWorkInward;
use App\Models\JobWorkInwardItem;
use App\Models\JobWorkerAssign;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobWorkInwardController extends Controller
{
    public function index(Request $request)
    {
        $query = JobWorkInward::with(['jobWorkerAssign.jobWorker'])->where('company_id', Auth::user()->company_id);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('inward_no', 'like', "%{$search}%")
                  ->orWhere('challan_no', 'like', "%{$search}%")
                  ->orWhereHas('jobWorkerAssign.jobWorker', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $jobWorkInwards = $query->orderBy('id', 'desc')->paginate(10);
        
        if ($request->ajax()) {
            return response()->json([
                'table' => view('admin.job-work-inward._table', compact('jobWorkInwards'))->render(),
                'pagination' => view('admin.job-work-inward._pagination', compact('jobWorkInwards'))->render()
            ]);
        }

        return view('admin.job-work-inward.index', compact('jobWorkInwards'));
    }

    public function create()
    {
        $assigns = JobWorkerAssign::with('jobWorker')->where('company_id', Auth::user()->company_id)->where('status', 'Pending')->get();
        $items = Item::where('company_id', Auth::user()->company_id)->where('status', 'active')->get();
        return view('admin.job-work-inward.create', compact('assigns', 'items'));
    }

    public function getAssignDetails(Request $request, $id)
    {
        $assign = JobWorkerAssign::with(['jobWorker', 'items.item.unit'])->where('company_id', Auth::user()->company_id)->findOrFail($id);
        
        $excludeInwardId = $request->query('exclude_inward_id');

        foreach ($assign->items as $item) {
            $receivedQtyQuery = \App\Models\JobWorkInwardItem::whereHas('inward', function($q) use ($id) {
                $q->where('job_worker_assign_id', $id);
            })->where('item_id', $item->item_id);

            if ($excludeInwardId) {
                $receivedQtyQuery->where('job_work_inward_id', '!=', $excludeInwardId);
            }

            $receivedQty = $receivedQtyQuery->sum('qty');
            
            $item->assigned_qty = $item->qty;
            $item->received_qty = $receivedQty;
            $item->remaining_qty = max(0, $item->qty - $receivedQty);
        }

        return response()->json($assign);
    }

    public function store(Request $request)
    {
        $request->validate([
            'inward_date' => 'required|date',
            'inward_no' => 'required|string|max:255',
            'job_worker_assign_id' => 'required|exists:job_worker_assigns,id',
            'challan_no' => 'nullable|string|max:255',
            'transport_name' => 'nullable|string|max:255',
            'lr_number' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'other_charges' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'transport_charges' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $assignItems = \App\Models\JobWorkerAssignItem::where('job_worker_assign_id', $request->job_worker_assign_id)
            ->with('item.unit')
            ->get()->keyBy('item_id');

        foreach ($request->items as $item) {
            if (!isset($assignItems[$item['item_id']])) {
                return redirect()->back()->with('error', 'One or more items are not assigned to this job worker assignment.')->withInput();
            }

            $receivedQty = \App\Models\JobWorkInwardItem::whereHas('inward', function($q) use ($request) {
                $q->where('job_worker_assign_id', $request->job_worker_assign_id);
            })->where('item_id', $item['item_id'])->sum('qty');
            
            $assignedQty = $assignItems[$item['item_id']]->qty;
            $remainingQty = max(0, $assignedQty - $receivedQty);
            
            if ($item['qty'] > $remainingQty) {
                $unit = $assignItems[$item['item_id']]->item->unit->unit_name ?? 'GM';
                return redirect()->back()->with('error', "Inward quantity cannot exceed the remaining assigned quantity. Remaining quantity: {$remainingQty} {$unit}.")->withInput();
            }
        }

        try {
            DB::beginTransaction();
            
            $jobWorkInward = JobWorkInward::create([
                'company_id' => Auth::user()->company_id,
                'job_worker_assign_id' => $request->job_worker_assign_id,
                'inward_date' => $request->inward_date,
                'inward_no' => $request->inward_no,
                'challan_no' => $request->challan_no,
                'transport_name' => $request->transport_name,
                'lr_number' => $request->lr_number,
                'remark' => $request->remark,
                'subtotal' => $request->subtotal,
                'other_charges' => $request->other_charges ?? 0,
                'discount' => $request->discount ?? 0,
                'transport_charges' => $request->transport_charges ?? 0,
                'total_amount' => $request->total_amount,
                'status' => 'Completed',
            ]);

            foreach ($request->items as $item) {
                JobWorkInwardItem::create([
                    'job_work_inward_id' => $jobWorkInward->id,
                    'item_id' => $item['item_id'],
                    'assigned_qty' => $item['assigned_qty'] ?? 0,
                    'prev_rec_qty' => $item['prev_rec_qty'] ?? 0,
                    'remaining_qty' => $item['remaining_qty'] ?? 0,
                    'qty' => $item['qty'],
                    'unit' => $item['unit'],
                    'rate' => $item['rate'],
                    'amount' => $item['amount'],
                ]);
            }

            // Update assign status
            $this->updateJobWorkerAssignStatus($request->job_worker_assign_id);

            DB::commit();
            return redirect()->route('company.job-work-inward.index')->with('success', 'Job work inward created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to create job work inward. ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $jobWorkInward = JobWorkInward::with('items')->where('company_id', Auth::user()->company_id)->findOrFail($id);
        $assigns = JobWorkerAssign::with('jobWorker')->where('company_id', Auth::user()->company_id)->get();
        $items = Item::where('company_id', Auth::user()->company_id)->where('status', 'active')->get();
        return view('admin.job-work-inward.edit', compact('jobWorkInward', 'assigns', 'items'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'inward_date' => 'required|date',
            'inward_no' => 'required|string|max:255',
            'job_worker_assign_id' => 'required|exists:job_worker_assigns,id',
            'challan_no' => 'nullable|string|max:255',
            'transport_name' => 'nullable|string|max:255',
            'lr_number' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'other_charges' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'transport_charges' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $assignItems = \App\Models\JobWorkerAssignItem::where('job_worker_assign_id', $request->job_worker_assign_id)
            ->with('item.unit')
            ->get()->keyBy('item_id');

        foreach ($request->items as $item) {
            if (!isset($assignItems[$item['item_id']])) {
                return redirect()->back()->with('error', 'One or more items are not assigned to this job worker assignment.')->withInput();
            }

            $receivedQty = \App\Models\JobWorkInwardItem::whereHas('inward', function($q) use ($request) {
                $q->where('job_worker_assign_id', $request->job_worker_assign_id);
            })->where('item_id', $item['item_id'])
              ->where('job_work_inward_id', '!=', $id)
              ->sum('qty');
            
            $assignedQty = $assignItems[$item['item_id']]->qty;
            $remainingQty = max(0, $assignedQty - $receivedQty);
            
            if ($item['qty'] > $remainingQty) {
                $unit = $assignItems[$item['item_id']]->item->unit->unit_name ?? 'GM';
                return redirect()->back()->with('error', "Inward quantity cannot exceed the remaining assigned quantity. Remaining quantity: {$remainingQty} {$unit}.")->withInput();
            }
        }

        $jobWorkInward = JobWorkInward::where('company_id', Auth::user()->company_id)->findOrFail($id);

        try {
            DB::beginTransaction();

            $oldAssignId = null;
            // Record old assign status if changed
            if ($jobWorkInward->job_worker_assign_id != $request->job_worker_assign_id) {
                $oldAssignId = $jobWorkInward->job_worker_assign_id;
            }

            $jobWorkInward->update([
                'job_worker_assign_id' => $request->job_worker_assign_id,
                'inward_date' => $request->inward_date,
                'inward_no' => $request->inward_no,
                'challan_no' => $request->challan_no,
                'transport_name' => $request->transport_name,
                'lr_number' => $request->lr_number,
                'remark' => $request->remark,
                'subtotal' => $request->subtotal,
                'other_charges' => $request->other_charges ?? 0,
                'discount' => $request->discount ?? 0,
                'transport_charges' => $request->transport_charges ?? 0,
                'total_amount' => $request->total_amount,
            ]);

            // Delete old items
            JobWorkInwardItem::where('job_work_inward_id', $jobWorkInward->id)->delete();

            // Insert new items
            foreach ($request->items as $item) {
                JobWorkInwardItem::create([
                    'job_work_inward_id' => $jobWorkInward->id,
                    'item_id' => $item['item_id'],
                    'assigned_qty' => $item['assigned_qty'] ?? 0,
                    'prev_rec_qty' => $item['prev_rec_qty'] ?? 0,
                    'remaining_qty' => $item['remaining_qty'] ?? 0,
                    'qty' => $item['qty'],
                    'unit' => $item['unit'],
                    'rate' => $item['rate'],
                    'amount' => $item['amount'],
                ]);
            }

            // Update new assign status
            $this->updateJobWorkerAssignStatus($request->job_worker_assign_id);
            if ($oldAssignId) {
                $this->updateJobWorkerAssignStatus($oldAssignId);
            }

            DB::commit();
            return redirect()->route('company.job-work-inward.index')->with('success', 'Job work inward updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to update job work inward. ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $jobWorkInward = JobWorkInward::where('company_id', Auth::user()->company_id)->findOrFail($id);
            $assignId = $jobWorkInward->job_worker_assign_id;
            $jobWorkInward->delete();
            $this->updateJobWorkerAssignStatus($assignId);
            DB::commit();
            return redirect()->route('company.job-work-inward.index')->with('success', 'Job work inward deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to delete job work inward. ' . $e->getMessage());
        }
    }
    private function updateJobWorkerAssignStatus($assignId)
    {
        $assign = JobWorkerAssign::with('items')->find($assignId);
        if (!$assign) return;

        $allCompleted = true;
        foreach ($assign->items as $item) {
            $receivedQty = \App\Models\JobWorkInwardItem::whereHas('inward', function($q) use ($assignId) {
                $q->where('job_worker_assign_id', $assignId);
            })->where('item_id', $item->item_id)->sum('qty');
            
            if ($receivedQty < $item->qty) {
                $allCompleted = false;
                break;
            }
        }

        if ($allCompleted && $assign->items->count() > 0) {
            $assign->update(['status' => 'Completed']);
        } else {
            $assign->update(['status' => 'Pending']);
        }
    }
}
