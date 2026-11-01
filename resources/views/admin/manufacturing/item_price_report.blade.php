@extends('admin.layouts.app')
@section('page_title', 'Item Wise Price by Month')
@section('content')
<div class="data-card">
    <div class="table-header">
        <h5 class="card-section-title mb-0"><i class="bi bi-table"></i> Item Wise Price by Month</h5>
    </div>

    <div class="px-4 py-3 border-bottom" style="background:#f8fafc">
        <form method="GET" action="{{ route('admin.manufacturing.item-price') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label" style="font-size:.75rem;font-weight:600">Item</label>
                <select name="item_id" class="form-select form-select-sm">
                    <option value="">All Items</option>
                    @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->item_name }} ({{ $item->item_type }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:.75rem;font-weight:600">Item Type</label>
                <select name="item_type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="Raw Material" {{ request('item_type') == 'Raw Material' ? 'selected' : '' }}>Raw Material</option>
                    <option value="Finished" {{ request('item_type') == 'Finished' ? 'selected' : '' }}>Finished</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:.75rem;font-weight:600">From Date</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:.75rem;font-weight:600">To Date</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3" style="border-radius:.75rem;font-size:.75rem;font-weight:700">Filter</button>
                <a href="{{ route('admin.manufacturing.item-price') }}" class="btn btn-light btn-sm px-3" style="border-radius:.75rem;font-size:.75rem;font-weight:700;border:1px solid #e2e8f0">Reset</a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        @if(empty($priceData))
        <div class="p-4 text-center text-secondary">No purchase data found for selected filters.</div>
        @else
        <table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
            <thead>
                <tr class="text-uppercase text-secondary" style="font-size:.6875rem;letter-spacing:.05em;font-weight:700;background:#f8fafc">
                    <th class="px-4 py-3">Item</th>
                    <th class="px-4 py-3 text-center">Type</th>
                    @foreach($months as $mKey => $mLabel)
                    <th class="px-3 py-3 text-center">{{ $mLabel }}</th>
                    @endforeach
                    <th class="px-3 py-3 text-center" style="background:#eef2ff">Avg Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($priceData as $itemId => $data)
                <tr>
                    <td class="px-4 fw-bold text-dark">{{ $data['item_name'] }}</td>
                    <td class="px-4 text-center text-secondary">{{ $data['item_type'] }}</td>
                    @foreach($months as $mKey => $mLabel)
                    <td class="px-3 text-center">
                        @if(isset($data['months'][$mKey]))
                            <span class="fw-bold">₹ {{ number_format($data['months'][$mKey]['rate'], 2) }}</span>
                            <br><small class="text-secondary">({{ $data['months'][$mKey]['qty'] }} qty)</small>
                        @else
                            <span class="text-secondary">—</span>
                        @endif
                    </td>
                    @endforeach
                    <td class="px-3 text-center fw-bold" style="background:#eef2ff;color:#4338ca">₹ {{ number_format($data['avg_rate'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
