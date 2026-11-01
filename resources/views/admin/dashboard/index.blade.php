@extends('admin.layouts.app')
@section('page_title', 'Dashboard Overview')
@section('content')
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-header">
                <h3>Vendors</h3>
                <div class="stat-icon"><i class="bi bi-buildings"></i></div>
            </div>
            <div class="stat-body">
                <div class="stat-row">
                    <span>Active</span>
                    <span class="text-success">0</span>
                </div>
                <hr>
                <div class="stat-row">
                    <span>In-Active</span>
                    <span class="text-danger">0</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-header">
                <h3>Customers</h3>
                <div class="stat-icon"><i class="bi bi-people"></i></div>
            </div>
            <div class="stat-body">
                <div class="stat-row">
                    <span>Active</span>
                    <span class="text-success">0</span>
                </div>
                <hr>
                <div class="stat-row">
                    <span>In-Active</span>
                    <span class="text-danger">0</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-header">
                <h3>Sales Persons</h3>
                <div class="stat-icon"><i class="bi bi-person-badge"></i></div>
            </div>
            <div class="stat-body">
                <div class="stat-row">
                    <span>Active</span>
                    <span class="text-success">0</span>
                </div>
                <hr>
                <div class="stat-row">
                    <span>In-Active</span>
                    <span class="text-danger">0</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-header">
                <h3>Items</h3>
                <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
            </div>
            <div class="stat-body">
                <div class="stat-row">
                    <span>Total</span>
                    <span class="text-primary">0</span>
                </div>
                <hr>
                <div class="stat-row">
                    <span>Categories</span>
                    <span class="text-primary">0</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-6">
        <div class="data-card">
            <div class="d-flex align-items-center gap-3 px-4 py-3" style="background:linear-gradient(135deg,#2563eb,#4f46e5)">
                <div class="p-2 rounded" style="background:rgba(255,255,255,.15)">
                    <i class="bi bi-clipboard-check fs-5 text-white"></i>
                </div>
                <h5 class="fw-bold text-white mb-0">Job Issue Overview</h5>
            </div>
            <div class="row g-0">
                <div class="col-6 border-end">
                    <div class="p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="d-inline-block rounded-circle" style="width:8px;height:8px;background:#3b82f6"></span>
                            <h6 class="fw-bold text-dark text-uppercase mb-0" style="font-size:.6875rem;letter-spacing:.05em">Month Job Issue</h6>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-2" style="background:#f8fafc">
                            <span class="fw-semibold text-secondary" style="font-size:.75rem">Total Assign</span>
                            <span class="fw-extrabold text-dark" style="font-size:1rem">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-2" style="background:#ecfdf5">
                            <span class="fw-semibold text-emerald-700" style="font-size:.75rem">Complete Assign</span>
                            <span class="fw-extrabold text-success" style="font-size:1rem">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background:#fffbeb">
                            <span class="fw-semibold text-amber-700" style="font-size:.75rem">Process Assign</span>
                            <span class="fw-extrabold text-warning" style="font-size:1rem">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="d-inline-block rounded-circle" style="width:8px;height:8px;background:#6366f1"></span>
                            <h6 class="fw-bold text-dark text-uppercase mb-0" style="font-size:.6875rem;letter-spacing:.05em">Today's Job Issue</h6>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-2" style="background:#f8fafc">
                            <span class="fw-semibold text-secondary" style="font-size:.75rem">Total Assign</span>
                            <span class="fw-extrabold text-dark" style="font-size:1rem">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-2" style="background:#ecfdf5">
                            <span class="fw-semibold text-emerald-700" style="font-size:.75rem">Complete Assign</span>
                            <span class="fw-extrabold text-success" style="font-size:1rem">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background:#fffbeb">
                            <span class="fw-semibold text-amber-700" style="font-size:.75rem">Process Assign</span>
                            <span class="fw-extrabold text-warning" style="font-size:1rem">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="data-card">
            <div class="d-flex align-items-center gap-3 px-4 py-3" style="background:linear-gradient(135deg,#7c3aed,#d946ef)">
                <div class="p-2 rounded" style="background:rgba(255,255,255,.15)">
                    <i class="bi bi-truck fs-5 text-white"></i>
                </div>
                <h5 class="fw-bold text-white mb-0">Dispatch Order Overview</h5>
            </div>
            <div class="row g-0">
                <div class="col-6 border-end">
                    <div class="p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="d-inline-block rounded-circle" style="width:8px;height:8px;background:#8b5cf6"></span>
                            <h6 class="fw-bold text-dark text-uppercase mb-0" style="font-size:.6875rem;letter-spacing:.05em">Month Dispatch</h6>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-2" style="background:#f8fafc">
                            <span class="fw-semibold text-secondary" style="font-size:.75rem">Total Order</span>
                            <span class="fw-extrabold text-dark" style="font-size:1rem">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-2" style="background:#ecfdf5">
                            <span class="fw-semibold text-emerald-700" style="font-size:.75rem">Complete Order</span>
                            <span class="fw-extrabold text-success" style="font-size:1rem">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background:#fef2f2">
                            <span class="fw-semibold text-rose-700" style="font-size:.75rem">Pending Order</span>
                            <span class="fw-extrabold text-danger" style="font-size:1rem">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="d-inline-block rounded-circle" style="width:8px;height:8px;background:#d946ef"></span>
                            <h6 class="fw-bold text-dark text-uppercase mb-0" style="font-size:.6875rem;letter-spacing:.05em">Today Dispatch</h6>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-2" style="background:#f8fafc">
                            <span class="fw-semibold text-secondary" style="font-size:.75rem">Total Order</span>
                            <span class="fw-extrabold text-dark" style="font-size:1rem">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-2" style="background:#ecfdf5">
                            <span class="fw-semibold text-emerald-700" style="font-size:.75rem">Complete Order</span>
                            <span class="fw-extrabold text-success" style="font-size:1rem">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background:#fef2f2">
                            <span class="fw-semibold text-rose-700" style="font-size:.75rem">Pending Order</span>
                            <span class="fw-extrabold text-danger" style="font-size:1rem">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection