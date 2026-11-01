<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Unit;
use Exception;

class ItemController extends Controller
{
    protected function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        try {
            $query = Item::query();
            if ($companyId = $this->getCompanyId()) {
                $query->where('company_id', $companyId);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('item_name', 'like', "%{$s}%")
                      ->orWhere('short_code', 'like', "%{$s}%")
                      ->orWhere('item_type', 'like', "%{$s}%");
                });
            }

            $items = $query->latest()->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.item._table', compact('items'))->render(),
                    'pagination' => view('admin.item._pagination', compact('items'))->render(),
                ]);
            }

            return view('admin.item.index', compact('items'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        $itemTypes = ['Raw Material', 'Finished'];
        $units = \App\Models\Unit::forCompany()->where('status', 'active')->orderBy('unit_name')->get();
        return view('admin.item.create', compact('itemTypes', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'short_code'  => 'required|unique:items,short_code',
            'item_name'   => 'required|unique:items,item_name',
            'item_type'   => 'required',
            'unit_id'     => 'required|exists:units,id',
            'size'        => 'nullable|string|max:255',
        ]);

        try {
            $data = $request->only(['short_code', 'item_name', 'item_type', 'unit_id', 'size']);
            $data['company_id'] = $this->getCompanyId();
            $data['created_by'] = Auth::id();
            $data['status'] = 'active';

            Item::create($data);

            return redirect()->route('admin.item.index')->with('success', 'Item created successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit(Item $item)
    {
        $this->authorizeAccess($item);
        $itemTypes = ['Raw Material', 'Finished'];
        $units = Unit::forCompany()->where('status', 'active')->orderBy('unit_name')->get();
        return view('admin.item.edit', compact('item', 'itemTypes', 'units'));
    }

    public function update(Request $request, Item $item)
    {
        $this->authorizeAccess($item);
        $request->validate([
            'short_code'  => 'required|unique:items,short_code,' . $item->id,
            'item_name'   => 'required|unique:items,item_name,' . $item->id,
            'item_type'   => 'required',
            'unit_id'     => 'required|exists:units,id',
            'size'        => 'nullable|string|max:255',
        ]);

        try {
            $data = $request->only(['short_code', 'item_name', 'item_type', 'unit_id', 'size']);
            $item->update($data);

            return redirect()->route('admin.item.index')->with('success', 'Item updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(Item $item)
    {
        $this->authorizeAccess($item);
        try {
            $item->status = $item->status === 'active' ? 'inactive' : 'active';
            $item->save();
            return response()->json(['success' => true, 'status' => $item->status]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Item $item)
    {
        $this->authorizeAccess($item);
        try {
            $item->delete();
            return redirect()->route('admin.item.index')->with('success', 'Item deleted successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function authorizeAccess(Item $item)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $item->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
