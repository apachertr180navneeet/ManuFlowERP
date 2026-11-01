@extends('admin.layouts.app')
@section('page_title', 'Edit Vendor')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.vendor.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-pencil"></i> Edit Vendor</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('admin.vendor.update', $vendor->id) }}" method="POST">
            @csrf
            @method('PUT')
            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">General Information</h6>
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Vendor Name <span class="text-danger">*</span></label>
                    <input type="text" name="vendor_name" class="form-control @error('vendor_name') is-invalid @enderror" placeholder="Enter vendor contact name" value="{{ old('vendor_name', $vendor->vendor_name) }}">
                    @error('vendor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Firm Name <span class="text-danger">*</span></label>
                    <input type="text" name="firm_name" class="form-control @error('firm_name') is-invalid @enderror" placeholder="Enter business / firm name" value="{{ old('firm_name', $vendor->firm_name) }}">
                    @error('firm_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Contact Information</h6>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" placeholder="Enter contact number" value="{{ old('mobile', $vendor->mobile) }}" maxlength="10">
                    @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email address" value="{{ old('email', $vendor->email) }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Address & Tax Details</h6>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Office Address <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="Enter street address, building, suite...">{{ old('address', $vendor->address) }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">GST No. (optional)</label>
                    <input type="text" name="gst_number" class="form-control @error('gst_number') is-invalid @enderror" placeholder="Enter GST number" value="{{ old('gst_number', $vendor->gst_number) }}">
                    @error('gst_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" placeholder="Enter city" value="{{ old('city', $vendor->city) }}">
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Pin Code <span class="text-danger">*</span></label>
                    <input type="text" name="pin_code" class="form-control @error('pin_code') is-invalid @enderror" placeholder="Enter pin code" value="{{ old('pin_code', $vendor->pin_code) }}">
                    @error('pin_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">State <span class="text-danger">*</span></label>
                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" placeholder="Enter state" value="{{ old('state', $vendor->state) }}">
                    @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.vendor.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Cancel</a>
                <button type="submit" class="btn btn-primary px-4" style="border-radius:.75rem;font-weight:600">Update Vendor</button>
            </div>
        </form>
    </div>
</div>
@endsection
