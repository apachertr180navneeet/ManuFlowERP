@extends('admin.layouts.app')
@section('page_title', 'Company Master')
@section('content')
<div class="data-card">
    <div class="table-header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2 gap-md-3 flex-grow-1 flex-md-grow-0">
                <div class="position-relative flex-grow-1" style="min-width:0;max-width:320px">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary" style="font-size:.75rem"></i>
                    <input type="text" id="searchInput" class="form-control ps-5" placeholder="Search company, owner, email, city..." style="border-radius:.75rem;font-size:.8125rem;padding:.375rem .75rem" value="{{ request('search') }}">
                </div>
                <button class="btn btn-primary btn-sm px-2 px-md-3" id="searchBtn" style="border-radius:.75rem;font-size:.75rem;font-weight:700">Search</button>
            </div>
            <a href="{{ route('admin.company.create') }}" class="btn btn-success btn-sm px-3 flex-shrink-0" style="border-radius:.75rem;font-size:.75rem;font-weight:700">
                <i class="bi bi-plus-lg me-1"></i> Add Company
            </a>
        </div>
    </div>

    <div class="overflow-x-auto" id="tableContainer">
        @include('admin.company._table')
    </div>

    <div class="table-footer" id="paginationContainer">
        @include('admin.company._pagination')
    </div>
</div>


@endsection
@section('script')
<script>
const csrfToken = '{{ csrf_token() }}';

function postForm(url, data, onSuccess) {
    const fd = new FormData();
    Object.entries(data).forEach(([k, v]) => fd.append(k, v));
    fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: fd })
        .then(r => r.json()).then(onSuccess).catch(() =>
            Swal.fire({ title: 'Error', text: 'Something went wrong.', icon: 'error', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 })
        );
}

function toggleStatus(el, id) {
    postForm(BASE_URL + '/admin/company/' + id + '/toggle-status', { _token: csrfToken }, function(res) {
        if (res.success) {
            el.className = 'status-badge ' + res.status;
            el.textContent = res.status.charAt(0).toUpperCase() + res.status.slice(1);
            Swal.fire({ title: 'Status Updated', text: 'Company status changed to ' + res.status, icon: 'success', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchBtn').addEventListener('click', function() {
        const search = document.getElementById('searchInput').value;
        window.location.href = '{{ route("admin.company.index") }}?search=' + encodeURIComponent(search);
    });

    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') document.getElementById('searchBtn').click();
    });

});
</script>
@endsection