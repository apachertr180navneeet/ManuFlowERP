@extends('admin.layouts.app')
@section('page_title', 'Add Purchase')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.purchase.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-cart3"></i> Add Purchase</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('admin.purchase.store') }}" method="POST" id="purchaseForm">
            @csrf
            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Purchase Information</h6>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                    <input type="date" name="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date', date('Y-m-d')) }}">
                    @error('purchase_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">BNO Number <span class="text-danger">*</span></label>
                    <input type="text" name="bno" class="form-control @error('bno') is-invalid @enderror" placeholder="Enter BNO number" value="{{ old('bno') }}">
                    @error('bno')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Challan Number <span class="text-danger">*</span></label>
                    <input type="text" name="challan_no" class="form-control @error('challan_no') is-invalid @enderror" placeholder="Enter challan number" value="{{ old('challan_no') }}">
                    @error('challan_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Vendor <span class="text-danger">*</span></label>
                    <select name="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror">
                        <option value="">Select Vendor</option>
                        @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->vendor_name }} ({{ $vendor->firm_name }})</option>
                        @endforeach
                    </select>
                    @error('vendor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Transport Name</label>
                    <input type="text" name="transport" class="form-control @error('transport') is-invalid @enderror" placeholder="Enter transport name" value="{{ old('transport') }}">
                    @error('transport')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">LR Number</label>
                    <input type="text" name="lr_no" class="form-control @error('lr_no') is-invalid @enderror" placeholder="Enter LR number" value="{{ old('lr_no') }}">
                    @error('lr_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row g-4 mb-4">
                {{--  <div class="col-md-4">
                    <label class="form-label">Purchase Status <span class="text-danger">*</span></label>
                    <select name="purchase_status" class="form-select @error('purchase_status') is-invalid @enderror">
                        <option value="Pending" {{ old('purchase_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Completed" {{ old('purchase_status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ old('purchase_status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('purchase_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>  --}}
                <div class="col-md-8">
                    <label class="form-label">Remarks</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="1" placeholder="Enter remarks...">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Item Details</h6>
            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle mb-0" style="font-size:.8125rem" id="itemsTable">
                    <thead>
                        <tr style="background:#f8fafc">
                            <th class="px-3 py-2 text-uppercase text-center" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:40px">#</th>
                            <th class="px-3 py-2 text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Item</th>
                            <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:100px">Qty</th>
                            <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:90px">Unit</th>
                            <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:110px">Rate</th>
                            <th class="px-3 py-2 text-end text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:120px">Amount</th>
                            <th class="px-3 py-2 text-center" style="width:40px"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td class="px-2 py-2 text-center text-secondary sr-no" style="font-size:.75rem">1</td>
                            <td class="px-3 py-2">
                                <select name="items[0][item_id]" class="form-select form-select-sm item-select" style="font-size:.8125rem" onchange="onItemSelect(this)">
                                    <option value="">Select Item</option>
                                    @foreach($items as $item)
                                    <option value="{{ $item->id }}" data-name="{{ $item->item_name }}" data-unit="{{ $item->unit->unit_name ?? '' }}">{{ $item->item_name }} ({{ $item->short_code }})</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="items[0][item_name]" class="item-name-input" value="">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="items[0][quantity]" class="form-control form-control-sm text-center item-qty" style="font-size:.8125rem" placeholder="0" value="1" oninput="calcRow(this)">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="items[0][unit]" class="form-control form-control-sm text-center item-unit" style="font-size:.8125rem" placeholder="Unit" value="" readonly>
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="items[0][rate]" class="form-control form-control-sm text-center item-rate" style="font-size:.8125rem" placeholder="0.00" value="" oninput="calcRow(this)">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="items[0][amount]" class="form-control form-control-sm text-end item-amount fw-bold" style="font-size:.8125rem" placeholder="0.00" value="" readonly>
                            </td>
                            <td class="px-2 py-2 text-center">
                                <button type="button" class="btn btn-link text-danger p-0 border-0" style="font-size:1rem" onclick="removeRow(this)" title="Remove">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-light btn-sm mb-4" style="border-radius:.5rem;font-size:.75rem;font-weight:600;border:1px solid #e2e8f0" onclick="addRow()">
                <i class="bi bi-plus-lg me-1"></i> Add Item
            </button>

            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Financial Summary</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Subtotal</label>
                    <input type="text" name="subtotal" id="subtotal" class="form-control text-end fw-bold" style="font-size:.875rem" value="0.00" readonly>
                    @error('subtotal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">GST (%)</label>
                    <select name="gst_percentage" id="gst_percentage" class="form-select" style="font-size:.875rem" onchange="calcTotal()">
                        <option value="18" {{ old('gst_percentage') == '18' ? 'selected' : '' }}>18%</option>
                        <option value="28" {{ old('gst_percentage') == '28' ? 'selected' : '' }}>28%</option>
                    </select>
                    @error('gst_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">GST Amount</label>
                    <input type="text" name="tax_amount" id="tax_amount" class="form-control text-end" style="font-size:.875rem" value="0.00" readonly>
                    @error('tax_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">Discount</label>
                    <input type="number" name="discount" id="discount" class="form-control text-end" style="font-size:.875rem" value="{{ old('discount', '0') }}" min="0" oninput="calcTotal()">
                    @error('discount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Transport Charges</label>
                    <input type="number" name="transport_charges" id="transport_charges" class="form-control text-end" style="font-size:.875rem" value="{{ old('transport_charges', '0') }}" min="0" oninput="calcTotal()">
                    @error('transport_charges')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Other Charges</label>
                    <input type="number" name="other_charges" id="other_charges" class="form-control text-end" style="font-size:.875rem" value="{{ old('other_charges', '0') }}" min="0" oninput="calcTotal()">
                    @error('other_charges')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-9 d-flex align-items-end justify-content-end">
                    <div class="bg-indigo-50 border border-indigo-200 rounded-3 p-3 text-center" style="background:#eef2ff;border-color:#c7d2fe;min-width:250px">
                        <label class="form-label text-indigo-600 fw-bold mb-1" style="color:#4338ca;font-size:.75rem">GRAND TOTAL</label>
                        <input type="text" name="total_amount" id="total_amount" class="form-control text-center fw-bold text-indigo-700 border-0 bg-transparent" style="font-size:1.5rem;color:#4338ca" value="0.00" readonly>
                        @error('total_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="pt-3 border-top d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.purchase.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Cancel</a>
                <button type="submit" class="btn btn-primary px-4" style="border-radius:.75rem;font-weight:600">Save Purchase</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
<script>
let rowIndex = 1;

function addRow() {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
        <td class="px-2 py-2 text-center text-secondary sr-no" style="font-size:.75rem">${tbody.children.length + 1}</td>
        <td class="px-3 py-2">
            <select name="items[${rowIndex}][item_id]" class="form-select form-select-sm item-select" style="font-size:.8125rem" onchange="onItemSelect(this)">
                <option value="">Select Item</option>
                @foreach($items as $item)
                <option value="{{ $item->id }}" data-name="{{ $item->item_name }}" data-unit="{{ $item->unit->unit_name ?? '' }}">{{ $item->item_name }} ({{ $item->short_code }})</option>
                @endforeach
            </select>
            <input type="hidden" name="items[${rowIndex}][item_name]" class="item-name-input" value="">
        </td>
        <td class="px-2 py-2">
            <input type="text" name="items[${rowIndex}][quantity]" class="form-control form-control-sm text-center item-qty" style="font-size:.8125rem" placeholder="0" value="1" oninput="calcRow(this)">
        </td>
        <td class="px-2 py-2">
            <input type="text" name="items[${rowIndex}][unit]" class="form-control form-control-sm text-center item-unit" style="font-size:.8125rem" placeholder="Unit" value="" readonly>
        </td>
        <td class="px-2 py-2">
            <input type="text" name="items[${rowIndex}][rate]" class="form-control form-control-sm text-center item-rate" style="font-size:.8125rem" placeholder="0.00" value="" oninput="calcRow(this)">
        </td>
        <td class="px-2 py-2">
            <input type="text" name="items[${rowIndex}][amount]" class="form-control form-control-sm text-end item-amount fw-bold" style="font-size:.8125rem" placeholder="0.00" value="" readonly>
        </td>
        <td class="px-2 py-2 text-center">
            <button type="button" class="btn btn-link text-danger p-0 border-0" style="font-size:1rem" onclick="removeRow(this)" title="Remove">
                <i class="bi bi-x-circle"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    rowIndex++;
    updateSrNos();
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length <= 1) return;
    btn.closest('.item-row').remove();
    updateSrNos();
    recalcAll();
}

function updateSrNos() {
    document.querySelectorAll('.item-row').forEach((tr, i) => {
        tr.querySelector('.sr-no').textContent = i + 1;
    });
}

function onItemSelect(sel) {
    const opt = sel.options[sel.selectedIndex];
    const name = opt ? opt.dataset.name || opt.text : '';
    const unit = opt ? opt.dataset.unit || '' : '';
    const row = sel.closest('.item-row');
    row.querySelector('.item-name-input').value = name;
    row.querySelector('.item-unit').value = unit;
}

function calcRow(el) {
    const row = el.closest('.item-row');
    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
    const rate = parseFloat(row.querySelector('.item-rate').value) || 0;
    const amount = qty * rate;
    row.querySelector('.item-amount').value = amount.toFixed(2);
    recalcAll();
}

function recalcAll() {
    let subtotal = 0;
    document.querySelectorAll('.item-amount').forEach(inp => {
        subtotal += parseFloat(inp.value) || 0;
    });
    document.getElementById('subtotal').value = subtotal.toFixed(2);
    calcTotal();
}

function calcTotal() {
    const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
    const gst = parseFloat(document.getElementById('gst_percentage').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const transport = parseFloat(document.getElementById('transport_charges').value) || 0;
    const other = parseFloat(document.getElementById('other_charges').value) || 0;
    const tax = subtotal * (gst / 100);
    const grandTotal = subtotal + tax - discount + transport + other;
    document.getElementById('tax_amount').value = tax.toFixed(2);
    document.getElementById('total_amount').value = grandTotal.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    recalcAll();
});
</script>
@endsection
