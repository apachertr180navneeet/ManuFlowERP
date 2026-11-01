@php
$statusClass = function($s) {
    return $s === 'Final' ? 'success' : 'warning';
};
@endphp
<table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
    <thead>
        <tr class="text-uppercase text-secondary" style="font-size:.6875rem;letter-spacing:.05em;font-weight:700;background:#f8fafc">
            <th class="px-4 py-3 text-center" style="width:50px">#</th>
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3">BOM No</th>
            <th class="px-4 py-3">Product</th>
            <th class="px-4 py-3 text-center">Qty</th>
            <th class="px-4 py-3 text-end">Final Amount</th>
            <th class="px-4 py-3 text-center" style="width:100px">Status</th>
            <th class="px-4 py-3 text-center" style="width:80px">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($costSheets as $cs)
        <tr>
            <td class="px-4 text-center text-secondary">{{ $costSheets->firstItem() + $loop->index }}</td>
            <td class="px-4"><span class="fw-bold text-dark">{{ $cs->date->format('d-m-Y') }}</span></td>
            <td class="px-4"><span class="fw-bold text-dark">{{ $cs->bom_no }}</span></td>
            <td class="px-4 text-secondary">{{ $cs->product->item_name ?? '—' }}</td>
            <td class="px-4 text-center">{{ $cs->qty }}</td>
            <td class="px-4 text-end fw-bold text-dark">₹ {{ number_format($cs->selling_price, 2) }}</td>
            <td class="px-4 text-center">
                <span class="badge bg-{{ $statusClass($cs->status) }} rounded-pill px-3 py-1" style="font-size:.6875rem;font-weight:600;cursor:pointer" onclick="toggleCsStatus(this, {{ $cs->id }})">{{ $cs->status }}</span>
            </td>
            <td class="px-4 text-center">
                <div class="d-flex gap-1 justify-content-center">
                    <a href="{{ route('admin.cost-sheet.show', $cs->id) }}" class="action-btn edit" title="View"><i class="bi bi-eye" style="font-size:.75rem"></i></a>
                    @if($cs->status == 'Draft')
                    <a href="{{ route('admin.cost-sheet.edit', $cs->id) }}" class="action-btn edit" title="Edit"><i class="bi bi-pencil" style="font-size:.75rem"></i></a>
                    @endif
                    <button type="button" class="action-btn delete" title="Delete" onclick="confirmDelete({{ $cs->id }})"><i class="bi bi-trash" style="font-size:.75rem"></i></button>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-4 py-5 text-center text-secondary">No cost sheets found.</td></tr>
        @endforelse
    </tbody>
</table>
