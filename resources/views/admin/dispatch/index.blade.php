@extends('admin.layouts.app')
@section('page_title', 'Dispatch Orders')
@section('content')
<div class="data-card">
    <div class="table-header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2 gap-md-3 flex-grow-1 flex-md-grow-0">
                <div class="position-relative flex-grow-1" style="min-width:0;max-width:320px">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary" style="font-size:.75rem"></i>
                    <input type="text" id="searchInput" class="form-control ps-5" placeholder="Search challan, customer, transport..." style="border-radius:.75rem;font-size:.8125rem;padding:.375rem .75rem" value="{{ request('search') }}">
                </div>
                <button class="btn btn-primary btn-sm px-2 px-md-3" id="searchBtn" style="border-radius:.75rem;font-size:.75rem;font-weight:700">Search</button>
                <select id="statusFilter" class="form-select form-select-sm" style="border-radius:.75rem;font-size:.75rem;width:auto" onchange="filterStatus()">
                    <option value="">All Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="In Transit" {{ request('status') == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.dispatch.reports') }}" class="btn btn-info btn-sm px-3 flex-shrink-0" style="border-radius:.75rem;font-size:.75rem;font-weight:700">
                    <i class="bi bi-bar-chart me-1"></i> Reports
                </a>
                <a href="{{ route('admin.dispatch.create') }}" class="btn btn-success btn-sm px-3 flex-shrink-0" style="border-radius:.75rem;font-size:.75rem;font-weight:700">
                    <i class="bi bi-plus-lg me-1"></i> Add New Dispatch
                </a>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto" id="tableContainer">
        @include('admin.dispatch._table')
    </div>

    <div class="table-footer" id="paginationContainer">
        @include('admin.dispatch._pagination')
    </div>
</div>
@endsection
@section('script')
<script>
const csrfToken = '{{ csrf_token() }}';

function filterStatus() {
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchInput').value;
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (status) params.set('status', status);
    window.location.href = '{{ route("admin.dispatch.index") }}?' + params.toString();
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This dispatch order will be deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then(function(result) {
        if (result.isConfirmed) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = BASE_URL + '/admin/dispatch/' + id;
            form.style.display = 'none';
            var csrf = document.createElement('input');
            csrf.name = '_token';
            csrf.value = csrfToken;
            form.appendChild(csrf);
            var method = document.createElement('input');
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchBtn').addEventListener('click', function() {
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        const params = new URLSearchParams();
        if (search) params.set('search', search);
        if (status) params.set('status', status);
        window.location.href = '{{ route("admin.dispatch.index") }}?' + params.toString();
    });

    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') document.getElementById('searchBtn').click();
    });
});
</script>
@endsection
