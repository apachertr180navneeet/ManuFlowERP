<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Unit;
use Exception;

class UnitController extends Controller
{
    protected function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        try {
            $companyId = $this->getCompanyId();

            $query = Unit::parents()->with('children');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('unit_name', 'like', "%{$s}%")
                      ->orWhereHas('children', fn($q) => $q->where('unit_name', 'like', "%{$s}%"));
                });
            }

            $units = $query->latest()->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.unit._table', compact('units'))->render(),
                    'pagination' => view('admin.unit._pagination', compact('units'))->render(),
                ]);
            }

            return view('admin.unit.index', compact('units'));
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    public function create()
    {
        $parents = Unit::parents()->where('company_id', $this->getCompanyId())->get();
        return view('admin.unit.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:units,id',
            'conversion_value' => 'nullable|required_with:parent_id|numeric|min:0',
        ]);

        try {
            $data = $request->only(['unit_name', 'parent_id', 'conversion_value']);
            $data['company_id'] = $this->getCompanyId();
            $data['created_by'] = Auth::id();
            $data['status'] = 'active';
            $data['conversion_value'] = $data['conversion_value'] ?? 0;

            Unit::create($data);

            return redirect()->route('admin.unit.index')->with('success', 'Unit created successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    public function edit(Unit $unit)
    {
        $this->authorizeAccess($unit);
        $parents = Unit::parents()->where('company_id', $this->getCompanyId())->where('id', '!=', $unit->id)->get();
        return view('admin.unit.edit', compact('unit', 'parents'));
    }

    public function update(Request $request, Unit $unit)
    {
        $this->authorizeAccess($unit);
        $request->validate([
            'unit_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:units,id|different:' . $unit->id,
            'conversion_value' => 'nullable|required_with:parent_id|numeric|min:0',
        ]);

        try {
            $data = $request->only(['unit_name', 'parent_id', 'conversion_value']);
            $data['conversion_value'] = $data['conversion_value'] ?? 0;
            $unit->update($data);

            return redirect()->route('admin.unit.index')->with('success', 'Unit updated successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    public function toggleStatus(Unit $unit)
    {
        $this->authorizeAccess($unit);
        try {
            $unit->status = $unit->status === 'active' ? 'inactive' : 'active';
            $unit->save();
            return response()->json(['success' => true, 'status' => $unit->status]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred']);
        }
    }

    public function destroy(Unit $unit)
    {
        $this->authorizeAccess($unit);
        try {
            $unit->children()->delete();
            $unit->delete();
            return redirect()->route('admin.unit.index')->with('success', 'Unit deleted successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    protected function authorizeAccess(Unit $unit)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $unit->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
