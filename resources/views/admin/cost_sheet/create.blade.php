@extends('admin.layouts.app')
@section('page_title', 'Add Cost Sheet')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.cost-sheet.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-calculator"></i> Add Cost Sheet</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('admin.cost-sheet.store') }}" method="POST" id="costSheetForm">
            @csrf
            <input type="hidden" name="items_json" id="items_json">
            <input type="hidden" name="expenses_json" id="expenses_json">
            <input type="hidden" name="status" id="status_input">

            <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Header Information</h6>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}">
                    @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">BOM No. <span class="text-danger">*</span></label>
                    <input type="text" name="bom_no" class="form-control @error('bom_no') is-invalid @enderror" value="{{ old('bom_no', $nextBomNo) }}" readonly>
                    @error('bom_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Manufacturing Record</label>
                    <select name="manufacturing_id" id="manufacturing_id" class="form-select" onchange="onManufacturingChange()">
                        <option value="">-- None (Manual) --</option>
                        @foreach($manufacturings as $m)
                        <option value="{{ $m->id }}" data-product="{{ $m->finished_item_id }}" data-product-name="{{ $m->finishedItem->item_name ?? '' }}" data-qty="{{ $m->production_qty }}" data-date="{{ $m->production_date->format('Y-m-d') }}" {{ old('manufacturing_id') == $m->id ? 'selected' : '' }}>{{ $m->production_no }} - {{ $m->finishedItem->item_name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Finished Product <span class="text-danger">*</span></label>
                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" onchange="onProductChange()">
                        <option value="">Select Product</option>
                        @foreach($finishedItems as $item)
                        <option value="{{ $item->id }}" data-name="{{ $item->item_name }}" {{ old('product_id') == $item->id ? 'selected' : '' }}>{{ $item->item_name }} ({{ $item->short_code }})</option>
                        @endforeach
                    </select>
                    @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Production Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="qty" id="qty" class="form-control @error('qty') is-invalid @enderror" placeholder="Enter qty" value="{{ old('qty') }}" min="0.01" step="0.01" oninput="onQtyChange()">
                    @error('qty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div id="bomSection" style="display:none">
                <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Raw Material Consumption</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle mb-0" style="font-size:.8125rem">
                        <thead>
                            <tr style="background:#f8fafc">
                                <th class="px-3 py-2 text-uppercase text-center" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:40px">#</th>
                                <th class="px-3 py-2 text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Raw Material</th>
                                <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">BOM Qty</th>
                                <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Req. Qty</th>
                                <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Unit</th>
                                <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">FIFO Rate</th>
                                <th class="px-3 py-2 text-end text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:120px">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="bomBody"></tbody>
                    </table>
                </div>

                <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Weight & Factory Expenses Allocation</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Unit Weight (KG)</label>
                        <input type="number" name="unit_weight" id="unitWeight" class="form-control" value="{{ old('unit_weight', 0) }}" min="0" step="0.01" oninput="calcCosts()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Current Product Total Weight (KG)</label>
                        <input type="text" id="totalWeight" class="form-control bg-light" value="0.00" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Overall Mfg Weight (KG)</label>
                        <input type="number" name="overall_mfg_weight" id="overallMfgWeight" class="form-control" value="{{ old('overall_mfg_weight', 0) }}" min="0" step="0.01" oninput="calcCosts()">
                    </div>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle mb-0" style="font-size:.8125rem">
                        <thead>
                            <tr style="background:#f8fafc">
                                <th class="px-3 py-2 text-uppercase text-center" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:40px">#</th>
                                <th class="px-3 py-2 text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8">Expense</th>
                                <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:150px">Overall Amount</th>
                                <th class="px-3 py-2 text-center text-uppercase" style="font-size:.6875rem;font-weight:700;color:#94a3b8;width:150px">Allocated Amount</th>
                                <th class="px-3 py-2 text-center" style="width:40px"></th>
                            </tr>
                        </thead>
                        <tbody id="expenseBody">
                            <tr class="expense-row">
                                <td class="px-2 py-2 text-center text-secondary sr-no" style="font-size:.75rem">1</td>
                                <td class="px-2 py-2">
                                    <select class="form-select form-select-sm expense-name" style="font-size:.8125rem" onchange="calcCosts()">
                                        <option value="">Select expense</option>
                                        @foreach($expenses as $exp)
                                        <option value="{{ $exp->expense_name }}">{{ $exp->expense_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="number" class="form-control form-control-sm text-center expense-overall-amount" style="font-size:.8125rem" placeholder="0" value="" min="0" step="0.01" oninput="calcCosts()">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="text" class="form-control form-control-sm text-center expense-amount bg-light" style="font-size:.8125rem" placeholder="0" value="0.00" readonly>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <button type="button" class="btn btn-link text-danger p-0 border-0" style="font-size:1rem" onclick="removeExpense(this)" title="Remove"><i class="bi bi-x-circle"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-light btn-sm mb-4" style="border-radius:.5rem;font-size:.75rem;font-weight:600;border:1px solid #e2e8f0" onclick="addExpense()">
                    <i class="bi bi-plus-lg me-1"></i> Add Expense
                </button>

                <h6 class="fw-bold text-dark mb-3" style="font-size:.8125rem">Cost Summary</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Raw Material Cost</label>
                        <input type="text" id="rawMaterialCost" class="form-control text-end fw-bold" style="font-size:.875rem" value="0.00" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Factory Expense Cost</label>
                        <input type="text" id="expenseCost" class="form-control text-end fw-bold" style="font-size:.875rem" value="0.00" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Total Cost</label>
                        <input type="text" id="totalCost" class="form-control text-end fw-bold" style="font-size:.875rem" value="0.00" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Profit % <span class="text-danger">*</span></label>
                        <input type="number" name="profit_percent" id="profitPercent" class="form-control text-center @error('profit_percent') is-invalid @enderror" value="{{ old('profit_percent', '10') }}" min="0" step="0.01" oninput="calcCosts()">
                        @error('profit_percent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="bg-indigo-50 border border-indigo-200 rounded-3 p-3 text-center" style="background:#eef2ff;border-color:#c7d2fe">
                            <label class="form-label text-indigo-600 fw-bold mb-1" style="color:#4338ca;font-size:.75rem">UNIT PRICE</label>
                            <input type="text" id="unitPrice" class="form-control text-center fw-bold text-indigo-700 border-0 bg-transparent" style="font-size:1.5rem;color:#4338ca" value="0.00" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-indigo-50 border border-indigo-200 rounded-3 p-3 text-center" style="background:#eef2ff;border-color:#c7d2fe">
                            <label class="form-label text-indigo-600 fw-bold mb-1" style="color:#4338ca;font-size:.75rem">SELLING PRICE</label>
                            <input type="text" id="sellingPrice" class="form-control text-center fw-bold text-indigo-700 border-0 bg-transparent" style="font-size:1.5rem;color:#4338ca" value="0.00" readonly>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-top d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-primary px-4" style="border-radius:.75rem;font-weight:600" onclick="submitForm()">
                        <i class="bi bi-check-circle me-1"></i> Save as Final
                    </button>
                </div>
            </div>

            <div class="pt-3 border-top d-flex gap-2 justify-content-end" id="initialActions">
                <a href="{{ route('admin.cost-sheet.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
<script>
const csrfToken = '{{ csrf_token() }}';
let rawMaterialItems = [];
let expenseIndex = 1;

function onProductChange() {
    document.getElementById('bomSection').style.display = 'none';
    document.getElementById('initialActions').style.display = 'flex';
    document.getElementById('manufacturing_id').value = '';
    autoLoadBom();
}

function onQtyChange() {
    document.getElementById('bomSection').style.display = 'none';
    document.getElementById('initialActions').style.display = 'flex';
    autoLoadBom();
}

function autoLoadBom() {
    const productId = document.getElementById('product_id').value;
    const qty = document.getElementById('qty').value;
    if (productId && qty && qty > 0) {
        loadBom();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('manufacturing_id').value) {
        onManufacturingChange();
    }
});

function onManufacturingChange() {
    const sel = document.getElementById('manufacturing_id');
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.value) {
        document.getElementById('product_id').value = opt.dataset.product;
        document.getElementById('qty').value = opt.dataset.qty;
        document.querySelector('[name="date"]').value = opt.dataset.date;
        loadBom();
    }
    document.getElementById('bomSection').style.display = 'none';
    document.getElementById('initialActions').style.display = 'flex';
}

function loadBom() {
    const productId = document.getElementById('product_id').value;
    const qty = document.getElementById('qty').value;
    const manufacturingId = document.getElementById('manufacturing_id').value;

    if (!productId) { Swal.fire({ title: 'Error', text: 'Please select a product.', icon: 'error', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 }); return; }
    if (!qty || qty <= 0) { Swal.fire({ title: 'Error', text: 'Please enter a valid quantity.', icon: 'error', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 }); return; }

    fetch(BASE_URL + '/admin/cost-sheet/get-bom', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ product_id: productId, qty: qty, manufacturing_id: manufacturingId || null })
    })
    .then(r => r.json())
    .then(res => {
        document.getElementById('bomSection').style.display = 'block';
        document.getElementById('initialActions').style.display = 'none';

        const tbody = document.getElementById('bomBody');

        if (!res.success) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-secondary">' + res.message + '</td></tr>';
            rawMaterialItems = [];
            calcCosts();
            return;
        }

        rawMaterialItems = res.items;
        renderBomTable();
        calcCosts();
    })
    .catch(() => {
        Swal.fire({ title: 'Error', text: 'Failed to load BOM.', icon: 'error', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
    });
}

function renderBomTable() {
    const tbody = document.getElementById('bomBody');
    tbody.innerHTML = '';
    rawMaterialItems.forEach((item, i) => {
        tbody.innerHTML += `<tr data-index="${i}">
            <td class="px-2 text-center text-secondary" style="font-size:.75rem">${i + 1}</td>
            <td class="px-3 fw-bold text-dark">${item.raw_material_name}</td>
            <td class="px-3 text-center">${item.bom_qty || '-'}</td>
            <td class="px-3 text-center fw-bold">${item.required_qty || '-'}</td>
            <td class="px-3 text-center">${item.unit_name}</td>
            <td class="px-3 text-center">${item.fifo_rate}</td>
            <td class="px-3 text-end fw-bold text-dark">${item.amount}</td>
        </tr>`;
    });
}

function calcCosts() {
    const qty = parseFloat(document.getElementById('qty').value) || 1;
    const unitWeight = parseFloat(document.getElementById('unitWeight').value) || 0;
    const overallMfgWeight = parseFloat(document.getElementById('overallMfgWeight').value) || 0;
    
    const totalWeight = unitWeight * qty;
    document.getElementById('totalWeight').value = totalWeight.toFixed(2);

    let rawCost = 0;
    rawMaterialItems.forEach(item => { rawCost += parseFloat(item.amount) || 0; });

    let expCost = 0;
    document.querySelectorAll('.expense-row').forEach(row => {
        const overallAmt = parseFloat(row.querySelector('.expense-overall-amount').value) || 0;
        let allocatedAmt = parseFloat(row.querySelector('.expense-amount').value) || 0;
        
        if (overallMfgWeight > 0 && overallAmt > 0) {
            allocatedAmt = (totalWeight / overallMfgWeight) * overallAmt;
            row.querySelector('.expense-amount').value = allocatedAmt.toFixed(2);
        } else if (overallAmt > 0 && overallMfgWeight === 0) {
            // If overall mfg weight is not provided, assume allocated is the same as overall
            allocatedAmt = overallAmt;
            row.querySelector('.expense-amount').value = allocatedAmt.toFixed(2);
        }

        expCost += allocatedAmt;
    });

    const totalCost = rawCost + expCost;
    const profitPct = parseFloat(document.getElementById('profitPercent').value) || 0;
    const profitAmt = totalCost * profitPct / 100;
    const sellingPrice = totalCost + profitAmt;

    document.getElementById('rawMaterialCost').value = rawCost.toFixed(2);
    document.getElementById('expenseCost').value = expCost.toFixed(2);
    document.getElementById('totalCost').value = totalCost.toFixed(2);
    document.getElementById('sellingPrice').value = sellingPrice.toFixed(2);

    document.getElementById('unitPrice').value = (sellingPrice / qty).toFixed(2);
}

function addExpense() {
    const tbody = document.getElementById('expenseBody');
    const tr = document.createElement('tr');
    tr.className = 'expense-row';
    tr.innerHTML = `
        <td class="px-2 py-2 text-center text-secondary sr-no" style="font-size:.75rem">${tbody.children.length + 1}</td>
        <td class="px-2 py-2">
            <select class="form-select form-select-sm expense-name" style="font-size:.8125rem" onchange="calcCosts()">
                <option value="">Select expense</option>
                @foreach($expenses as $exp)
                <option value="{{ $exp->expense_name }}">{{ $exp->expense_name }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-2 py-2">
            <input type="number" class="form-control form-control-sm text-center expense-overall-amount" style="font-size:.8125rem" placeholder="0" value="" min="0" step="0.01" oninput="calcCosts()">
        </td>
        <td class="px-2 py-2">
            <input type="text" class="form-control form-control-sm text-center expense-amount bg-light" style="font-size:.8125rem" placeholder="0" value="0.00" readonly>
        </td>
        <td class="px-2 py-2 text-center">
            <button type="button" class="btn btn-link text-danger p-0 border-0" style="font-size:1rem" onclick="removeExpense(this)" title="Remove"><i class="bi bi-x-circle"></i></button>
        </td>
    `;
    tbody.appendChild(tr);
    expenseIndex++;
    updateExpenseSrNos();
}

function removeExpense(btn) {
    const rows = document.querySelectorAll('.expense-row');
    if (rows.length <= 1) return;
    btn.closest('.expense-row').remove();
    updateExpenseSrNos();
    calcCosts();
}

function updateExpenseSrNos() {
    document.querySelectorAll('.expense-row').forEach((tr, i) => {
        tr.querySelector('.sr-no').textContent = i + 1;
    });
}

function submitForm() {
    const itemsPayload = rawMaterialItems.map(item => ({
        raw_material_id: item.raw_material_id,
        raw_material_name: item.raw_material_name,
        required_qty: item.required_qty,
        unit_name: item.unit_name,
        fifo_rate: item.fifo_rate,
        amount: item.amount,
        available_stock: item.available_stock || 0,
        sufficient: item.sufficient !== undefined ? item.sufficient : true,
    }));
    document.getElementById('items_json').value = JSON.stringify(itemsPayload);

    const expensesPayload = [];
    document.querySelectorAll('.expense-row').forEach(row => {
        const name = row.querySelector('.expense-name').value;
        const overallAmt = parseFloat(row.querySelector('.expense-overall-amount').value) || 0;
        const amt = parseFloat(row.querySelector('.expense-amount').value) || 0;
        if (name && amt > 0) {
            expensesPayload.push({ expense_name: name, amount: amt, overall_amount: overallAmt });
        }
    });
    document.getElementById('expenses_json').value = JSON.stringify(expensesPayload);
    document.getElementById('status_input').value = 'Final';
    document.getElementById('costSheetForm').submit();
}
</script>
@endsection
