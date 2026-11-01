<table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
    <thead>
        <tr class="text-uppercase text-secondary" style="font-size:.6875rem;letter-spacing:.05em;font-weight:700;background:#f8fafc">
            <th class="px-4 py-3 text-center" style="width:50px">#</th>
            <th class="px-4 py-3">Company Name</th>
            <th class="px-4 py-3">Owner Name</th>
            <th class="px-4 py-3">Phone</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">City</th>
            <th class="px-4 py-3 text-center" style="width:100px">Status</th>
            <th class="px-4 py-3 text-center" style="width:130px">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($companies as $company)
        <tr>
            <td class="px-4 text-center text-secondary">{{ $companies->firstItem() + $loop->index }}</td>
            <td class="px-4">
                <span class="fw-bold text-dark">{{ $company->company_name }}</span>
                @if($company->gst_number)
                <span class="d-inline-block ms-1 px-1.5 py-0.5 rounded text-uppercase fw-bold" style="font-size:.625rem;background:#eef2ff;color:#4f46e5;border:1px solid #c7d2fe">{{ $company->gst_number }}</span>
                @endif
            </td>
            <td class="px-4 text-secondary">{{ $company->owner_name }}</td>
            <td class="px-4 text-secondary">{{ $company->mobile }}</td>
            <td class="px-4 text-secondary" style="font-size:.75rem">{{ $company->email }}</td>
            <td class="px-4 text-secondary">{{ $company->city }} @if($company->state)<br><span class="text-uppercase" style="font-size:.625rem;color:#94a3b8">{{ $company->state }}</span>@endif</td>
            <td class="px-4 text-center">
                <span class="status-badge {{ $company->status }}" onclick="toggleStatus(this, {{ $company->id }})">{{ ucfirst($company->status) }}</span>
            </td>
            <td class="px-4 text-center">
                <div class="d-flex justify-content-center gap-1">
                    <a href="{{ route('admin.company.edit', $company->id) }}" class="action-btn edit" title="Edit">
                        <i class="bi bi-pencil" style="font-size:.75rem"></i>
                    </a>

                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="px-4 py-5 text-center text-secondary">No companies found.</td>
        </tr>
        @endforelse
    </tbody>
</table>