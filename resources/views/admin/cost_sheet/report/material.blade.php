@extends('admin.layouts.app')
@section('page_title', 'Raw Material Consumption Report')
@section('content')
<div class="data-card">
    <div class="table-header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <h5 class="card-section-title mb-0"><i class="bi bi-boxes"></i> Raw Material Consumption Report</h5>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.cost-sheet.material-report') }}" class="btn btn-light btn-sm px-3" style="border-radius:.75rem;font-size:.75rem;font-weight:700;border:1px solid #e2e8f0">Reset</a>
            </div>
        </div>
    </div>

    <div class="px-4 py-3 border-bottom" style="background:#f8fafc">
        <form method="GET" action="{{ route('admin.cost-sheet.material-report') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label" style="font-size:.75rem;font-weight:600">Raw Material</label>
                <select name="raw_material_id" class="form-select form-select-sm">
                    <option value="">All Materials</option>
                    @foreach($rawMaterials as $rm)
                    <option value="{{ $rm->id }}" {{ request('raw_material_id') == $rm->id ? 'selected' : '' }}>{{ $rm->item_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-size:.75rem;font-weight:600">From Date</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-size:.75rem;font-weight:600">To Date</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3" style="border-radius:.75rem;font-size:.75rem;font-weight:700">Filter</button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto" id="reportTableContainer">
        @include('admin.cost_sheet.report.material_table')
    </div>

    <div class="table-footer" id="reportPaginationContainer">
        @include('admin.cost_sheet.report._pagination')
    </div>
</div>
@endsection
@section('script')
<script>
const csrfToken = '{{ csrf_token() }}';
</script>
@endsection
