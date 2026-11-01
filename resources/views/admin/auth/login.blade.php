@extends('admin.layouts.login_layout')
@section('content')
<div class="text-center mb-4">
    <h3 class="fw-bold text-dark">Sign In</h3>
    <p class="text-muted" style="font-size:.875rem">Please input your system credentials to access the ERP suite.</p>
</div>
<form action="{{ route('admin.login.post') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <div class="position-relative">
            <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary">
                <i class="bi bi-envelope"></i>
            </span>
            <input type="email" class="form-control ps-5 @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label mb-0">Password</label>
        </div>
        <div class="position-relative">
            <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary">
                <i class="bi bi-lock"></i>
            </span>
            <input type="password" class="form-control ps-5 @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border:none;box-shadow:0 4px 16px rgba(79,70,229,.3)">Sign In to System</button>
</form>
@endsection