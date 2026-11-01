<div class="d-flex align-items-center justify-content-between w-100">
    <span class="text-secondary" style="font-size:.75rem;font-weight:600">
        Showing <span class="fw-bold text-dark">{{ $costSheets->count() ? $costSheets->firstItem() : 0 }}</span>
        to <span class="fw-bold text-dark">{{ $costSheets->lastItem() }}</span>
        of <span class="fw-bold text-dark">{{ $costSheets->total() }}</span> cost sheets
    </span>
    <nav>
        <ul class="pagination pagination-sm mb-0">
            <li class="page-item {{ $costSheets->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $costSheets->appends(request()->query())->previousPageUrl() }}" style="border-radius:.5rem;font-size:.75rem;font-weight:600">&laquo; Prev</a>
            </li>
            <li class="page-item disabled mx-1">
                <span class="page-link fw-bold text-dark" style="border-radius:.5rem;font-size:.75rem;border:none;background:transparent">{{ $costSheets->currentPage() }} / {{ $costSheets->lastPage() }}</span>
            </li>
            <li class="page-item {{ $costSheets->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $costSheets->appends(request()->query())->nextPageUrl() }}" style="border-radius:.5rem;font-size:.75rem;font-weight:600">Next &raquo;</a>
            </li>
        </ul>
    </nav>
</div>
