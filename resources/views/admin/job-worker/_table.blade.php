<table class="table mb-0">
    <thead style="background-color:#f9fafb; border-bottom: 1px solid #f1f5f9;">
        <tr>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">ID</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">Job Worker Name</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">Phone</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">Email</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">City</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">Status</th>
            <th class="text-end" style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jobWorkers as $index => $jw)
        <tr>
            <td>{{ $jobWorkers->firstItem() + $index }}.</td>
            <td>
                <div class="d-flex flex-column">
                    <span class="fw-medium text-dark">{{ $jw->name }}</span>
                </div>
            </td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary">{{ $jw->phone ?? 'N/A' }}</span>
                </div>
            </td>
            <td>
                <span class="text-secondary">{{ $jw->email ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="text-secondary">{{ $jw->city ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="status-badge {{ $jw->status }}" style="cursor: pointer;" onclick="toggleStatus(this, {{ $jw->id }})">
                    {{ ucfirst($jw->status) }}
                </span>
            </td>
            <td>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="#" class="btn btn-light btn-sm text-primary" title="Ledger" style="border-radius:.5rem">Ledger</a>
                    <a href="{{ route('company.job-worker.edit', $jw->id) }}" class="btn btn-light btn-sm text-primary" title="Edit" style="border-radius:.5rem">Edit</a>
                    <button type="button" class="btn btn-light btn-sm text-danger" title="Delete" style="border-radius:.5rem" onclick="confirmDelete({{ $jw->id }})">Delete</button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center py-5">
                <div class="empty-state d-flex flex-column align-items-center justify-content-center">
                    <div class="empty-state-icon mb-2 text-secondary" style="font-size: 2rem;">
                        <i class="bi bi-people"></i>
                    </div>
                    <h6 class="fw-bold mb-1">No Job Workers Found</h6>
                    <p class="text-secondary mb-0">Add a new job worker to get started.</p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
