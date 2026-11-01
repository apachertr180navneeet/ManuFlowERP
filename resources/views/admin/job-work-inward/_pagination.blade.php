<div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 w-100">
    <div class="text-secondary" style="font-size:0.8125rem;">
        Showing {{ $jobWorkInwards->firstItem() ?? 0 }} to {{ $jobWorkInwards->lastItem() ?? 0 }} of {{ $jobWorkInwards->total() }} entries
    </div>
    <div class="pagination-wrapper">
        {{ $jobWorkInwards->links('pagination::bootstrap-4') }}
    </div>
</div>
