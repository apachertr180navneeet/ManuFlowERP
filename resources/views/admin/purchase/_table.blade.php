@php
$statusClass = function($status) {
    return match($status) {
        'Completed' => 'success',
        'Cancelled' => 'danger',
        default     => 'warning',
    };
};
@endphp
<table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
    <thead>
        <tr class="text-uppercase text-secondary" style="font-size:.6875rem;letter-spacing:.05em;font-weight:700;background:#f8fafc">
            <th class="px-4 py-3 text-center" style="width:50px">#</th>
            <th class="px-4 py-3">Challan No</th>
            <th class="px-4 py-3">BNO</th>
            <th class="px-4 py-3">Vendor</th>
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3 text-end">Amount</th>
            <th class="px-4 py-3 text-center" style="width:100px">Status</th>
            <th class="px-4 py-3 text-center" style="width:80px">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($purchases as $purchase)
        <tr>
            <td class="px-4 text-center text-secondary">{{ $purchases->firstItem() + $loop->index }}</td>
            <td class="px-4">
                <span class="fw-bold text-dark">{{ $purchase->challan_no ?: '—' }}</span>
            </td>
            <td class="px-4 text-secondary">{{ $purchase->bno ?: '—' }}</td>
            <td class="px-4 text-secondary">{{ $purchase->vendor->vendor_name ?? '—' }}</td>
            <td class="px-4 text-secondary">{{ $purchase->purchase_date->format('d-m-Y') }}</td>
            <td class="px-4 text-end fw-bold text-dark">₹ {{ number_format($purchase->total_amount, 2) }}</td>
            <td class="px-4 text-center">
                <span class="badge bg-{{ $statusClass($purchase->purchase_status) }} rounded-pill px-3 py-1" style="font-size:.6875rem;font-weight:600">{{ $purchase->purchase_status }}</span>
            </td>
            <td class="px-4 text-center">
                <div class="d-flex gap-1 justify-content-center">
                    <a href="{{ route('admin.purchase.edit', $purchase->id) }}" class="action-btn edit" title="Edit">
                        <i class="bi bi-pencil" style="font-size:.75rem"></i>
                    </a>
                    <button type="button" class="action-btn delete" title="Delete" onclick="confirmDelete({{ $purchase->id }})">
                        <i class="bi bi-trash" style="font-size:.75rem"></i>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="px-4 py-5 text-center text-secondary">No purchases found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
