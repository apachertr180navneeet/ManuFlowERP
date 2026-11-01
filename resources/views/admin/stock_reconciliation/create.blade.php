@extends('admin.layouts.app')
@section('page_title', 'Stock Reconciliation')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.stock-reconciliation.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-clipboard-check"></i> Stock Reconciliation</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('admin.stock-reconciliation.store') }}" method="POST" id="reconciliationForm">
            @csrf
            <input type="hidden" name="items_json" id="items_json">
            <input type="hidden" name="status" id="status_input">

            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Header Information</h6>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="reconciliation_date" class="form-control @error('reconciliation_date') is-invalid @enderror" value="{{ old('reconciliation_date', date('Y-m-d')) }}">
                    @error('reconciliation_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Reference No. <span class="text-danger">*</span></label>
                    <input type="text" name="reference_no" class="form-control @error('reference_no') is-invalid @enderror" value="{{ old('reference_no', $nextRef) }}" readonly>
                    @error('reference_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Notes</label>
                    <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Optional notes">
                </div>
            </div>

            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Reconciliation Items</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle mb-0" style="font-size:.8125rem">
                    <thead>
                        <tr style="background:#f8fafc">
                            <th class="px-3 py-2 text-uppercase text-center" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:40px">#</th>
                            <th class="px-3 py-2 text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Item</th>
                            <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">System Qty</th>
                            <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Physical Qty</th>
                            <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Difference</th>
                            <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Rate</th>
                            <th class="px-3 py-2 text-end text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Adjustment</th>
                            <th class="px-3 py-2 text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Remarks</th>
                            <th class="px-3 py-2 text-center" style="width:40px"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr>
                            <td class="px-2 py-2 text-center text-secondary sr-no" style="font-size:.75rem">1</td>
                            <td class="px-2 py-2">
                                <select class="form-select form-select-sm item-select" style="font-size:.8125rem" onchange="onItemSelect(this)">
                                    <option value="">Select Item</option>
                                    @foreach($items as $item)
                                    <option value="{{ $item->id }}" data-name="{{ $item->item_name }}">{{ $item->item_name }} ({{ $item->item_type }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" class="form-control form-control-sm text-center system-qty" style="font-size:.8125rem" readonly value="0">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" class="form-control form-control-sm text-center physical-qty" style="font-size:.8125rem" placeholder="0" value="" oninput="calcRow(this)">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" class="form-control form-control-sm text-center fw-bold difference-qty" style="font-size:.8125rem" readonly value="0">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" class="form-control form-control-sm text-center rate" style="font-size:.8125rem" readonly value="0">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" class="form-control form-control-sm text-end fw-bold adjustment-amount" style="font-size:.8125rem" readonly value="0">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" class="form-control form-control-sm remarks" style="font-size:.8125rem" placeholder="Remarks">
                            </td>
                            <td class="px-2 py-2 text-center">
                                <button type="button" class="btn btn-link text-danger p-0 border-0" style="font-size:1rem" onclick="removeRow(this)" title="Remove"><i class="bi bi-x-circle"></i></button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9" class="px-3 py-2">
                                <button type="button" class="btn btn-light btn-sm" style="border-radius:.5rem;font-size:.75rem;font-weight:600;border:1px solid #e2e8f0" onclick="addRow()">
                                    <i class="bi bi-plus-lg me-1"></i> Add Item
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="pt-3 border-top d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.stock-reconciliation.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Cancel</a>
                <button type="button" class="btn btn-warning px-4" style="border-radius:.75rem;font-weight:600" onclick="submitForm('Draft')">
                    <i class="bi bi-save me-1"></i> Save as Draft
                </button>
                <button type="button" class="btn btn-primary px-4" style="border-radius:.75rem;font-weight:600" onclick="submitForm('Posted')">
                    <i class="bi bi-check-circle me-1"></i> Save & Post
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
<script>
const csrfToken = '{{ csrf_token() }}';
let rowIndex = 1;

function onItemSelect(sel) {
    const row = sel.closest('tr');
    const itemId = sel.value;
    if (!itemId) return;

    fetch(BASE_URL + '/admin/stock-reconciliation/get-item-stock', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ item_id: itemId })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            row.querySelector('.system-qty').value = res.system_qty;
            row.querySelector('.rate').value = res.rate;
            calcRow(row.querySelector('.physical-qty'));
        }
    });
}

