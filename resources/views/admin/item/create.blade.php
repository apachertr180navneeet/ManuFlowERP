@extends('admin.layouts.app')
@section('page_title', 'Add Item')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.item.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-box-seam"></i> Add Item</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('admin.item.store') }}" method="POST">
            @csrf
            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Item Details</h6>
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label">Short Code <span class="text-danger">*</span></label>
                    <input type="text" name="short_code" class="form-control @error('short_code') is-invalid @enderror" placeholder="e.g. ERLD" value="{{ old('short_code') }}">
                    @error('short_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Item Name <span class="text-danger">*</span></label>
                    <input type="text" name="item_name" class="form-control @error('item_name') is-invalid @enderror" placeholder="e.g. Emerald" value="{{ old('item_name') }}">
                    @error('item_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Item Type <span class="text-danger">*</span></label>
                    <select name="item_type" class="form-select @error('item_type') is-invalid @enderror">
                        <option value="">Select Type</option>
                        @foreach($itemTypes as $type)
                        <option value="{{ $type }}" {{ old('item_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('item_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Measure In <span class="text-danger">*</span></label>
                    <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->unit_name }}</option>
                        @endforeach
                    </select>
                    @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Size</label>
                    <input type="text" name="size" class="form-control @error('size') is-invalid @enderror" placeholder="e.g. 10mm, 1kg, etc." value="{{ old('size') }}">
                    @error('size')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.item.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Cancel</a>
                <button type="submit" class="btn btn-primary px-4" style="border-radius:.75rem;font-weight:600">Save Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
