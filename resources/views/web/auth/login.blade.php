@extends('admin.layouts.login_layout')
@section('content')
<div class="text-center mb-4">
    <h3 class="fw-bold text-dark">Company Login</h3>
    <p class="text-muted" style="font-size:.875rem">Sign in with your company credentials.</p>
</div>
<form action="{{ route('company.login.post') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <div class="position-relative">
            <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary">
                <i class="bi bi-person"></i>
            </span>
            <input type="text" class="form-control ps-5 @error('username') is-invalid @enderror" id="username" name="username" placeholder="Enter your username" value="{{ old('username') }}" autofocus>
            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="position-relative">
            <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-secondary">
                <i class="bi bi-lock"></i>
            </span>
            <input type="password" class="form-control ps-5 @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border:none;box-shadow:0 4px 16px rgba(79,70,229,.3)">Sign In to Company Portal</button>
</form>
@endsection
