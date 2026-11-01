@php
$statusClass = function($status) {
    return match($status) {
        'Delivered' => 'success',
        'Cancelled' => 'danger',
        'In Transit' => 'info',
        default     => 'warning',
    };
};
@endphp
<table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
    <thead>
        <tr class="text-uppercase text-secondary" style="font-size:.6875rem;letter-spacing:.05em;font-weight:700;background:#f8fafc">
            <th class="px-4 py-3 text-center" style="width:50px">#</th>
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3">CH No.</th>
            <th class="px-4 py-3">Customer</th>
            <th class="px-4 py-3">Contact</th>
            <th class="px-4 py-3">Transport</th>
            <th class="px-4 py-3 text-center" style="width:110px">Status</th>
            <th class="px-4 py-3 text-center" style="width:80px">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($dispatchOrders as $dispatch)
        <tr>
            <td class="px-4 text-center text-secondary">{{ $dispatchOrders->firstItem() + $loop->index }}</td>
            <td class="px-4">
                <span class="fw-bold text-dark">{{ $dispatch->dispatch_date->format('d-m-Y') }}</span>
            </td>
            <td class="px-4">
                <span class="fw-bold text-dark">{{ $dispatch->challan_no ?: '—' }}</span>
            </td>
            <td class="px-4 text-secondary">{{ $dispatch->customer->customer_name ?? '—' }}</td>
            <td class="px-4 text-secondary">{{ $dispatch->customer_mobile ?: '—' }}</td>
            <td class="px-4 text-secondary">{{ $dispatch->transport_name ?: '—' }}</td>
            <td class="px-4 text-center">
                <span class="badge bg-{{ $statusClass($dispatch->dispatch_status) }} rounded-pill px-3 py-1" style="font-size:.6875rem;font-weight:600">{{ $dispatch->dispatch_status }}</span>
            </td>
            <td class="px-4 text-center">
                <div class="d-flex gap-1 justify-content-center">
                    <a href="{{ route('admin.dispatch.edit', $dispatch->id) }}" class="action-btn edit" title="Edit">
                        <i class="bi bi-pencil" style="font-size:.75rem"></i>
                    </a>
                    <button type="button" class="action-btn delete" title="Delete" onclick="confirmDelete({{ $dispatch->id }})">
                        <i class="bi bi-trash" style="font-size:.75rem"></i>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="px-4 py-5 text-center text-secondary">No dispatch orders found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
