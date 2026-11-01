{{-- TODO: $reports is a paginated collection — sums below are per-page only. Pass $totals from controller for full sums across all pages. --}}
@php
$totalMaterial = $reports->sum('raw_material_cost');
$totalExpense = $reports->sum('expense_cost');
$totalCost = $reports->sum('total_cost');
$totalProfit = $reports->sum('profit_amount');
$totalSelling = $reports->sum('selling_price');
@endphp
<table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
    <thead>
        <tr class="text-uppercase text-secondary" style="font-size:.6875rem;letter-spacing:.05em;font-weight:700;background:#f8fafc">
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3">BOM No</th>
            <th class="px-4 py-3">Product</th>
            <th class="px-4 py-3 text-center">Qty</th>
            <th class="px-4 py-3 text-end">Material Cost</th>
            <th class="px-4 py-3 text-end">Expense Cost</th>
            <th class="px-4 py-3 text-end">Total Cost</th>
            <th class="px-4 py-3 text-end">Profit</th>
            <th class="px-4 py-3 text-end">Selling Price</th>
            <th class="px-4 py-3 text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($reports as $r)
        <tr>
            <td class="px-4">{{ $r->date->format('d-m-Y') }}</td>
            <td class="px-4 fw-bold text-dark">{{ $r->bom_no }}</td>
            <td class="px-4">{{ $r->product->item_name ?? '—' }}</td>
            <td class="px-4 text-center">{{ $r->qty }}</td>
            <td class="px-4 text-end">₹ {{ number_format($r->raw_material_cost, 2) }}</td>
            <td class="px-4 text-end">₹ {{ number_format($r->expense_cost, 2) }}</td>
            <td class="px-4 text-end fw-bold">₹ {{ number_format($r->total_cost, 2) }}</td>
            <td class="px-4 text-end text-success">₹ {{ number_format($r->profit_amount, 2) }} ({{ $r->profit_percent }}%)</td>
            <td class="px-4 text-end fw-bold text-indigo-700" style="color:#4338ca">₹ {{ number_format($r->selling_price, 2) }}</td>
            <td class="px-4 text-center">
                <span class="badge bg-{{ $r->status == 'Final' ? 'success' : 'warning' }} rounded-pill px-3 py-1" style="font-size:.6875rem">{{ $r->status }}</span>
            </td>
        </tr>
        @empty
        <tr><td colspan="10" class="px-4 py-5 text-center text-secondary">No records found.</td></tr>
        @endforelse
    </tbody>
    @if($reports->count())
    <tfoot>
        <tr style="background:#f8fafc;font-weight:700">
            <td colspan="4" class="px-4 py-3 text-end text-dark">Total:</td>
            <td class="px-4 py-3 text-end text-dark">₹ {{ number_format($totalMaterial, 2) }}</td>
            <td class="px-4 py-3 text-end text-dark">₹ {{ number_format($totalExpense, 2) }}</td>
            <td class="px-4 py-3 text-end text-dark">₹ {{ number_format($totalCost, 2) }}</td>
            <td class="px-4 py-3 text-end text-dark">₹ {{ number_format($totalProfit, 2) }}</td>
            <td class="px-4 py-3 text-end text-dark">₹ {{ number_format($totalSelling, 2) }}</td>
            <td class="px-4 py-3"></td>
        </tr>
    </tfoot>
    @endif
</table>
