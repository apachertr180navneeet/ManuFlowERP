<table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
    <thead>
        <tr class="text-uppercase text-secondary" style="font-size:.6875rem;letter-spacing:.05em;font-weight:700;background:#f8fafc">
            <th class="px-4 py-3 text-center" style="width:50px">#</th>
            <th class="px-4 py-3">Customer Name</th>
            <th class="px-4 py-3">Firm Name</th>
            <th class="px-4 py-3">Mobile</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">Location</th>
            <th class="px-4 py-3 text-center" style="width:100px">Status</th>
            <th class="px-4 py-3 text-center" style="width:80px">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($customers as $customer)
        <tr>
            <td class="px-4 text-center text-secondary">{{ $customers->firstItem() + $loop->index }}</td>
            <td class="px-4">
                <span class="fw-bold text-dark">{{ $customer->customer_name }}</span>
            </td>
            <td class="px-4 text-secondary">{{ $customer->firm_name }}</td>
            <td class="px-4 text-secondary">{{ $customer->mobile }}</td>
            <td class="px-4 text-secondary" style="font-size:.75rem">{{ $customer->email }}</td>
            <td class="px-4 text-secondary">{{ $customer->location }}</td>
            <td class="px-4 text-center">
                <span class="status-badge {{ $customer->status }}" onclick="toggleStatus(this, {{ $customer->id }})">{{ ucfirst($customer->status) }}</span>
            </td>
            <td class="px-4 text-center">
                <div class="d-flex gap-1 justify-content-center">
                    <a href="{{ route('admin.customer.edit', $customer->id) }}" class="action-btn edit" title="Edit">
                        <i class="bi bi-pencil" style="font-size:.75rem"></i>
                    </a>
                    <button type="button" class="action-btn delete" title="Delete" onclick="confirmDelete({{ $customer->id }})">
                        <i class="bi bi-trash" style="font-size:.75rem"></i>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="px-4 py-5 text-center text-secondary">No customers found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
