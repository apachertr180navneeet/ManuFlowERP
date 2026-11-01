@extends('admin.layouts.app')
@section('page_title', 'Edit Salesman')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.salesman.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-pencil"></i> Edit Salesman</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('admin.salesman.update', $salesman->id) }}" method="POST">
            @csrf
            @method('PUT')
            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Personal Information</h6>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Salesman Name <span class="text-danger">*</span></label>
                    <input type="text" name="salesman_name" class="form-control @error('salesman_name') is-invalid @enderror" placeholder="Enter salesman name" value="{{ old('salesman_name', $salesman->salesman_name) }}">
                    @error('salesman_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" placeholder="Enter mobile number" value="{{ old('mobile', $salesman->mobile) }}" maxlength="10">
                    @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email address" value="{{ old('email', $salesman->email) }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Joining Date</label>
                    <input type="date" name="joining_date" class="form-control @error('joining_date') is-invalid @enderror" value="{{ old('joining_date', $salesman->joining_date ? $salesman->joining_date->format('Y-m-d') : '') }}">
                    @error('joining_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Address Details</h6>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="Enter address">{{ old('address', $salesman->address) }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" placeholder="Enter city" value="{{ old('city', $salesman->city) }}">
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" placeholder="Enter state" value="{{ old('state', $salesman->state) }}">
                    @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Pin Code</label>
                    <input type="text" name="pin_code" class="form-control @error('pin_code') is-invalid @enderror" placeholder="Enter pin code" value="{{ old('pin_code', $salesman->pin_code) }}">
                    @error('pin_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.salesman.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Cancel</a>
                <button type="submit" class="btn btn-primary px-4" style="border-radius:.75rem;font-weight:600">Update Salesman</button>
            </div>
        </form>
    </div>
</div>
@endsection
