<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobWorker;
use Illuminate\Support\Facades\Auth;

class JobWorkerController extends Controller
{
    public function index(Request $request)
    {
        $query = JobWorker::where('company_id', Auth::user()->company_id);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $jobWorkers = $query->orderBy('id', 'desc')->paginate(10);
        
        if ($request->ajax()) {
            return response()->json([
                'table' => view('admin.job-worker._table', compact('jobWorkers'))->render(),
                'pagination' => view('admin.job-worker._pagination', compact('jobWorkers'))->render()
            ]);
        }

        return view('admin.job-worker.index', compact('jobWorkers'));
    }

    public function create()
    {
        return view('admin.job-worker.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pin_code' => 'nullable|string|max:20',
        ]);

        $data = $request->all();
        $data['company_id'] = Auth::user()->company_id;

        JobWorker::create($data);

        return redirect()->route('company.job-worker.index')->with('success', 'Job worker created successfully.');
    }

    public function edit($id)
    {
        $jobWorker = JobWorker::where('company_id', Auth::user()->company_id)->findOrFail($id);
        return view('admin.job-worker.edit', compact('jobWorker'));
    }

    public function update(Request $request, $id)
    {
        $jobWorker = JobWorker::where('company_id', Auth::user()->company_id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pin_code' => 'nullable|string|max:20',
        ]);

        $jobWorker->update($request->all());

        return redirect()->route('company.job-worker.index')->with('success', 'Job worker updated successfully.');
    }

    public function destroy($id)
    {
        $jobWorker = JobWorker::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $jobWorker->delete();
        return redirect()->route('company.job-worker.index')->with('success', 'Job worker deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $jobWorker = JobWorker::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $jobWorker->status = $jobWorker->status == 'active' ? 'inactive' : 'active';
        $jobWorker->save();

        return response()->json([
            'success' => true,
            'status' => $jobWorker->status
        ]);
    }
}
