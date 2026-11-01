@extends('admin.layouts.app')
@section('page_title', 'Edit Dispatch')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.dispatch.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-pencil"></i> Edit Dispatch</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('admin.dispatch.update', $dispatchOrder->id) }}" method="POST" id="dispatchForm">
            @csrf
            @method('PUT')
            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Dispatch Information</h6>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="dispatch_date" class="form-control @error('dispatch_date') is-invalid @enderror" value="{{ old('dispatch_date', $dispatchOrder->dispatch_date->format('Y-m-d')) }}">
                    @error('dispatch_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Challan No. <span class="text-danger">*</span></label>
                    <input type="text" name="challan_no" class="form-control @error('challan_no') is-invalid @enderror" placeholder="Auto or manual" value="{{ old('challan_no', $dispatchOrder->challan_no) }}">
                    @error('challan_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" onchange="onCustomerChange(this)">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" data-mobile="{{ $customer->mobile }}" {{ old('customer_id', $dispatchOrder->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->customer_name }} ({{ $customer->firm_name ?? $customer->mobile }})</option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                    <input type="text" name="customer_mobile" id="customer_mobile" class="form-control @error('customer_mobile') is-invalid @enderror" placeholder="Auto fetch" value="{{ old('customer_mobile', $dispatchOrder->customer_mobile) }}" readonly>
                    @error('customer_mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Transport Name <span class="text-danger">*</span></label>
                    <input type="text" name="transport_name" class="form-control @error('transport_name') is-invalid @enderror" placeholder="Enter transport name" value="{{ old('transport_name', $dispatchOrder->transport_name) }}">
                    @error('transport_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Item Details</h6>
            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle mb-0" style="font-size:.8125rem" id="itemsTable">
                    <thead>
                        <tr style="background:#f8fafc">
                            <th class="px-3 py-2 text-uppercase text-center" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:40px">#</th>
                            <th class="px-3 py-2 text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Item</th>
                            <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:90px">Qty</th>
                            <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:100px">Unit</th>
                            <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:110px">Rate</th>
                            <th class="px-3 py-2 text-end text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:120px">Amount</th>
                            <th class="px-3 py-2 text-center" style="width:40px"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @forelse($dispatchOrder->items as $i => $dItem)
                        <tr class="item-row">
                            <td class="px-2 py-2 text-center text-secondary sr-no" style="font-size:.75rem">{{ $i + 1 }}</td>
                            <td class="px-2 py-2">
                                <select name="items[{{ $i }}][item_id]" class="form-select form-select-sm item-select" style="font-size:.8125rem" onchange="onItemSelect(this)">
                                    <option value="">Select Item</option>
                                    @foreach($items as $item)
                                    <option value="{{ $item->id }}" data-unit-name="{{ $item->unit->unit_name ?? '' }}" data-price="{{ $item->latest_price ?? 0 }}" {{ $item->id == $dItem->item_id ? 'selected' : '' }}>{{ $item->item_name }}@if($item->short_code) ({{ $item->short_code }})@endif</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="items[{{ $i }}][qty]" class="form-control form-control-sm text-center item-qty" style="font-size:.8125rem" placeholder="0" value="{{ old("items.$i.qty", $dItem->qty) }}" oninput="calcRow(this)">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" class="form-control form-control-sm text-center item-unit-display" style="font-size:.8125rem" placeholder="Unit" value="{{ $dItem->item->unit->unit_name ?? '' }}" readonly>
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="items[{{ $i }}][rate]" class="form-control form-control-sm text-center item-rate" style="font-size:.8125rem" placeholder="0.00" value="{{ old("items.$i.rate", $dItem->rate) }}" oninput="calcRow(this)">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="items[{{ $i }}][amount]" class="form-control form-control-sm text-end item-amount fw-bold" style="font-size:.8125rem" placeholder="0.00" value="{{ old("items.$i.amount", $dItem->amount) }}" readonly>
                            </td>
                            <td class="px-2 py-2 text-center">
                                <button type="button" class="btn btn-link text-danger p-0 border-0" style="font-size:1rem" onclick="removeRow(this)" title="Remove">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr class="item-row">
                            <td class="px-2 py-2 text-center text-secondary sr-no" style="font-size:.75rem">1</td>
                            <td class="px-2 py-2">
                                <select name="items[0][item_id]" class="form-select form-select-sm item-select" style="font-size:.8125rem" onchange="onItemSelect(this)">
                                    <option value="">Select Item</option>
                                    @foreach($items as $item)
                                    <option value="{{ $item->id }}" data-unit-name="{{ $item->unit->unit_name ?? '' }}" data-price="{{ $item->latest_price ?? 0 }}">{{ $item->item_name }}@if($item->short_code) ({{ $item->short_code }})@endif</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="items[0][qty]" class="form-control form-control-sm text-center item-qty" style="font-size:.8125rem" placeholder="0" value="1" oninput="calcRow(this)">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" class="form-control form-control-sm text-center item-unit-display" style="font-size:.8125rem" placeholder="Unit" readonly>
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
                        @endforelse
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-light btn-sm mb-4" style="border-radius:.5rem;font-size:.75rem;font-weight:600;border:1px solid #e2e8f0" onclick="addRow()">
                <i class="bi bi-plus-lg me-1"></i> Add Item
            </button>

            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Financial Summary</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                </div>
                <div class="col-md-6 d-flex align-items-end justify-content-end">
                    <div class="bg-indigo-50 border border-indigo-200 rounded-3 p-3 text-center" style="background:#eef2ff;border-color:#c7d2fe;min-width:250px">
                        <label class="form-label text-indigo-600 fw-bold mb-1" style="color:#4338ca;font-size:.75rem">GRAND TOTAL</label>
                        <input type="text" name="total_amount" id="total_amount" class="form-control text-center fw-bold text-indigo-700 border-0 bg-transparent" style="font-size:1.5rem;color:#4338ca" value="{{ old('total_amount', $dispatchOrder->total_amount) }}" readonly>
                        @error('total_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="pt-3 border-top d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.dispatch.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Cancel</a>
                <button type="submit" class="btn btn-primary px-4" style="border-radius:.75rem;font-weight:600">Update Dispatch</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
<script>
let rowIndex = {{ max(count($dispatchOrder->items), 1) }};

function addRow() {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
        <td class="px-2 py-2 text-center text-secondary sr-no" style="font-size:.75rem">${tbody.children.length + 1}</td>
        <td class="px-2 py-2">
                <select name="items[${rowIndex}][item_id]" class="form-select form-select-sm item-select" style="font-size:.8125rem" onchange="onItemSelect(this)">
                    <option value="">Select Item</option>
                    @foreach($items as $item)
                    <option value="{{ $item->id }}" data-unit-name="{{ $item->unit->unit_name ?? '' }}" data-price="{{ $item->latest_price ?? 0 }}">{{ $item->item_name }}@if($item->short_code) ({{ $item->short_code }})@endif</option>
                    @endforeach
                </select>
            </td>
        <td class="px-2 py-2">
            <input type="text" name="items[${rowIndex}][qty]" class="form-control form-control-sm text-center item-qty" style="font-size:.8125rem" placeholder="0" value="1" oninput="calcRow(this)">
        </td>
        <td class="px-2 py-2">
            <input type="text" class="form-control form-control-sm text-center item-unit-display" style="font-size:.8125rem" placeholder="Unit" readonly>
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
    const unitName = opt ? opt.dataset.unitName || '' : '';
    const price = opt ? opt.dataset.price || '' : '';
    const row = sel.closest('.item-row');
    row.querySelector('.item-unit-display').value = unitName;

    if (price && price > 0) {
        row.querySelector('.item-rate').value = price;
    }
    calcRow(row.querySelector('.item-qty'));
}

function onCustomerChange(sel) {
    const opt = sel.options[sel.selectedIndex];
    const mobile = opt ? opt.dataset.mobile || '' : '';
    document.getElementById('customer_mobile').value = mobile;
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
    let grandTotal = 0;
    document.querySelectorAll('.item-amount').forEach(inp => {
        grandTotal += parseFloat(inp.value) || 0;
    });
    document.getElementById('total_amount').value = grandTotal.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    recalcAll();
    const customerSelect = document.getElementById('customer_id');
    if (customerSelect.value) {
        onCustomerChange(customerSelect);
    }
});
</script>
@endsection
