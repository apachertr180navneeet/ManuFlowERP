@extends('admin.layouts.app')
@section('page_title', 'Cost Sheet Details')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.cost-sheet.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-calculator"></i> Cost Sheet Details</h5>
        <span class="badge bg-{{ $costSheet->status == 'Final' ? 'success' : 'warning' }} rounded-pill px-3 py-1 ms-2" style="font-size:.6875rem">{{ $costSheet->status }}</span>
    </div>
    <div class="p-4">
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">BOM No.</label>
                <div class="fw-bold text-dark" style="font-size:.875rem">{{ $costSheet->bom_no }}</div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">Date</label>
                <div class="fw-bold text-dark" style="font-size:.875rem">{{ $costSheet->date->format('d-m-Y') }}</div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">Product</label>
                <div class="fw-bold text-dark" style="font-size:.875rem">{{ $costSheet->product->item_name ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">Quantity</label>
                <div class="fw-bold text-dark" style="font-size:.875rem">{{ $costSheet->qty }}</div>
            </div>
        </div>

        <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Raw Material Consumption</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle mb-0" style="font-size:.8125rem">
                <thead><tr style="background:#f8fafc">
                    <th class="px-3 py-2 text-uppercase text-center" style="font-size:.6875rem;font-weight:700;color:#94a3b8">#</th>
                    <th class="px-3 py-2 text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Raw Material</th>
                    <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Required Qty</th>
                    <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">FIFO Rate</th>
                    <th class="px-3 py-2 text-end text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Amount</th>
                </tr></thead>
                <tbody>
                    @forelse($costSheet->items as $i => $item)
                    <tr>
                        <td class="px-3 text-center text-secondary">{{ $i + 1 }}</td>
                        <td class="px-3 fw-bold text-dark">{{ $item->rawMaterial->item_name ?? '—' }}</td>
                        <td class="px-3 text-center">{{ $item->required_qty }} {{ $item->unit_name }}</td>
                        <td class="px-3 text-center">{{ number_format($item->fifo_rate, 2) }}</td>
                        <td class="px-3 text-end fw-bold text-dark">₹ {{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-3 text-secondary">No items.</td></tr>
                    @endforelse
                </tbody>
                <tfoot><tr style="background:#f8fafc">
                    <td colspan="4" class="px-3 py-2 text-end fw-bold text-dark">Raw Material Total:</td>
                    <td class="px-3 py-2 text-end fw-bold text-dark">₹ {{ number_format($costSheet->raw_material_cost, 2) }}</td>
                </tr></tfoot>
            </table>
        </div>

        <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Factory Expenses</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle mb-0" style="font-size:.8125rem">
                <thead><tr style="background:#f8fafc">
                    <th class="px-3 py-2 text-uppercase text-center" style="font-size:.6875rem;font-weight:700;color:#94a3b8">#</th>
                    <th class="px-3 py-2 text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Expense</th>
                    <th class="px-3 py-2 text-end text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Amount</th>
                </tr></thead>
                <tbody>
                    @forelse($costSheet->expenses as $i => $exp)
                    <tr>
                        <td class="px-3 text-center text-secondary">{{ $i + 1 }}</td>
                        <td class="px-3 fw-bold text-dark">{{ $exp->expense_name }}</td>
                        <td class="px-3 text-end">₹ {{ number_format($exp->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center py-3 text-secondary">No expenses.</td></tr>
                    @endforelse
                </tbody>
                <tfoot><tr style="background:#f8fafc">
                    <td colspan="2" class="px-3 py-2 text-end fw-bold text-dark">Expense Total:</td>
                    <td class="px-3 py-2 text-end fw-bold text-dark">₹ {{ number_format($costSheet->expense_cost, 2) }}</td>
                </tr></tfoot>
            </table>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-3 rounded-3 border" style="background:#f8fafc">
                    <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">Raw Material Cost</label>
                    <div class="fw-bold text-dark" style="font-size:1.125rem">₹ {{ number_format($costSheet->raw_material_cost, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-3 border" style="background:#f8fafc">
                    <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">Factory Expense Cost</label>
                    <div class="fw-bold text-dark" style="font-size:1.125rem">₹ {{ number_format($costSheet->expense_cost, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-3 border" style="background:#f8fafc">
                    <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">Total Manufacturing Cost</label>
                    <div class="fw-bold text-dark" style="font-size:1.125rem">₹ {{ number_format($costSheet->total_cost, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-3 rounded-3 border" style="background:#f8fafc">
                    <label class="form-label text-secondary" style="font-size:.75rem;font-weight:600">Profit ({{ $costSheet->profit_percent }}%)</label>
                    <div class="fw-bold text-success" style="font-size:1.125rem">₹ {{ number_format($costSheet->profit_amount, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-3 border border-indigo-200" style="background:#eef2ff">
                    <label class="form-label text-indigo-600" style="font-size:.75rem;font-weight:600">UNIT PRICE</label>
                    <div class="fw-bold" style="font-size:1.5rem;color:#4338ca">₹ {{ number_format($costSheet->unit_price, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-3 border border-indigo-200" style="background:#eef2ff">
                    <label class="form-label text-indigo-600" style="font-size:.75rem;font-weight:600">SELLING PRICE</label>
                    <div class="fw-bold" style="font-size:1.5rem;color:#4338ca">₹ {{ number_format($costSheet->selling_price, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="pt-3 border-top d-flex gap-2 justify-content-end">
            <a href="{{ route('admin.cost-sheet.export-pdf', $costSheet->id) }}" class="btn btn-danger px-4" style="border-radius:.75rem;font-weight:600">
                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('admin.cost-sheet.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Back to List</a>
            @if($costSheet->status == 'Draft')
            <a href="{{ route('admin.cost-sheet.edit', $costSheet->id) }}" class="btn btn-primary px-4" style="border-radius:.75rem;font-weight:600">Edit</a>
            @endif
        </div>
    </div>
</div>
@endsection
