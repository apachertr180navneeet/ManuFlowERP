@extends('admin.layouts.app')
@section('page_title', 'Production Details')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.manufacturing.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-info-circle"></i> Production Details</h5>
    </div>
    <div class="p-4">
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">Production No.</label>
                <div class="fw-bold text-dark" style="font-size:.875rem">{{ $manufacturing->production_no }}</div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">Finished Item</label>
                <div class="fw-bold text-dark" style="font-size:.875rem">{{ $manufacturing->finishedItem->item_name ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">Production Qty</label>
                <div class="fw-bold text-dark" style="font-size:.875rem">{{ $manufacturing->production_qty }}</div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">Date</label>
                <div class="fw-bold text-dark" style="font-size:.875rem">{{ $manufacturing->production_date->format('d-m-Y') }}</div>
            </div>
        </div>

        <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Raw Materials Consumed</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle mb-0" style="font-size:.8125rem">
                <thead>
                    <tr style="background:#f8fafc">
                        <th class="px-3 py-2 text-uppercase text-center" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:40px">#</th>
                        <th class="px-3 py-2 text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Raw Material</th>
                        <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Required Qty</th>
                        <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Consumed Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($manufacturing->details as $i => $d)
                    <tr>
                        <td class="px-3 text-center text-secondary">{{ $i + 1 }}</td>
                        <td class="px-3 fw-bold text-dark">{{ $d->rawMaterial->item_name ?? '—' }}</td>
                        <td class="px-3 text-center">{{ $d->required_qty }}</td>
                        <td class="px-3 text-center fw-bold text-dark">{{ $d->consumed_qty }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-3 text-secondary">No details found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-3 border-top d-flex gap-2 justify-content-end">
            <a href="{{ route('admin.manufacturing.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Back to List</a>
        </div>
    </div>
</div>
@endsection
