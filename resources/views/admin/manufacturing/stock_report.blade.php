@extends('admin.layouts.app')
@section('page_title', 'Stock Report')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <h5 class="card-section-title mb-0"><i class="bi bi-boxes"></i> Stock Report</h5>
    </div>
    <div class="p-4">
        <form method="GET" action="{{ route('admin.manufacturing.stock') }}" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Item</label>
                    <select name="item_id" class="form-select form-select-sm">
                        <option value="">All Items</option>
                        @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->item_name }} ({{ $item->short_code }}) - {{ $item->item_type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Item Type</label>
                    <select name="item_type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="Raw Material" {{ request('item_type') == 'Raw Material' ? 'selected' : '' }}>Raw Material</option>
                        <option value="Finished" {{ request('item_type') == 'Finished' ? 'selected' : '' }}>Finished</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm px-3" style="border-radius:.75rem;font-weight:600"><i class="bi bi-filter me-1"></i> Filter</button>
                    <a href="{{ route('admin.manufacturing.stock') }}" class="btn btn-light btn-sm px-3" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
                </div>
            </div>
        </form>

        <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Item-wise Stock Summary</h6>
        <div class="table-responsive mb-4">
            <table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
                <thead>
                    <tr class="text-uppercase text-secondary" style="font-size:.6875rem;font-weight:700;background:#f8fafc">
                        <th class="px-3 py-2">#</th>
                        <th class="px-3 py-2">Item</th>
                        <th class="px-3 py-2">Code</th>
                        <th class="px-3 py-2">Type</th>
                        <th class="px-3 py-2 text-end">Received</th>
                        <th class="px-3 py-2 text-end">Consumed</th>
                        <th class="px-3 py-2 text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($itemWiseStock as $i => $stock)
                    <tr>
                        <td class="px-3 text-secondary">{{ $i + 1 }}</td>
                        <td class="px-3 fw-bold text-dark">{{ $stock['item_name'] }}</td>
                        <td class="px-3 text-secondary">{{ $stock['short_code'] }}</td>
                        <td class="px-3">
                            <span class="badge bg-{{ $stock['item_type'] == 'Raw Material' ? 'warning' : 'info' }} rounded-pill px-3 py-1" style="font-size:.6875rem">{{ $stock['item_type'] }}</span>
                        </td>
                        <td class="px-3 text-end">{{ $stock['total_received'] }}</td>
                        <td class="px-3 text-end">{{ $stock['total_consumed'] }}</td>
                        <td class="px-3 text-end fw-bold text-{{ $stock['balance_qty'] > 0 ? 'dark' : 'danger' }}">{{ $stock['balance_qty'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-secondary">No stock data found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Batch-wise Stock Details</h6>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0" style="font-size:.8125rem">
                <thead>
                    <tr style="background:#f8fafc">
                        <th class="px-3 py-2 text-uppercase text-center" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:40px">#</th>
                        <th class="px-3 py-2 text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Item</th>
                        <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Purchase Date</th>
                        <th class="px-3 py-2 text-end text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Received</th>
                        <th class="px-3 py-2 text-end text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Consumed</th>
                        <th class="px-3 py-2 text-end text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $i => $batch)
                    <tr>
                        <td class="px-3 text-center text-secondary">{{ $i + 1 }}</td>
                        <td class="px-3 fw-bold text-dark">{{ $batch->item->item_name ?? '—' }} <span class="text-secondary" style="font-size:.6875rem">({{ $batch->item->short_code ?? '' }})</span></td>
                        <td class="px-3 text-center">{{ $batch->purchase_date->format('d-m-Y') }}</td>
                        <td class="px-3 text-end">{{ $batch->received_qty }}</td>
                        <td class="px-3 text-end">{{ $batch->consumed_qty }}</td>
                        <td class="px-3 text-end fw-bold text-{{ $batch->balance_qty > 0 ? 'dark' : 'danger' }}">{{ $batch->balance_qty }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-secondary">No batch records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
