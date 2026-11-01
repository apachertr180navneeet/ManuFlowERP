@extends('admin.layouts.app')
@section('page_title', 'Reconciliation Details')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.stock-reconciliation.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-clipboard-check"></i> Reconciliation Details</h5>
        <span class="badge bg-{{ $stockReconciliation->status == 'Posted' ? 'success' : 'warning' }} rounded-pill px-3 py-1 ms-2" style="font-size:.6875rem">{{ $stockReconciliation->status }}</span>
    </div>
    <div class="p-4">
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">Reference No.</label>
                <div class="fw-bold text-dark" style="font-size:.875rem">{{ $stockReconciliation->reference_no }}</div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">Date</label>
                <div class="fw-bold text-dark" style="font-size:.875rem">{{ $stockReconciliation->reconciliation_date->format('d-m-Y') }}</div>
            </div>
            <div class="col-md-6">
                <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">Notes</label>
                <div class="fw-bold text-dark" style="font-size:.875rem">{{ $stockReconciliation->notes ?? '—' }}</div>
            </div>
        </div>

        <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Reconciliation Items</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle mb-0" style="font-size:.8125rem">
                <thead><tr style="background:#f8fafc">
                    <th class="px-3 py-2 text-uppercase text-center" style="font-size:.6875rem;font-weight:700;color:#94a3b8">#</th>
                    <th class="px-3 py-2 text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Item</th>
                    <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">System Qty</th>
                    <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Physical Qty</th>
                    <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Difference</th>
                    <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Rate</th>
                    <th class="px-3 py-2 text-end text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Adjustment Amt</th>
                    <th class="px-3 py-2 text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Remarks</th>
                </tr></thead>
                <tbody>
                    @forelse($stockReconciliation->items as $i => $item)
                    <tr>
                        <td class="px-3 text-center text-secondary">{{ $i + 1 }}</td>
                        <td class="px-3 fw-bold text-dark">{{ $item->item->item_name ?? '—' }}</td>
                        <td class="px-3 text-center">{{ $item->system_qty }}</td>
                        <td class="px-3 text-center">{{ $item->physical_qty }}</td>
                        <td class="px-3 text-center fw-bold {{ $item->difference_qty < 0 ? 'text-danger' : ($item->difference_qty > 0 ? 'text-success' : '') }}">{{ $item->difference_qty > 0 ? '+' : '' }}{{ $item->difference_qty }}</td>
                        <td class="px-3 text-center">{{ number_format($item->rate, 2) }}</td>
                        <td class="px-3 text-end fw-bold">₹ {{ number_format($item->adjustment_amount, 2) }}</td>
                        <td class="px-3">{{ $item->remarks ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-3 text-secondary">No items.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-3 border-top d-flex gap-2 justify-content-end">
            <a href="{{ route('admin.stock-reconciliation.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Back to List</a>
        </div>
    </div>
</div>
@endsection
