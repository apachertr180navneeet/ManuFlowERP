<div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
    <div class="text-secondary" style="font-size: .8125rem;">
        Showing {{ $jobWorkerAssigns->firstItem() ?? 0 }} to {{ $jobWorkerAssigns->lastItem() ?? 0 }} of {{ $jobWorkerAssigns->total() }} entries
    </div>
    
    @if ($jobWorkerAssigns->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm mb-0" style="gap: .25rem;">
            {{-- Previous Page Link --}}
            @if ($jobWorkerAssigns->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link" style="border-radius: .5rem; padding: .375rem .75rem;">Previous</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $jobWorkerAssigns->previousPageUrl() }}" style="border-radius: .5rem; padding: .375rem .75rem;">Previous</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($jobWorkerAssigns->links()->elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link" style="border-radius: .5rem; padding: .375rem .75rem;">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $jobWorkerAssigns->currentPage())
                            <li class="page-item active">
                                <span class="page-link" style="border-radius: .5rem; padding: .375rem .75rem; background-color: var(--bs-primary); border-color: var(--bs-primary);">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}" style="border-radius: .5rem; padding: .375rem .75rem;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($jobWorkerAssigns->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $jobWorkerAssigns->nextPageUrl() }}" style="border-radius: .5rem; padding: .375rem .75rem;">Next</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" style="border-radius: .5rem; padding: .375rem .75rem;">Next</span>
                </li>
            @endif
        </ul>
    </nav>
    @endif
</div>

