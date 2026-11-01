@extends('web.layouts.app')
@section('content')
<section style="background:linear-gradient(135deg,#0f172a,#1e1b4b);padding:6rem 0">
    <div class="container text-center">
        <span class="d-inline-flex align-items-center px-3 py-1 rounded-pill mb-3" style="background:rgba(99,102,241,.1);color:#a5b4fc;border:1px solid rgba(99,102,241,.2);font-size:.75rem;font-weight:600">
            Enterprise Resource Planning Suite
        </span>
        <h1 class="display-4 fw-bold text-white mb-3" style="letter-spacing:-.02em">Welcome to {{ config('app.name') }}</h1>
        <p class="lead mb-4" style="color:#94a3b8;max-width:600px;margin:0 auto">Manage your entire operations in one central hub. Access premium analytics, secure databases, and user management portals.</p>
        <a href="{{ route('admin.login') }}" class="btn btn-lg px-5 py-2.5 rounded-3 fw-bold" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;box-shadow:0 8px 24px rgba(79,70,229,.35)">Get Started</a>
    </div>
</section>
<section class="py-5" style="background:#f8fafc">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="stat-card h-100 text-center p-4">
                    <div class="stat-icon mx-auto mb-3" style="width:56px;height:56px;font-size:1.5rem"><i class="bi bi-speedometer2"></i></div>
                    <h5 class="fw-bold">Dashboard</h5>
                    <p class="text-muted small mb-0">Monitor all your business activities and metrics in one unified dashboard.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card h-100 text-center p-4">
                    <div class="stat-icon mx-auto mb-3" style="width:56px;height:56px;font-size:1.5rem"><i class="bi bi-people"></i></div>
                    <h5 class="fw-bold">User Management</h5>
                    <p class="text-muted small mb-0">Manage companies, vendors, customers, and sales personnel efficiently.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card h-100 text-center p-4">
                    <div class="stat-icon mx-auto mb-3" style="width:56px;height:56px;font-size:1.5rem"><i class="bi bi-shield-check"></i></div>
                    <h5 class="fw-bold">Secure & Reliable</h5>
                    <p class="text-muted small mb-0">Enterprise-grade security with role-based access and data protection.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection