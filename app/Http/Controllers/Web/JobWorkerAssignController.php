<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobWorkerAssign;
use App\Models\JobWorkerAssignItem;
use App\Models\JobWorker;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobWorkerAssignController extends Controller
{
    public function index(Request $request)
    {
        $query = JobWorkerAssign::with(['jobWorker'])->where('company_id', Auth::user()->company_id);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('assign_no', 'like', "%{$search}%")
                  ->orWhereHas('jobWorker', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $jobWorkerAssigns = $query->orderBy('id', 'desc')->paginate(10);
        
        if ($request->ajax()) {
            return response()->json([
                'table' => view('admin.job-worker-assign._table', compact('jobWorkerAssigns'))->render(),
                'pagination' => view('admin.job-worker-assign._pagination', compact('jobWorkerAssigns'))->render()
            ]);
        }

        return view('admin.job-worker-assign.index', compact('jobWorkerAssigns'));
    }

    public function create()
    {
        $jobWorkers = JobWorker::where('company_id', Auth::user()->company_id)->where('status', 'active')->get();
        $items = Item::where('company_id', Auth::user()->company_id)->where('status', 'active')->get();
        return view('admin.job-worker-assign.create', compact('jobWorkers', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'assign_no' => 'required|string|max:255',
            'job_worker_id' => 'required|exists:job_workers,id',
            'contact_number' => 'nullable|string|max:20',
            'transport_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            
            $jobWorkerAssign = JobWorkerAssign::create([
                'company_id' => Auth::user()->company_id,
                'assign_no' => $request->assign_no,
                'date' => $request->date,
                'job_worker_id' => $request->job_worker_id,
                'contact_number' => $request->contact_number,
                'transport_name' => $request->transport_name,
                'status' => 'Pending',
            ]);

            foreach ($request->items as $item) {
                JobWorkerAssignItem::create([
                    'job_worker_assign_id' => $jobWorkerAssign->id,
                    'item_id' => $item['item_id'],
                    'qty' => $item['qty'],
                    'unit' => $item['unit'],
                    'rate' => $item['rate'],
                    'amount' => $item['amount'],
                ]);
            }

            DB::commit();
            return redirect()->route('company.job-worker-assign.index')->with('success', 'Job worker assign created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to create job worker assign. ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $jobWorkerAssign = JobWorkerAssign::with('items')->where('company_id', Auth::user()->company_id)->findOrFail($id);
        $jobWorkers = JobWorker::where('company_id', Auth::user()->company_id)->where('status', 'active')->get();
        $items = Item::where('company_id', Auth::user()->company_id)->where('status', 'active')->get();
        return view('admin.job-worker-assign.edit', compact('jobWorkerAssign', 'jobWorkers', 'items'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'assign_no' => 'required|string|max:255',
            'job_worker_id' => 'required|exists:job_workers,id',
            'contact_number' => 'nullable|string|max:20',
            'transport_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        $jobWorkerAssign = JobWorkerAssign::where('company_id', Auth::user()->company_id)->findOrFail($id);

        try {
            DB::beginTransaction();

            $jobWorkerAssign->update([
                'date' => $request->date,
                'assign_no' => $request->assign_no,
                'job_worker_id' => $request->job_worker_id,
                'contact_number' => $request->contact_number,
                'transport_name' => $request->transport_name,
            ]);

            // Delete old items
            JobWorkerAssignItem::where('job_worker_assign_id', $jobWorkerAssign->id)->delete();

            // Insert new items
            foreach ($request->items as $item) {
                JobWorkerAssignItem::create([
                    'job_worker_assign_id' => $jobWorkerAssign->id,
                    'item_id' => $item['item_id'],
                    'qty' => $item['qty'],
                    'unit' => $item['unit'],
                    'rate' => $item['rate'],
                    'amount' => $item['amount'],
                ]);
            }

            DB::commit();
            return redirect()->route('company.job-worker-assign.index')->with('success', 'Job worker assign updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to update job worker assign. ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $jobWorkerAssign = JobWorkerAssign::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $jobWorkerAssign->delete();
        // Items will be cascade deleted or soft deleted based on setup
        return redirect()->route('company.job-worker-assign.index')->with('success', 'Job worker assign deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $jobWorkerAssign = JobWorkerAssign::where('company_id', Auth::user()->company_id)->findOrFail($id);
        // Toggle status? The design has pending, maybe complete?
        // I will implement a simple toggle if needed or just leave as is
        $jobWorkerAssign->status = $jobWorkerAssign->status == 'Pending' ? 'Completed' : 'Pending';
        $jobWorkerAssign->save();

        return response()->json([
            'success' => true,
            'status' => $jobWorkerAssign->status
        ]);
    }
}
