@extends('admin.layouts.app')
@section('page_title', 'Edit Job Worker')
@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="data-card p-4 p-md-5">
            <div class="d-flex align-items-center gap-3 mb-4 mb-md-5">
                <a href="{{ route('company.job-worker.index') }}" class="btn btn-light btn-sm text-secondary px-3" style="border-radius:.75rem">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
                <h5 class="fw-bold mb-0">Edit Job Worker</h5>
            </div>

            <form action="{{ route('company.job-worker.update', $jobWorker->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Job Worker Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $jobWorker->name) }}" placeholder="Enter Name" required>
                        @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone', $jobWorker->phone) }}" placeholder="Enter Phone">
                        @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $jobWorker->email) }}" placeholder="Enter Email">
                        @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">City</label>
                        <input type="text" class="form-control" name="city" value="{{ old('city', $jobWorker->city) }}" placeholder="Enter City">
                        @error('city')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Address</label>
                        <input type="text" class="form-control" name="address" value="{{ old('address', $jobWorker->address) }}" placeholder="Enter Address">
                        @error('address')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">State</label>
                        <input type="text" class="form-control" name="state" value="{{ old('state', $jobWorker->state) }}" placeholder="Enter State">
                        @error('state')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Pin code</label>
                        <input type="text" class="form-control" name="pin_code" value="{{ old('pin_code', $jobWorker->pin_code) }}" placeholder="Enter Pin Code">
                        @error('pin_code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-5">
                    <a href="{{ route('company.job-worker.index') }}" class="btn btn-sm px-4" style="background-color:#f8f9fa;color:#000;border-radius:50rem;font-weight:600;padding:.5rem 1.5rem;">Cancel</a>
                    <button type="submit" class="btn btn-sm px-4" style="background-color:#5a54f9;border-color:#5a54f9;color:#fff;border-radius:50rem;font-weight:600;padding:.5rem 1.5rem;">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
