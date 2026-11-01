<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use Exception;

class CustomerController extends Controller
{
    protected function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        try {
            $query = Customer::query();
            if ($companyId = $this->getCompanyId()) {
                $query->where('company_id', $companyId);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('customer_name', 'like', "%{$s}%")
                      ->orWhere('firm_name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%")
                      ->orWhere('mobile', 'like', "%{$s}%")
                      ->orWhere('location', 'like', "%{$s}%");
                });
            }

            $customers = $query->latest()->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.customer._table', compact('customers'))->render(),
                    'pagination' => view('admin.customer._pagination', compact('customers'))->render(),
                ]);
            }

            return view('admin.customer.index', compact('customers'));
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    public function create()
    {
        return view('admin.customer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'mobile'        => 'required|digits:10|unique:customers,mobile',
            'email'         => 'required|email|unique:customers,email',
            'location'      => 'required',
        ]);

        try {
            $data = $request->only(['customer_name', 'firm_name', 'mobile', 'email', 'location', 'address', 'city', 'pin_code', 'state', 'gst_no']);
            $data['company_id'] = $this->getCompanyId();
            $data['created_by'] = Auth::id();
            $data['status'] = 'active';

            Customer::create($data);

            return redirect()->route('admin.customer.index')->with('success', 'Customer created successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    public function edit(Customer $customer)
    {
        $this->authorizeAccess($customer);
        return view('admin.customer.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorizeAccess($customer);
        $request->validate([
            'customer_name' => 'required',
            'mobile'        => 'required|digits:10|unique:customers,mobile,' . $customer->id,
            'email'         => 'required|email|unique:customers,email,' . $customer->id,
            'location'      => 'required',
        ]);

        try {
            $data = $request->only(['customer_name', 'firm_name', 'mobile', 'email', 'location', 'address', 'city', 'pin_code', 'state', 'gst_no']);
            $customer->update($data);

            return redirect()->route('admin.customer.index')->with('success', 'Customer updated successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    public function toggleStatus(Customer $customer)
    {
        $this->authorizeAccess($customer);
        try {
            $customer->status = $customer->status === 'active' ? 'inactive' : 'active';
            $customer->save();
            return response()->json(['success' => true, 'status' => $customer->status]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred']);
        }
    }

    public function destroy(Customer $customer)
    {
        $this->authorizeAccess($customer);
        try {
            $customer->delete();
            return redirect()->route('admin.customer.index')->with('success', 'Customer deleted successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'An error occurred');
        }
    }

    protected function authorizeAccess(Customer $customer)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $customer->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
