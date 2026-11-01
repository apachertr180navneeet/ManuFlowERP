@extends('admin.layouts.app')
@section('page_title', 'Expense Master')
@section('content')
<div class="data-card">
    <div class="table-header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2 gap-md-3 flex-grow-1 flex-md-grow-0">
                <div class="position-relative flex-grow-1" style="min-width:0;max-width:320px">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary" style="font-size:.75rem"></i>
                    <input type="text" id="searchInput" class="form-control ps-5" placeholder="Search expense..." style="border-radius:.75rem;font-size:.8125rem;padding:.375rem .75rem" value="{{ request('search') }}">
                </div>
                <button class="btn btn-primary btn-sm px-2 px-md-3" id="searchBtn" style="border-radius:.75rem;font-size:.75rem;font-weight:700">Search</button>
            </div>
            <a href="{{ route('admin.expense.create') }}" class="btn btn-success btn-sm px-3 flex-shrink-0" style="border-radius:.75rem;font-size:.75rem;font-weight:700">
                <i class="bi bi-plus-lg me-1"></i> Add Expense
            </a>
        </div>
    </div>

    <div class="overflow-x-auto" id="tableContainer">
        @include('admin.expense._table')
    </div>

    <div class="table-footer" id="paginationContainer">
        @include('admin.expense._pagination')
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
    postForm(BASE_URL + '/admin/expense/' + id + '/toggle-status', { _token: csrfToken }, function(res) {
        if (res.success) {
            el.className = 'status-badge ' + res.status;
            el.textContent = res.status.charAt(0).toUpperCase() + res.status.slice(1);
            Swal.fire({ title: 'Status Updated', text: 'Expense status changed to ' + res.status, icon: 'success', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
        }
    });
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This expense will be deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then(function(result) {
        if (result.isConfirmed) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = BASE_URL + '/admin/expense/' + id;
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
        window.location.href = '{{ route("admin.expense.index") }}?search=' + encodeURIComponent(search);
    });

    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') document.getElementById('searchBtn').click();
    });
});
</script>
@endsection
