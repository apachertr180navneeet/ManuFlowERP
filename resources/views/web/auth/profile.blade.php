@extends('admin.layouts.app')
@section('page_title', 'My Profile')
@section('style')
<style>
    .avatar-preview { width:100px; height:100px; object-fit:cover; border-radius:50%; border:2px solid #e2e8f0; }
    .avatar-preview-initials { width:100px; height:100px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:700; color:#4f46e5; background:rgba(99,102,241,.1); border:2px solid #e2e8f0; }
</style>
@endsection
@section('content')
<div class="data-card">
    <div class="table-header">
        <h5 class="card-section-title"><i class="bi bi-person"></i> Profile Information</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('company.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" placeholder="First name" value="{{old('first_name', $user->first_name)}}">
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" placeholder="Last name" value="{{old('last_name', $user->last_name)}}">
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number *</label>
                    <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Phone number" value="{{old('phone', $user->phone)}}">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email address" value="{{old('email', $user->email)}}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Profile Picture</label>
                    <input type="file" name="avatar" accept="image/*" class="form-control @error('avatar') is-invalid @enderror" onchange="previewAvatar(this)">
                    @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <div class="position-relative">
                        @if($user->avatar && file_exists(public_path($user->avatar)))
                            <img src="{{asset($user->avatar)}}" class="rounded-circle border" id="user-image" style="width:100px;height:100px;object-fit:cover">
                        @else
                            <div class="rounded-circle border d-flex align-items-center justify-content-center bg-light" id="user-image" style="width:100px;height:100px;font-size:2rem;font-weight:700;color:#4f46e5;background:rgba(99,102,241,.1)!important">
                                {{ strtoupper(substr(($user->full_name ?? 'A'), 0, 2)) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="pt-4 border-top mt-4">
                <button type="submit" class="btn btn-primary px-4">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
<script>
function previewAvatar(input) {
    const container = document.getElementById('user-image').parentElement;
    if (input.files && input.files[0]) {
        container.innerHTML = '<img src="' + window.URL.createObjectURL(input.files[0]) + '" class="avatar-preview" id="user-image">';
    }
}
</script>
@endsection
