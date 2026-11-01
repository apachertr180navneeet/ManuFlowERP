@extends('admin.layouts.app')
@section('page_title', 'Add Unit')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.unit.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-rulers"></i> Add Unit</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('admin.unit.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Unit Name <span class="text-danger">*</span></label>
                    <input type="text" name="unit_name" class="form-control @error('unit_name') is-invalid @enderror" placeholder="e.g. Pcs, Kg, Meter" value="{{ old('unit_name') }}">
                    @error('unit_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Parent Unit</label>
                    <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror" onchange="toggleConversion(this)">
                        <option value="">— None (Top Level) —</option>
                        @foreach($parents as $p)
                        <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->unit_name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3" id="conversionField" style="{{ old('parent_id') ? '' : 'display:none' }}">
                    <label class="form-label">Conversion Value</label>
                    <input type="number" name="conversion_value" class="form-control @error('conversion_value') is-invalid @enderror" placeholder="e.g. 1000" value="{{ old('conversion_value', 0) }}" step="any">
                    <small class="text-muted" id="conversionHint">1 Parent = ? Child</small>
                    @error('conversion_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.unit.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Cancel</a>
                <button type="submit" class="btn btn-primary px-4" style="border-radius:.75rem;font-weight:600">Save Unit</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
<script>
function toggleConversion(sel) {
    document.getElementById('conversionField').style.display = sel.value ? '' : 'none';
}
</script>
@endsection
