@extends('admin.layouts.app')

@section('page_title', 'Job Work Inward')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="data-card">
            <div class="table-header">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2 gap-md-3 flex-grow-1 flex-md-grow-0">
                        <div class="input-group" style="max-width: 300px;">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-secondary"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Search here">
                            <button class="btn btn-primary" type="button" id="searchBtn">Search</button>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('company.job-work-inward.create') }}" class="btn btn-primary btn-sm mt-2 mt-md-0">
                            Add
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive" id="tableContainer">
                    @include('admin.job-work-inward._table')
                </div>
                <div class="table-footer" id="paginationContainer">
                    @include('admin.job-work-inward._pagination')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        function fetch_data(page, query) {
            $.ajax({
                url: "{{ route('company.job-work-inward.index') }}?page=" + page + "&search=" + query,
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
            if (confirm('Are you sure you want to delete this job work inward?')) {
                form.submit();
            }
        });
    });
</script>
@endsection
