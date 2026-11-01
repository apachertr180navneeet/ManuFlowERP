<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Salesman;
use Exception;

class SalesmanController extends Controller
{
    protected function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        try {
            $query = Salesman::query();
            if ($companyId = $this->getCompanyId()) {
                $query->where('company_id', $companyId);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('salesman_name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%")
                      ->orWhere('mobile', 'like', "%{$s}%")
                      ->orWhere('city', 'like', "%{$s}%");
                });
            }

            $salesmen = $query->latest()->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.salesman._table', compact('salesmen'))->render(),
                    'pagination' => view('admin.salesman._pagination', compact('salesmen'))->render(),
                ]);
            }

            return view('admin.salesman.index', compact('salesmen'));
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    public function create()
    {
        return view('admin.salesman.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'salesman_name' => 'required',
            'mobile'        => 'required|digits:10|unique:salesmen,mobile',
            'email'         => 'nullable|email|unique:salesmen,email',
            'joining_date'  => 'nullable|date',
            'city'          => 'required',
        ]);

        try {
            $data = $request->only(['salesman_name', 'mobile', 'email', 'joining_date', 'city', 'address']);
            $data['company_id'] = $this->getCompanyId();
            $data['created_by'] = Auth::id();
            $data['status'] = 'active';

            Salesman::create($data);

            return redirect()->route('admin.salesman.index')->with('success', 'Salesman created successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    public function edit(Salesman $salesman)
    {
        $this->authorizeAccess($salesman);
        return view('admin.salesman.edit', compact('salesman'));
    }

    public function update(Request $request, Salesman $salesman)
    {
        $this->authorizeAccess($salesman);
        $request->validate([
            'salesman_name' => 'required',
            'mobile'        => 'required|digits:10|unique:salesmen,mobile,' . $salesman->id,
            'email'         => 'nullable|email|unique:salesmen,email,' . $salesman->id,
            'joining_date'  => 'nullable|date',
            'city'          => 'required',
        ]);

        try {
            $data = $request->only(['salesman_name', 'mobile', 'email', 'joining_date', 'city', 'address']);
            $salesman->update($data);

            return redirect()->route('admin.salesman.index')->with('success', 'Salesman updated successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    public function toggleStatus(Salesman $salesman)
    {
        $this->authorizeAccess($salesman);
        try {
            $salesman->status = $salesman->status === 'active' ? 'inactive' : 'active';
            $salesman->save();
            return response()->json(['success' => true, 'status' => $salesman->status]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred']);
        }
    }

    public function destroy(Salesman $salesman)
    {
        $this->authorizeAccess($salesman);
        try {
            $salesman->delete();
            return redirect()->route('admin.salesman.index')->with('success', 'Salesman deleted successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    protected function authorizeAccess(Salesman $salesman)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $salesman->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
