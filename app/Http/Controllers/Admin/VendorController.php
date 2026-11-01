<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor;
use Exception;

class VendorController extends Controller
{
    protected function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        try {
            $query = Vendor::query();
            if ($companyId = $this->getCompanyId()) {
                $query->where('company_id', $companyId);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('vendor_name', 'like', "%{$s}%")
                      ->orWhere('firm_name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%")
                      ->orWhere('mobile', 'like', "%{$s}%")
                      ->orWhere('city', 'like', "%{$s}%");
                });
            }

            $vendors = $query->latest()->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.vendor._table', compact('vendors'))->render(),
                    'pagination' => view('admin.vendor._pagination', compact('vendors'))->render(),
                ]);
            }

            return view('admin.vendor.index', compact('vendors'));
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    public function create()
    {
        return view('admin.vendor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_name' => 'required',
            'firm_name'   => 'required',
            'mobile'      => 'required|digits:10|unique:vendors,mobile',
            'email'       => 'required|email|unique:vendors,email',
            'address'     => 'required',
            'city'        => 'required',
            'pin_code'    => 'required',
            'state'       => 'required',
        ]);

        try {
            $data = $request->only(['vendor_name', 'firm_name', 'mobile', 'email', 'address', 'city', 'pin_code', 'state', 'gst_no', 'contact_person']);
            $data['company_id'] = $this->getCompanyId();
            $data['created_by'] = Auth::id();
            $data['status'] = 'active';

            Vendor::create($data);

            return redirect()->route('admin.vendor.index')->with('success', 'Vendor created successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    public function edit(Vendor $vendor)
    {
        $this->authorizeAccess($vendor);
        return view('admin.vendor.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $this->authorizeAccess($vendor);
        $request->validate([
            'vendor_name' => 'required',
            'firm_name'   => 'required',
            'mobile'      => 'required|digits:10|unique:vendors,mobile,' . $vendor->id,
            'email'       => 'required|email|unique:vendors,email,' . $vendor->id,
            'address'     => 'required',
            'city'        => 'required',
            'pin_code'    => 'required',
            'state'       => 'required',
        ]);

        try {
            $data = $request->only(['vendor_name', 'firm_name', 'mobile', 'email', 'address', 'city', 'pin_code', 'state', 'gst_no', 'contact_person']);
            $vendor->update($data);

            return redirect()->route('admin.vendor.index')->with('success', 'Vendor updated successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    public function toggleStatus(Vendor $vendor)
    {
        $this->authorizeAccess($vendor);
        try {
            $vendor->status = $vendor->status === 'active' ? 'inactive' : 'active';
            $vendor->save();
            return response()->json(['success' => true, 'status' => $vendor->status]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred']);
        }
    }

    public function destroy(Vendor $vendor)
    {
        $this->authorizeAccess($vendor);
        try {
            $vendor->delete();
            return redirect()->route('admin.vendor.index')->with('success', 'Vendor deleted successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    protected function authorizeAccess(Vendor $vendor)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $vendor->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
