@extends('admin.layouts.app')
@section('page_title', 'New Production')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.manufacturing.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-gear"></i> New Production</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('admin.manufacturing.store') }}" method="POST" id="productionForm">
            @csrf
            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Production Information</h6>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Production No. <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" value="{{ $nextProdNo }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Finished Item <span class="text-danger">*</span></label>
                    <select name="finished_item_id" id="finished_item_id" class="form-select @error('finished_item_id') is-invalid @enderror" onchange="onItemChange()">
                        <option value="">Select Finished Item</option>
                        @foreach($finishedItems as $item)
                        <option value="{{ $item->id }}" data-name="{{ $item->item_name }}" {{ old('finished_item_id') == $item->id ? 'selected' : '' }}>{{ $item->item_name }} ({{ $item->short_code }})</option>
                        @endforeach
                    </select>
                    @error('finished_item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Production Date <span class="text-danger">*</span></label>
                    <input type="date" name="production_date" class="form-control @error('production_date') is-invalid @enderror" value="{{ old('production_date', date('Y-m-d')) }}">
                    @error('production_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Production Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="production_qty" id="production_qty" class="form-control @error('production_qty') is-invalid @enderror" placeholder="Enter quantity" value="{{ old('production_qty') }}" min="0.01" step="0.01" oninput="onItemChange()">
                    @error('production_qty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-8 d-flex align-items-end justify-content-end">
                    <button type="button" class="btn btn-info btn-sm px-4" style="border-radius:.75rem;font-weight:600" onclick="checkStock()">
                        <i class="bi bi-search me-1"></i> Check BOM & Stock
                    </button>
                </div>
            </div>

            <div id="bomResult" style="display:none">
                <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Raw Material Requirements</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle mb-0" style="font-size:.8125rem">
                        <thead>
                            <tr style="background:#f8fafc">
                                <th class="px-3 py-2 text-uppercase text-center" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:40px">#</th>
                                <th class="px-3 py-2 text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Raw Material</th>
                                <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Per Unit</th>
                                <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Required</th>
                                <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Available</th>
                                <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Status</th>
                            </tr>
                        </thead>
                        <tbody id="bomBody"></tbody>
                    </table>
                </div>

                <div id="stockErrors" class="alert alert-danger" style="display:none"></div>
                <div id="stockSuccess" class="alert alert-success" style="display:none"></div>

                <div class="pt-3 border-top d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.manufacturing.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Cancel</a>
                    <button type="submit" id="saveBtn" class="btn btn-primary px-4" style="border-radius:.75rem;font-weight:600" disabled>Complete Production</button>
                </div>
            </div>

            <div class="pt-3 border-top d-flex gap-2 justify-content-end" id="initialActions">
                <a href="{{ route('admin.manufacturing.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
<script>
const csrfToken = '{{ csrf_token() }}';

function onItemChange() {
    document.getElementById('bomResult').style.display = 'none';
    document.getElementById('initialActions').style.display = 'flex';
}

function checkStock() {
    const itemId = document.getElementById('finished_item_id').value;
    const qty = document.getElementById('production_qty').value;

    if (!itemId) {
        Swal.fire({ title: 'Error', text: 'Please select a finished item.', icon: 'error', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
        return;
    }
    if (!qty || qty <= 0) {
        Swal.fire({ title: 'Error', text: 'Please enter a valid production quantity.', icon: 'error', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
        return;
    }

    fetch(BASE_URL + '/admin/manufacturing/get-bom', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ finished_item_id: itemId, production_qty: qty })
    })
    .then(r => r.json())
    .then(res => {
        document.getElementById('bomResult').style.display = 'block';
        document.getElementById('initialActions').style.display = 'none';

        const tbody = document.getElementById('bomBody');
        const errDiv = document.getElementById('stockErrors');
        const sucDiv = document.getElementById('stockSuccess');
        const saveBtn = document.getElementById('saveBtn');

        if (!res.success) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-secondary">' + res.message + '</td></tr>';
            errDiv.style.display = 'none';
            sucDiv.style.display = 'none';
            saveBtn.disabled = true;
            return;
        }

        tbody.innerHTML = '';
        res.requirements.forEach((r, i) => {
            const statusClass = r.sufficient ? 'bg-success' : 'bg-danger';
            const statusText = r.sufficient ? 'Sufficient' : 'Shortage';
            tbody.innerHTML += `<tr>
                <td class="px-3 text-center text-secondary">${i + 1}</td>
                <td class="px-3 fw-bold text-dark">${r.raw_material_name} (${r.raw_material_code})</td>
                <td class="px-3 text-center">${r.consumption_per_unit} ${r.unit_name}</td>
                <td class="px-3 text-center fw-bold text-dark">${r.required_qty} ${r.unit_name}</td>
                <td class="px-3 text-center">${r.available_stock} ${r.unit_name}</td>
                <td class="px-3 text-center"><span class="badge ${statusClass} rounded-pill px-3 py-1" style="font-size:.6875rem">${statusText}</span></td>
            </tr>`;
        });

        if (res.has_errors) {
            errDiv.style.display = 'block';
            errDiv.innerHTML = '<strong>Stock Shortages:</strong><br>' + res.stock_errors.join('<br>');
            sucDiv.style.display = 'none';
            saveBtn.disabled = true;
        } else {
            errDiv.style.display = 'none';
            sucDiv.style.display = 'block';
            sucDiv.innerHTML = '<i class="bi bi-check-circle me-1"></i> Sufficient stock available for all raw materials.';
            saveBtn.disabled = false;
        }
    })
    .catch(() => {
        Swal.fire({ title: 'Error', text: 'Failed to check BOM.', icon: 'error', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const itemId = document.getElementById('finished_item_id').value;
    const qty = document.getElementById('production_qty').value;
    if (itemId && qty > 0) {
        checkStock();
    }
});
</script>
@endsection
