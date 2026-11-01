@extends('admin.layouts.app')
@section('page_title', 'Edit Customer')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.customer.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-pencil"></i> Edit Customer</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('admin.customer.update', $customer->id) }}" method="POST">
            @csrf
            @method('PUT')
            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Customer Information</h6>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" placeholder="Enter customer name" value="{{ old('customer_name', $customer->customer_name) }}">
                    @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" placeholder="Enter mobile number" value="{{ old('mobile', $customer->mobile) }}" maxlength="10">
                    @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email address" value="{{ old('email', $customer->email) }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Location <span class="text-danger">*</span></label>
                    <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" placeholder="Enter city / location" value="{{ old('location', $customer->location) }}">
                    @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Firm Name</label>
                    <input type="text" name="firm_name" class="form-control @error('firm_name') is-invalid @enderror" placeholder="Enter firm / business name" value="{{ old('firm_name', $customer->firm_name) }}">
                    @error('firm_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">GST No.</label>
                    <input type="text" name="gst_number" class="form-control @error('gst_number') is-invalid @enderror" placeholder="Enter GST number" value="{{ old('gst_number', $customer->gst_number) }}">
                    @error('gst_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.customer.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Cancel</a>
                <button type="submit" class="btn btn-primary px-4" style="border-radius:.75rem;font-weight:600">Update Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
