@extends('admin.layouts.app')
@section('page_title', 'Manufacturing Cost Report')
@section('content')
<div class="data-card">
    <div class="table-header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <h5 class="card-section-title mb-0"><i class="bi bi-bar-chart"></i> Manufacturing Cost Report</h5>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.cost-sheet.report') }}" class="btn btn-light btn-sm px-3" style="border-radius:.75rem;font-size:.75rem;font-weight:700;border:1px solid #e2e8f0">Reset</a>
            </div>
        </div>
    </div>

    <div class="px-4 py-3 border-bottom" style="background:#f8fafc">
        <form method="GET" action="{{ route('admin.cost-sheet.report') }}" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label" style="font-size:.75rem;font-weight:600">From Date</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:.75rem;font-weight:600">To Date</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-size:.75rem;font-weight:600">Product</label>
                <select name="product_id" class="form-select form-select-sm">
                    <option value="">All Products</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->item_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:.75rem;font-weight:600">BOM No</label>
                <input type="text" name="bom_no" class="form-control form-control-sm" placeholder="Search BOM" value="{{ request('bom_no') }}">
            </div>
            <div class="col-md-1">
                <label class="form-label" style="font-size:.75rem;font-weight:600">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Final" {{ request('status') == 'Final' ? 'selected' : '' }}>Final</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3" style="border-radius:.75rem;font-size:.75rem;font-weight:700">Filter</button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto" id="reportTableContainer">
        @include('admin.cost_sheet.report._table')
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
