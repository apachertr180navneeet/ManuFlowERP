<table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
    <thead>
        <tr class="text-uppercase text-secondary" style="font-size:.6875rem;letter-spacing:.05em;font-weight:700;background:#f8fafc">
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3">BOM No</th>
            <th class="px-4 py-3">Product</th>
            <th class="px-4 py-3">Raw Material</th>
            <th class="px-4 py-3 text-center">Consumed Qty</th>
            <th class="px-4 py-3 text-end">Rate</th>
            <th class="px-4 py-3 text-end">Amount</th>
        </tr>
    </thead>
    <tbody>
        @forelse($reports as $item)
        <tr>
            <td class="px-4">{{ $item->costSheet?->date?->format('d-m-Y') ?? '—' }}</td>
            <td class="px-4 fw-bold text-dark">{{ $item->costSheet?->bom_no ?? '—' }}</td>
            <td class="px-4">{{ $item->costSheet?->product?->item_name ?? '—' }}</td>
            <td class="px-4">{{ $item->rawMaterial?->item_name ?? '—' }}</td>
            <td class="px-4 text-center fw-bold">{{ $item->required_qty }} {{ $item->unit_name }}</td>
            <td class="px-4 text-end">₹ {{ number_format($item->fifo_rate, 2) }}</td>
            <td class="px-4 text-end fw-bold">₹ {{ number_format($item->amount, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-5 text-center text-secondary">No records found.</td></tr>
        @endforelse
    </tbody>
</table>

@if(!empty($stockSummary) && count($stockSummary) > 0)
<div class="px-4 py-3 mt-3">
    <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Stock Summary</h6>
    <table class="table table-bordered align-middle mb-0" style="font-size:.8125rem">
        <thead>
            <tr style="background:#f8fafc">
                <th class="px-3 py-2 text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Material</th>
                <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Opening Stock</th>
                <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Consumed Stock</th>
                <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Closing Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockSummary as $matId => $stock)
            <tr>
                <td class="px-3 fw-bold text-dark">{{ $stock['name'] }}</td>
                <td class="px-3 text-center">{{ $stock['opening'] }}</td>
                <td class="px-3 text-center">{{ $stock['consumed'] }}</td>
                <td class="px-3 text-center fw-bold">{{ $stock['closing'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
