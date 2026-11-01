@extends('admin.layouts.app')
@section('page_title', 'Change Password')
@section('content')
<div class="data-card">
    <div class="table-header">
        <h5 class="card-section-title"><i class="bi bi-key"></i> Update Password</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('admin.update.password') }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <label class="form-label">Old Password</label>
                <input name="old_password" type="password" class="form-control @error('old_password') is-invalid @enderror" placeholder="Enter old password">
                @error('old_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6"></div>
            <div class="col-md-6">
                <label class="form-label">New Password</label>
                <input name="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" placeholder="Enter new password">
                @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirm New Password</label>
                <input name="new_password_confirmation" type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" placeholder="Confirm new password">
                @error('new_password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 pt-3">
                <button type="submit" class="btn btn-primary px-4">Update Password</button>
            </div>
        </form>
    </div>
</div>
@endsection