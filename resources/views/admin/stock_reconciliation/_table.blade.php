@php
$statusClass = function($s) {
    return $s === 'Posted' ? 'success' : 'warning';
};
@endphp
<table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
    <thead>
        <tr class="text-uppercase text-secondary" style="font-size:.6875rem;letter-spacing:.05em;font-weight:700;background:#f8fafc">
            <th class="px-4 py-3 text-center" style="width:50px">#</th>
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3">Reference No</th>
            <th class="px-4 py-3 text-center">Items</th>
            <th class="px-4 py-3">Notes</th>
            <th class="px-4 py-3 text-center" style="width:100px">Status</th>
            <th class="px-4 py-3 text-center" style="width:80px">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($reconciliations as $r)
        <tr>
            <td class="px-4 text-center text-secondary">{{ $reconciliations->firstItem() + $loop->index }}</td>
            <td class="px-4"><span class="fw-bold text-dark">{{ $r->reconciliation_date->format('d-m-Y') }}</span></td>
            <td class="px-4"><span class="fw-bold text-dark">{{ $r->reference_no }}</span></td>
            <td class="px-4 text-center">{{ $r->items_count }}</td>
            <td class="px-4 text-secondary">{{ Str::limit($r->notes, 40) }}</td>
            <td class="px-4 text-center">
                <span class="badge bg-{{ $statusClass($r->status) }} rounded-pill px-3 py-1" style="font-size:.6875rem;font-weight:600;cursor:pointer" onclick="toggleSrStatus(this, {{ $r->id }})">{{ $r->status }}</span>
            </td>
            <td class="px-4 text-center">
                <div class="d-flex gap-1 justify-content-center">
                    <a href="{{ route('admin.stock-reconciliation.show', $r->id) }}" class="action-btn edit" title="View"><i class="bi bi-eye" style="font-size:.75rem"></i></a>
                    @if($r->status == 'Draft')
                    <button type="button" class="action-btn delete" title="Delete" onclick="confirmDelete({{ $r->id }})"><i class="bi bi-trash" style="font-size:.75rem"></i></button>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-5 text-center text-secondary">No reconciliations found.</td></tr>
        @endforelse
    </tbody>
</table>
