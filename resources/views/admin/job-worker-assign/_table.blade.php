<table class="table mb-0">
    <thead style="background-color:#f9fafb; border-bottom: 1px solid #f1f5f9;">
        <tr>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">#</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">DATE</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">CH NO.</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">CUSTOMER</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">CONTACT</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">TRANSPORT</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">STATUS</th>
            <th class="text-end" style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">ACTION</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jobWorkerAssigns as $assign)
        <tr>
            <td style="padding: 1rem;">{{ $loop->iteration + $jobWorkerAssigns->firstItem() - 1 }}</td>
            <td style="padding: 1rem;">{{ \Carbon\Carbon::parse($assign->date)->format('d/m/Y') }}</td>
            <td style="padding: 1rem;">{{ $assign->assign_no }}</td>
            <td style="padding: 1rem;">
                <span class="fw-medium text-dark">{{ $assign->jobWorker->name ?? 'N/A' }}</span>
            </td>
            <td style="padding: 1rem;">{{ $assign->contact_number }}</td>
            <td style="padding: 1rem;">{{ $assign->transport_name }}</td>
            <td style="padding: 1rem;">
                <span class="badge" style="background-color:{{ $assign->status == 'Completed' ? '#e0f2fe' : '#fef3c7' }}; color:{{ $assign->status == 'Completed' ? '#0369a1' : '#b45309' }}; border-radius:50rem; padding: 0.35rem 0.75rem; font-weight:500;">
                    {{ $assign->status }}
                </span>
            </td>
            <td class="text-end" style="padding: 1rem;">
                <div class="d-flex align-items-center justify-content-end">
                    <a href="{{ route('company.job-worker-assign.edit', $assign->id) }}" class="btn btn-light btn-sm text-primary me-2" title="Edit" style="border-radius:.5rem">
                        Edit
                    </a>
                    <form action="{{ route('company.job-worker-assign.destroy', $assign->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-light btn-sm text-danger delete-btn" title="Delete" style="border-radius:.5rem">
                            Delete
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center py-4" style="color:#64748b; font-size:.875rem;">No data available</td>
        </tr>
        @endforelse
    </tbody>
</table>

