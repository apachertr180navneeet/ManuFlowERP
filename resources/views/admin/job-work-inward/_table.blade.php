<table class="table mb-0">
    <thead style="background-color:#f9fafb; border-bottom: 1px solid #f1f5f9;">
        <tr>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">#</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">Challan No</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">BNO</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">Job Worker</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">Date</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">Amount</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;">Status</th>
            <th style="font-size:0.7rem; font-weight:700; color:#8b9bb4; text-transform:uppercase; padding: 1rem;" class="text-end">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jobWorkInwards as $key => $inward)
        <tr style="border-bottom: 1px solid #f1f5f9;">
            <td style="padding: 1rem; color:#475569; font-size:0.875rem;">{{ $jobWorkInwards->firstItem() + $key }}</td>
            <td style="padding: 1rem; color:#475569; font-size:0.875rem;">{{ $inward->challan_no ?? '-' }}</td>
            <td style="padding: 1rem; color:#475569; font-size:0.875rem;">{{ $inward->inward_no }}</td>
            <td style="padding: 1rem; color:#475569; font-size:0.875rem; font-weight: 500;">
                {{ $inward->jobWorkerAssign->jobWorker->name ?? 'N/A' }}
            </td>
            <td style="padding: 1rem;">
                <span class="badge-date">{{ \Carbon\Carbon::parse($inward->inward_date)->format('d/m/Y') }}</span>
            </td>
            <td style="padding: 1rem; color:#475569; font-size:0.875rem;">{{ number_format($inward->total_amount, 2) }}</td>
            <td style="padding: 1rem;">
                <span class="status-badge {{ strtolower($inward->status) == 'completed' ? 'active' : 'inactive' }}">
                    {{ $inward->status }}
                </span>
            </td>
            <td style="padding: 1rem;" class="text-end">
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('company.job-work-inward.edit', $inward->id) }}" class="action-btn edit" title="Edit">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('company.job-work-inward.destroy', $inward->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn delete delete-btn" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center py-4 text-muted">No Job Work Inwards Found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