function calcRow(inp) {
    const row = inp.closest('tr');
    const systemQty = parseFloat(row.querySelector('.system-qty').value) || 0;
    const physicalQty = parseFloat(row.querySelector('.physical-qty').value) || 0;
    const rate = parseFloat(row.querySelector('.rate').value) || 0;
    const diff = physicalQty - systemQty;
    row.querySelector('.difference-qty').value = diff.toFixed(2);
    row.querySelector('.adjustment-amount').value = (diff * rate).toFixed(2);
}

function addRow() {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="px-2 py-2 text-center text-secondary sr-no" style="font-size:.75rem">${tbody.children.length + 1}</td>
        <td class="px-2 py-2">
            <select class="form-select form-select-sm item-select" style="font-size:.8125rem" onchange="onItemSelect(this)">
                <option value="">Select Item</option>
                @foreach($items as $item)
                <option value="{{ $item->id }}" data-name="{{ $item->item_name }}">{{ $item->item_name }} ({{ $item->item_type }})</option>
                @endforeach
            </select>
        </td>
        <td class="px-2 py-2">
            <input type="text" class="form-control form-control-sm text-center system-qty" style="font-size:.8125rem" readonly value="0">
        </td>
        <td class="px-2 py-2">
            <input type="text" class="form-control form-control-sm text-center physical-qty" style="font-size:.8125rem" placeholder="0" value="" oninput="calcRow(this)">
        </td>
        <td class="px-2 py-2">
            <input type="text" class="form-control form-control-sm text-center fw-bold difference-qty" style="font-size:.8125rem" readonly value="0">
        </td>
        <td class="px-2 py-2">
            <input type="text" class="form-control form-control-sm text-center rate" style="font-size:.8125rem" readonly value="0">
        </td>
        <td class="px-2 py-2">
            <input type="text" class="form-control form-control-sm text-end fw-bold adjustment-amount" style="font-size:.8125rem" readonly value="0">
        </td>
        <td class="px-2 py-2">
            <input type="text" class="form-control form-control-sm remarks" style="font-size:.8125rem" placeholder="Remarks">
        </td>
        <td class="px-2 py-2 text-center">
            <button type="button" class="btn btn-link text-danger p-0 border-0" style="font-size:1rem" onclick="removeRow(this)" title="Remove"><i class="bi bi-x-circle"></i></button>
        </td>
    `;
    tbody.appendChild(tr);
    updateSrNos();
}

function removeRow(btn) {
    const rows = document.querySelectorAll('#itemsBody tr');
    if (rows.length <= 1) return;
    btn.closest('tr').remove();
    updateSrNos();
}

function updateSrNos() {
    document.querySelectorAll('#itemsBody tr').forEach((tr, i) => {
        tr.querySelector('.sr-no').textContent = i + 1;
    });
}

function submitForm(status) {
    const itemsPayload = [];
    document.querySelectorAll('#itemsBody tr').forEach(row => {
        const select = row.querySelector('.item-select');
        if (!select || !select.value) return;
        itemsPayload.push({
            item_id: parseInt(select.value),
            item_name: select.options[select.selectedIndex].dataset.name,
            system_qty: parseFloat(row.querySelector('.system-qty').value) || 0,
            physical_qty: parseFloat(row.querySelector('.physical-qty').value) || 0,
            difference_qty: parseFloat(row.querySelector('.difference-qty').value) || 0,
            rate: parseFloat(row.querySelector('.rate').value) || 0,
            adjustment_amount: parseFloat(row.querySelector('.adjustment-amount').value) || 0,
            remarks: row.querySelector('.remarks').value || '',
        });
    });

    if (itemsPayload.length === 0) {
        Swal.fire({ title: 'Error', text: 'At least one item is required.', icon: 'error', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
        return;
    }

    document.getElementById('items_json').value = JSON.stringify(itemsPayload);
    document.getElementById('status_input').value = status;
    document.getElementById('reconciliationForm').submit();
}
</script>
@endsection
