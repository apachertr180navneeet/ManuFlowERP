@if(count($finishedItems) && count($rawMaterials))
<div class="table-responsive" style="max-height: 80vh; overflow: auto;">
    <table class="table table-bordered align-middle mb-0" style="font-size:.8125rem">
        <thead>
            <tr>
                <th class="px-3 py-3 text-center" style="width:200px; position: sticky; top: 0; left: 0; z-index: 3; background-color: #fff;">Finished &nbsp;\&nbsp; Raw</th>
                @foreach($rawMaterials as $r)
                <th class="px-3 py-3 text-center text-nowrap" style="position: sticky; top: 0; z-index: 2; background-color: #fff;">
                    <span class="fw-bold" style="color:#d97706">{{ $r->short_code }}</span>
                    <span class="text-secondary ms-1" style="font-size:.6875rem;font-weight:500">{{ $r->item_name }}</span>
                    @if($r->unit_name) <span class="badge bg-light text-dark border ms-1" style="font-size:.5625rem;font-weight:500">{{ $r->unit_name }}</span> @endif
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($finishedItems as $f)
            @php $fAssigned = $assignments[$f->id] ?? []; $fUnit = $f->unit_name ?? ''; @endphp
            <tr>
                <td class="px-3 py-3" style="position: sticky; left: 0; z-index: 1; background-color: #fff; box-shadow: inset -1px 0 0 rgba(0,0,0,0.1);">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark">{{ $f->item_name }}</span>
                            <span class="badge bg-light text-success border border-success" style="font-size:.625rem;font-weight:700">{{ $f->short_code }}</span>
                        </div>
                        @if($fUnit)
                        <span class="badge bg-light text-dark border mt-2" style="font-size:.625rem;font-weight:500">Per {{ $fUnit }}</span>
                        @endif
                    </div>
                </td>
                @foreach($rawMaterials as $r)
                @php $cell = $fAssigned[$r->id] ?? null; @endphp
                <td class="px-3 py-3 text-center text-secondary">
                    @if($cell)
                        {{ $cell['value'] }}@if(!empty($cell['unit_name'])) <span class="fw-bold text-dark" style="font-size:.6875rem">{{ $cell['unit_name'] }}</span>@endif
                        {{--  <div class="text-secondary" style="font-size:.625rem">{{ $r->unit_name ?? '' }}</div>  --}}
                    @else
                        —
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="p-5 text-center text-secondary" style="font-size:.875rem">Both finished and raw items are required to show the matrix.</div>
@endif
