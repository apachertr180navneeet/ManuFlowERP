@extends('admin.layouts.app')

@section('page_title', 'Job Worker Assign')

@section('content')
<div class="data-card">
    <div class="table-header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2 gap-md-3 flex-grow-1 flex-md-grow-0">
                <div class="position-relative flex-grow-1" style="min-width:0;max-width:320px">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary" style="font-size:.875rem"></i>
                    <input type="text" id="searchInput" class="form-control ps-5" placeholder="Search here" style="border-radius:50rem;font-size:.8125rem;padding:.5rem 1rem" value="{{ request('search') }}">
                </div>
                <button class="btn btn-sm px-4" id="searchBtn" style="background-color:#5a54f9;border-color:#5a54f9;color:#fff;border-radius:50rem;font-size:.8125rem;font-weight:600;padding:.5rem 1.5rem;">Search</button>
            </div>
            <a href="{{ route('company.job-worker-assign.create') }}" class="btn btn-sm px-4 flex-shrink-0" style="background-color:#483d8b;border-color:#483d8b;color:#fff;border-radius:50rem;font-size:.8125rem;font-weight:600;padding:.5rem 1.5rem;">
                Add
            </a>
        </div>
    </div>

    <div class="overflow-x-auto" id="tableContainer">
        @include('admin.job-worker-assign._table')
    </div>

    <div class="table-footer" id="paginationContainer">
        @include('admin.job-worker-assign._pagination')
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        function fetch_data(page, query) {
            $.ajax({
                url: "{{ route('company.job-worker-assign.index') }}?page=" + page + "&search=" + query,
                success: function(data) {
                    $('#tableContainer').html(data.table);
                    $('#paginationContainer').html(data.pagination);
                }
            })
        }

        $(document).on('click', '.pagination a', function(event) {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            var query = $('#searchInput').val();
            fetch_data(page, query);
        });

        $('#searchBtn').on('click', function() {
            var query = $('#searchInput').val();
            fetch_data(1, query);
        });

        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            if (confirm('Are you sure you want to delete this job worker assign?')) {
                form.submit();
            }
        });
    });
</script>
@endsection
