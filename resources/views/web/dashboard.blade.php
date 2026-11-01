@extends('admin.layouts.app')
@section('page_title', 'Company Dashboard')
@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="data-card">
            <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                <div class="p-2 rounded" style="background:linear-gradient(135deg,#059669,#10b981)">
                    <i class="bi bi-building fs-5 text-white"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">{{ $company->company_name ?? 'Company Dashboard' }}</h5>
                    <small class="text-secondary">{{ $company->owner_name ?? '' }}</small>
                </div>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <div class="stat-card mb-0">
                            <div class="stat-header">
                                <h3>Vendors</h3>
                                <div class="stat-icon"><i class="bi bi-buildings"></i></div>
                            </div>
                            <div class="stat-body">
                                <div class="stat-row"><span>Active</span><span class="text-success">0</span></div>
                                <hr>
                                <div class="stat-row"><span>In-Active</span><span class="text-danger">0</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card mb-0">
                            <div class="stat-header">
                                <h3>Customers</h3>
                                <div class="stat-icon"><i class="bi bi-people"></i></div>
                            </div>
                            <div class="stat-body">
                                <div class="stat-row"><span>Active</span><span class="text-success">0</span></div>
                                <hr>
                                <div class="stat-row"><span>In-Active</span><span class="text-danger">0</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card mb-0">
                            <div class="stat-header">
                                <h3>Sales Persons</h3>
                                <div class="stat-icon"><i class="bi bi-person-badge"></i></div>
                            </div>
                            <div class="stat-body">
                                <div class="stat-row"><span>Active</span><span class="text-success">0</span></div>
                                <hr>
                                <div class="stat-row"><span>In-Active</span><span class="text-danger">0</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card mb-0">
                            <div class="stat-header">
                                <h3>Items</h3>
                                <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
                            </div>
                            <div class="stat-body">
                                <div class="stat-row"><span>Total</span><span class="text-primary">0</span></div>
                                <hr>
                                <div class="stat-row"><span>Categories</span><span class="text-primary">0</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
