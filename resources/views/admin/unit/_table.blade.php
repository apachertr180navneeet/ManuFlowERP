<table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
    <thead>
        <tr class="text-uppercase text-secondary" style="font-size:.6875rem;letter-spacing:.05em;font-weight:700;background:#f8fafc">
            <th class="px-4 py-3 text-center" style="width:50px">#</th>
            <th class="px-4 py-3">Unit Name</th>
            <th class="px-4 py-3">Parent Unit</th>
            <th class="px-4 py-3 text-center">Conversion</th>
            <th class="px-4 py-3 text-center" style="width:100px">Status</th>
            <th class="px-4 py-3 text-center" style="width:80px">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($units as $unit)
        <tr class="table-primary">
            <td class="px-4 text-center text-secondary">{{ $units->firstItem() + $loop->index }}</td>
            <td class="px-4">
                <span class="fw-bold text-dark">{{ $unit->unit_name }}</span>
            </td>
            <td class="px-4 text-secondary">—</td>
            <td class="px-4 text-center text-secondary">—</td>
            <td class="px-4 text-center">
                <span class="status-badge {{ $unit->status }}" onclick="toggleStatus(this, {{ $unit->id }})">{{ ucfirst($unit->status) }}</span>
            </td>
            <td class="px-4 text-center">
                <div class="d-flex gap-1 justify-content-center">
                    <a href="{{ route('admin.unit.edit', $unit->id) }}" class="action-btn edit" title="Edit">
                        <i class="bi bi-pencil" style="font-size:.75rem"></i>
                    </a>
                    <button type="button" class="action-btn delete" title="Delete" onclick="confirmDelete({{ $unit->id }})">
                        <i class="bi bi-trash" style="font-size:.75rem"></i>
                    </button>
                </div>
            </td>
        </tr>
            @if($unit->relationLoaded('children') && $unit->children->isNotEmpty())
                @foreach($unit->children as $child)
                <tr>
                    <td class="px-4 text-center text-secondary"></td>
                    <td class="px-4">
                        <span class="ms-4">↳ {{ $child->unit_name }}</span>
                    </td>
                    <td class="px-4">{{ $unit->unit_name }}</td>
                    <td class="px-4 text-center">
                        <span class="badge bg-light text-dark fs-7">1 {{ $unit->unit_name }} = {{ $child->conversion_value }} {{ $child->unit_name }}</span>
                    </td>
                    <td class="px-4 text-center">
                        <span class="status-badge {{ $child->status }}" onclick="toggleStatus(this, {{ $child->id }})">{{ ucfirst($child->status) }}</span>
                    </td>
                    <td class="px-4 text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('admin.unit.edit', $child->id) }}" class="action-btn edit" title="Edit">
                                <i class="bi bi-pencil" style="font-size:.75rem"></i>
                            </a>
                            <button type="button" class="action-btn delete" title="Delete" onclick="confirmDelete({{ $child->id }})">
                                <i class="bi bi-trash" style="font-size:.75rem"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            @endif
        @empty
        <tr>
            <td colspan="6" class="px-4 py-5 text-center text-secondary">No units found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
