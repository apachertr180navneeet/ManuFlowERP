<table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
    <thead>
        <tr class="text-uppercase text-secondary" style="font-size:.6875rem;letter-spacing:.05em;font-weight:700;background:#f8fafc">
            <th class="px-4 py-3 text-center" style="width:50px">#</th>
            <th class="px-4 py-3">Production No</th>
            <th class="px-4 py-3">Finished Item</th>
            <th class="px-4 py-3 text-center">Qty</th>
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3 text-center" style="width:100px">Status</th>
            <th class="px-4 py-3 text-center" style="width:80px">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($manufacturings as $m)
        <tr>
            <td class="px-4 text-center text-secondary">{{ $manufacturings->firstItem() + $loop->index }}</td>
            <td class="px-4">
                <a href="{{ route('admin.manufacturing.show', $m->id) }}" class="fw-bold text-dark text-decoration-none">{{ $m->production_no }}</a>
            </td>
            <td class="px-4 text-secondary">{{ $m->finishedItem->item_name ?? '—' }}</td>
            <td class="px-4 text-center fw-bold text-dark">{{ $m->production_qty }}</td>
            <td class="px-4 text-secondary">{{ $m->production_date->format('d-m-Y') }}</td>
            <td class="px-4 text-center">
                <span class="badge bg-success rounded-pill px-3 py-1" style="font-size:.6875rem;font-weight:600">{{ $m->status }}</span>
            </td>
            <td class="px-4 text-center">
                <div class="d-flex gap-1 justify-content-center">
                    <a href="{{ route('admin.manufacturing.show', $m->id) }}" class="action-btn edit" title="View">
                        <i class="bi bi-eye" style="font-size:.75rem"></i>
                    </a>
                    <button type="button" class="action-btn delete" title="Delete" onclick="confirmDelete({{ $m->id }})">
                        <i class="bi bi-trash" style="font-size:.75rem"></i>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="px-4 py-5 text-center text-secondary">No manufacturing records found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
