@extends('admin.layouts.app')
@section('page_title', 'Change Password')
@section('content')
<div class="data-card">
    <div class="table-header">
        <h5 class="card-section-title"><i class="bi bi-key"></i> Change Password</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('company.password.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Current Password *</label>
                    <input type="password" name="old_password" class="form-control @error('old_password') is-invalid @enderror" placeholder="Enter current password">
                    @error('old_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <label class="form-label">New Password *</label>
                    <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="Enter new password">
                    @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm New Password *</label>
                    <input type="password" name="new_password_confirmation" class="form-control @error('new_password_confirmation') is-invalid @enderror" placeholder="Confirm new password">
                    @error('new_password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="pt-4 border-top mt-4">
                <button type="submit" class="btn btn-primary px-4">Update Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
