@extends('admin.layouts.app')
@section('page_title', 'Verify Email')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="data-card text-center p-5">
            <i class="bi bi-envelope-check text-primary" style="font-size:3rem"></i>
            <h4 class="fw-bold mt-3">Verify Your Email Address</h4>
            @if (session('resent'))
                <div class="alert alert-success small">A fresh verification link has been sent to your email.</div>
            @endif
            <p class="text-muted">Before proceeding, please check your email for a verification link.</p>
            <p class="text-muted">If you did not receive the email,</p>
            <form method="POST" action="{{ route('admin.verification.resend') }}">
                @csrf
                <button type="submit" class="btn btn-link p-0 fw-semibold">click here to request another</button>.
            </form>
        </div>
    </div>
</div>
@endsection