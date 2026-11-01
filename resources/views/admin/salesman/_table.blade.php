<table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
    <thead>
        <tr class="text-uppercase text-secondary" style="font-size:.6875rem;letter-spacing:.05em;font-weight:700;background:#f8fafc">
            <th class="px-4 py-3 text-center" style="width:50px">#</th>
            <th class="px-4 py-3">Salesman Name</th>
            <th class="px-4 py-3">Mobile</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">Joining Date</th>
            <th class="px-4 py-3">City</th>
            <th class="px-4 py-3 text-center" style="width:100px">Status</th>
            <th class="px-4 py-3 text-center" style="width:80px">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($salesmen as $salesman)
        <tr>
            <td class="px-4 text-center text-secondary">{{ $salesmen->firstItem() + $loop->index }}</td>
            <td class="px-4">
                <span class="fw-bold text-dark">{{ $salesman->salesman_name }}</span>
            </td>
            <td class="px-4 text-secondary">{{ $salesman->mobile }}</td>
            <td class="px-4 text-secondary" style="font-size:.75rem">{{ $salesman->email }}</td>
            <td class="px-4 text-secondary">{{ $salesman->joining_date ? $salesman->joining_date->format('d-m-Y') : '' }}</td>
            <td class="px-4 text-secondary">{{ $salesman->city }}</td>
            <td class="px-4 text-center">
                <span class="status-badge {{ $salesman->status }}" onclick="toggleStatus(this, {{ $salesman->id }})">{{ ucfirst($salesman->status) }}</span>
            </td>
            <td class="px-4 text-center">
                <div class="d-flex gap-1 justify-content-center">
                    <a href="{{ route('admin.salesman.edit', $salesman->id) }}" class="action-btn edit" title="Edit">
                        <i class="bi bi-pencil" style="font-size:.75rem"></i>
                    </a>
                    <button type="button" class="action-btn delete" title="Delete" onclick="confirmDelete({{ $salesman->id }})">
                        <i class="bi bi-trash" style="font-size:.75rem"></i>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="px-4 py-5 text-center text-secondary">No salesmen found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
