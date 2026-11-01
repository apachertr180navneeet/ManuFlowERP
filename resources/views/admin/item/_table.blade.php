<table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
    <thead>
        <tr class="text-uppercase text-secondary" style="font-size:.6875rem;letter-spacing:.05em;font-weight:700;background:#f8fafc">
            <th class="px-4 py-3 text-center" style="width:50px">#</th>
            <th class="px-4 py-3">Short Code</th>
            <th class="px-4 py-3">Item Name</th>
            <th class="px-4 py-3">Item Type</th>
            <th class="px-4 py-3">Measure In</th>
            <th class="px-4 py-3">Size</th>
            <th class="px-4 py-3 text-center" style="width:100px">Status</th>
            <th class="px-4 py-3 text-center" style="width:80px">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $item)
        <tr>
            <td class="px-4 text-center text-secondary">{{ $items->firstItem() + $loop->index }}</td>
            <td class="px-4">
                <span class="fw-bold text-dark">{{ $item->short_code }}</span>
            </td>
            <td class="px-4 text-secondary">{{ $item->item_name }}</td>
            <td class="px-4 text-secondary">{{ $item->item_type }}</td>
            <td class="px-4 text-secondary">{{ $item->unit->unit_name ?? '' }}</td>
            <td class="px-4 text-secondary">{{ $item->size }}</td>
            <td class="px-4 text-center">
                <span class="status-badge {{ $item->status }}" onclick="toggleStatus(this, {{ $item->id }})">{{ ucfirst($item->status) }}</span>
            </td>
            <td class="px-4 text-center">
                <div class="d-flex gap-1 justify-content-center">
                    <a href="{{ route('admin.item.edit', $item->id) }}" class="action-btn edit" title="Edit">
                        <i class="bi bi-pencil" style="font-size:.75rem"></i>
                    </a>
                    <button type="button" class="action-btn delete" title="Delete" onclick="confirmDelete({{ $item->id }})">
                        <i class="bi bi-trash" style="font-size:.75rem"></i>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="px-4 py-5 text-center text-secondary">No items found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
