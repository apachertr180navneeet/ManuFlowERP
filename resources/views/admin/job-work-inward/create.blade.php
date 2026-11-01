@extends('admin.layouts.app')

@section('page_title', 'Add Job Inward')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header pb-0 border-bottom-0 text-center">
                <h5 class="mb-0">Add Job Inward</h5>
            </div>
            
            <form action="{{ route('company.job-work-inward.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <h6 class="mb-3">Job work Information</h6>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Job inward Date</label>
                            <input type="date" name="inward_date" class="form-control" value="{{ old('inward_date', date('Y-m-d')) }}" required>
                            @error('inward_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">JW Number</label>
                            <input type="text" name="inward_no" class="form-control" placeholder="Enter BNO Number" value="{{ old('inward_no') }}" required>
                            @error('inward_no') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">JOB WORKER ASSIGN NO.</label>
                            <select name="job_worker_assign_id" class="form-select" id="job_worker_assign_id" required>
                                <option value="">Select JW no.</option>
                                @foreach($assigns as $assign)
                                    <option value="{{ $assign->id }}" {{ old('job_worker_assign_id') == $assign->id ? 'selected' : '' }}>{{ $assign->assign_no }}</option>
                                @endforeach
                            </select>
                            @error('job_worker_assign_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Challan Number</label>
                            <input type="text" name="challan_no" id="challan_no" class="form-control" placeholder="Enter Challan Number" value="{{ old('challan_no') }}">
                            @error('challan_no') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Transport Name</label>
                            <input type="text" name="transport_name" id="transport_name" class="form-control" placeholder="Enter Transport Name" value="{{ old('transport_name') }}">
                            @error('transport_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">LR Number</label>
                            <input type="text" name="lr_number" class="form-control" placeholder="Enter LR Number" value="{{ old('lr_number') }}">
                            @error('lr_number') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Job Worker</label>
                            <input type="text" id="job_worker_name" class="form-control" value="Select Job Worker" readonly>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Remark</label>
                            <textarea name="remark" class="form-control" rows="1" placeholder="Textarea">{{ old('remark') }}</textarea>
                            @error('remark') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <h6 class="mb-3 mt-4">Item details</h6>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ITEM</th>
                                    <th>ASSIGN QTY</th>
                                    <th>PREV. REC. QTY</th>
                                    <th>REM. QTY</th>
                                    <th>UNIT</th>
                                    <th>RATE</th>
                                    <th>AMOUNT</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(old('items'))
                                    @foreach(old('items') as $index => $itemData)
                                        <tr class="item-row">
                                            <td class="row-number">{{ $index + 1 }}</td>
                                            <td>
                                                <select name="items[{{ $index }}][item_id]" class="form-select item-select" required>
                                                    <option value="">Select Item</option>
                                                    @foreach($items as $item)
                                                        <option value="{{ $item->id }}" data-unit="{{ $item->unit->unit_name ?? '' }}" {{ $itemData['item_id'] == $item->id ? 'selected' : '' }}>{{ $item->item_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error("items.{$index}.item_id") <small class="text-danger">{{ $message }}</small> @enderror
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][assigned_qty]" class="form-control assigned-qty" value="{{ $itemData['assigned_qty'] ?? '' }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][prev_rec_qty]" class="form-control received-qty" value="{{ $itemData['prev_rec_qty'] ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][qty]" class="qty-input" value="{{ $itemData['qty'] ?? '1' }}">
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][remaining_qty]" class="form-control remaining-qty" value="{{ $itemData['remaining_qty'] ?? '' }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $index }}][unit]" class="form-control unit-input" value="{{ $itemData['unit'] }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][rate]" class="form-control rate-input" step="0.01" min="0" value="{{ $itemData['rate'] }}" required>
                                                @error("items.{$index}.rate") <small class="text-danger">{{ $message }}</small> @enderror
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][amount]" class="form-control amount-input" step="0.01" min="0" value="{{ $itemData['amount'] }}" readonly required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-item-btn">X</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="item-row">
                                        <td class="row-number">1</td>
                                        <td>
                                            <select name="items[0][item_id]" class="form-select item-select" required>
                                                <option value="">Select item</option>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}" data-unit="{{ $item->unit->unit_name ?? '' }}">{{ $item->item_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][assigned_qty]" class="form-control assigned-qty" value="" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][prev_rec_qty]" class="form-control received-qty" value="">
                                            <input type="hidden" name="items[0][qty]" class="qty-input" value="1">
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][remaining_qty]" class="form-control remaining-qty" value="" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][unit]" class="form-control unit-input" placeholder="Unit" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][rate]" class="form-control rate-input" step="0.01" min="0" value="0" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][amount]" class="form-control amount-input" step="0.01" min="0" value="0" readonly required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-item-btn" disabled>X</button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-primary btn-sm mb-4" id="addItemBtn">Add item</button>

                    <h6 class="mb-3">Financial Summary</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Subtotal</label>
                            <input type="number" name="subtotal" id="subtotal" class="form-control" step="0.01" min="0" value="{{ old('subtotal', 0) }}" readonly required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Other Charges</label>
                            <input type="number" name="other_charges" id="other_charges" class="form-control calculate-total" step="0.01" min="0" value="{{ old('other_charges', 0) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">DISCOUNT</label>
                            <input type="number" name="discount" id="discount" class="form-control calculate-total" step="0.01" min="0" value="{{ old('discount', 0) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Transport Charges</label>
                            <input type="number" name="transport_charges" id="transport_charges" class="form-control calculate-total" step="0.01" min="0" value="{{ old('transport_charges', 0) }}">
                        </div>
                    </div>
                    
                    <div class="row mt-2 mb-4">
                        <div class="col-md-3 ms-auto">
                            <label class="form-label">Total Amount</label>
                            <input type="number" name="total_amount" id="total_amount" class="form-control font-weight-bold" step="0.01" min="0" value="{{ old('total_amount', 0) }}" readonly required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        <a href="{{ route('company.job-work-inward.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Inward</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        
        let allItems = @json($items);
        let assignedItems = []; // Add this to track assigned items

        // Fetch Assign details when Assign No is selected
        $('#job_worker_assign_id').on('change', function() {
            let assignId = $(this).val();
            if(assignId) {
                $.ajax({
                    url: BASE_URL + '/company/job-worker-assign/' + assignId + '/details',
                    type: 'GET',
                    success: function(response) {
                        $('#job_worker_name').val(response.job_worker ? response.job_worker.name : '');
                        $('#transport_name').val(response.transport_name ? response.transport_name : '');
                        $('#challan_no').val(response.assign_no ? response.assign_no : '');
                        
                        // Populate Items
                        if(response.items && response.items.length > 0) {
                            assignedItems = response.items.map(function(item) {
                                return {
                                    id: item.item_id,
                                    item_name: item.item ? item.item.item_name : '',
                                    unit: item.item && item.item.unit ? item.item.unit : null,
                                    assigned_qty: item.assigned_qty,
                                    received_qty: item.received_qty,
                                    remaining_qty: item.remaining_qty
                                };
                            });

                            $('#itemsTable tbody').empty();
                            itemIndex = 0;
                            response.items.forEach(function(item) {
                                if (item.remaining_qty <= 0) return;
                                let unitName = item.item && item.item.unit ? item.item.unit.unit_name : '';
                                let newRow = `
                                    <tr class="item-row">
                                        <td class="row-number">${itemIndex + 1}</td>
                                        <td>
                                            <select name="items[${itemIndex}][item_id]" class="form-select item-select" required>
                                                <option value="">Select Item</option>
                                                ${assignedItems.map(i => `<option value="${i.id}" data-unit="${i.unit ? i.unit.unit_name : ''}" data-assigned-qty="${i.assigned_qty}" data-received-qty="${i.received_qty}" data-remaining-qty="${i.remaining_qty}" ${i.id == item.item_id ? 'selected' : ''}>${i.item_name}</option>`).join('')}
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[${itemIndex}][assigned_qty]" class="form-control assigned-qty" value="${item.assigned_qty}" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="items[${itemIndex}][prev_rec_qty]" class="form-control received-qty" value="${item.received_qty}">
                                            <input type="hidden" name="items[${itemIndex}][qty]" class="qty-input" value="${item.qty || item.remaining_qty}">
                                        </td>
                                        <td>
                                            <input type="number" name="items[${itemIndex}][remaining_qty]" class="form-control remaining-qty" value="${item.remaining_qty}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="items[${itemIndex}][unit]" class="form-control unit-input" value="${unitName}" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="items[${itemIndex}][rate]" class="form-control rate-input" step="0.01" min="0" value="${item.rate}" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[${itemIndex}][amount]" class="form-control amount-input" step="0.01" min="0" value="${item.amount}" readonly required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-item-btn">X</button>
                                        </td>
                                    </tr>
                                `;
                                $('#itemsTable tbody').append(newRow);
                                itemIndex++;
                            });
                            updateRemoveButtons();
                            calculateTotals();
                        } else {
                            assignedItems = [];
                            $('#itemsTable tbody').empty();
                            $('#addItemBtn').trigger('click');
                        }
                    }
                });
            } else {
                $('#job_worker_name').val('Select Job Worker');
                $('#transport_name').val('');
                $('#challan_no').val('');
                assignedItems = [];
                $('#itemsTable tbody').empty();
                $('#addItemBtn').trigger('click'); // add empty row
            }
        });

        // Trigger change for old input or if assign is pre-selected
        if($('#job_worker_assign_id').val()) {
            // We want to fetch assignedItems silently if old items exist, so we don't overwrite the table
            if ({{ old('items') ? 'true' : 'false' }}) {
                $.ajax({
                    url: BASE_URL + '/company/job-worker-assign/' + $('#job_worker_assign_id').val() + '/details',
                    type: 'GET',
                    success: function(response) {
                        if(response.items && response.items.length > 0) {
                            assignedItems = response.items.map(function(item) {
                                return {
                                    id: item.item_id,
                                    item_name: item.item ? item.item.item_name : '',
                                    unit: item.item && item.item.unit ? item.item.unit : null,
                                    assigned_qty: item.assigned_qty,
                                    received_qty: item.received_qty,
                                    remaining_qty: item.remaining_qty
                                };
                            });
                            // Update existing item-select dropdowns to only show assigned items
                            $('.item-select').each(function() {
                                let selectedId = $(this).val();
                                $(this).empty().append('<option value="">Select Item</option>');
                                assignedItems.forEach(i => {
                                    let selected = (i.id == selectedId) ? 'selected' : '';
                                    $(this).append(`<option value="${i.id}" data-unit="${i.unit ? i.unit.unit_name : ''}" data-assigned-qty="${i.assigned_qty}" data-received-qty="${i.received_qty}" data-remaining-qty="${i.remaining_qty}" ${selected}>${i.item_name}</option>`);
                                });
                                // Do not trigger change on initial load to preserve old input values
                            });
                        }
                    }
                });
            } else {
                $('#job_worker_assign_id').trigger('change');
            }
        }

        let itemIndex = {{ old('items') ? count(old('items')) : 1 }};

        // Add Item Row
        $('#addItemBtn').on('click', function() {
            let rowCount = $('#itemsTable tbody tr').length + 1;
            let listToUse = $('#job_worker_assign_id').val() ? assignedItems : allItems;
            let newRow = `
                <tr class="item-row">
                    <td class="row-number">${rowCount}</td>
                    <td>
                        <select name="items[${itemIndex}][item_id]" class="form-select item-select" required>
                            <option value="">Select Item</option>
                            ${listToUse.map(i => `<option value="${i.id}" data-unit="${i.unit ? i.unit.unit_name : ''}" data-assigned-qty="${i.assigned_qty !== undefined ? i.assigned_qty : ''}" data-received-qty="${i.received_qty !== undefined ? i.received_qty : ''}" data-remaining-qty="${i.remaining_qty !== undefined ? i.remaining_qty : ''}">${i.item_name}</option>`).join('')}
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control assigned-qty" value="" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control received-qty" value="" >
                        <input type="hidden" name="items[${itemIndex}][qty]" class="qty-input" value="1">
                    </td>
                    <td>
                        <input type="number" class="form-control remaining-qty" value="" readonly>
                    </td>
                    <td>
                        <input type="text" name="items[${itemIndex}][unit]" class="form-control unit-input" placeholder="Unit" readonly>
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][rate]" class="form-control rate-input" step="0.01" min="0" value="0" required>
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][amount]" class="form-control amount-input" step="0.01" min="0" value="0" readonly required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-item-btn">X</button>
                    </td>
                </tr>
            `;
            $('#itemsTable tbody').append(newRow);
            itemIndex++;
            updateRemoveButtons();
        });

        // Remove Item Row
        $(document).on('click', '.remove-item-btn', function() {
            if ($('#itemsTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
                updateRowNumbers();
                updateRemoveButtons();
                calculateTotals();
            }
        });

        // Auto-fill unit and qty based on item selection
        $(document).on('change', '.item-select', function() {
            let option = $(this).find(':selected');
            let unit = option.data('unit');
            let assignedQty = option.data('assigned-qty');
            let receivedQty = option.data('received-qty');
            let remainingQty = option.data('remaining-qty');

            let row = $(this).closest('tr');
            row.find('.unit-input').val(unit ? unit : '');
            
            if (assignedQty !== undefined && assignedQty !== '') {
                row.find('.assigned-qty').val(assignedQty);
                row.find('.received-qty').val(receivedQty);
                row.find('.remaining-qty').val(remainingQty);
                row.find('.qty-input').attr('max', remainingQty);
            } else {
                row.find('.assigned-qty').val('');
                row.find('.received-qty').val('');
                row.find('.remaining-qty').val('');
                row.find('.qty-input').removeAttr('max');
            }
            
            row.find('.received-qty').trigger('input');
        });

        // Calculate amount for a row
        $(document).on('input', '.received-qty, .rate-input', function() {
            let row = $(this).closest('tr');
            let qty = parseFloat(row.find('.received-qty').val()) || 0;
            let rate = parseFloat(row.find('.rate-input').val()) || 0;
            let amount = qty * rate;
            row.find('.amount-input').val(amount.toFixed(2));
            row.find('.qty-input').val(qty);
            calculateTotals();
        });

        // Auto-calculate remaining quantity
        $(document).on('input', '.assigned-qty, .received-qty', function() {
            let row = $(this).closest('tr');
            let assignedQty = parseFloat(row.find('.assigned-qty').val()) || 0;
            let receivedQty = parseFloat(row.find('.received-qty').val()) || 0;
            let remainingQty = Math.max(0, assignedQty - receivedQty);
            
            row.find('.remaining-qty').val(remainingQty);
            row.find('.qty-input').attr('max', remainingQty);
        });

        $(document).on('input', '.calculate-total', function() {
            calculateTotals();
        });

        function calculateTotals() {
            let subtotal = 0;
            $('.amount-input').each(function() {
                subtotal += parseFloat($(this).val()) || 0;
            });
            $('#subtotal').val(subtotal.toFixed(2));

            let otherCharges = parseFloat($('#other_charges').val()) || 0;
            let discount = parseFloat($('#discount').val()) || 0;
            let transportCharges = parseFloat($('#transport_charges').val()) || 0;

            let total = subtotal + otherCharges - discount + transportCharges;
            $('#total_amount').val(total.toFixed(2));
        }

        function updateRowNumbers() {
            $('#itemsTable tbody tr').each(function(index) {
                $(this).find('.row-number').text(index + 1);
            });
        }

        function updateRemoveButtons() {
            let rowsCount = $('#itemsTable tbody tr').length;
            if (rowsCount <= 1) {
                $('.remove-item-btn').prop('disabled', true);
            } else {
                $('.remove-item-btn').prop('disabled', false);
            }
        }

        $('form').on('submit', function(e) {
            let assignId = $('#job_worker_assign_id').val();
            if (assignId && assignedItems.length > 0) {
                let hasError = false;
                $('.item-select').each(function() {
                    let val = $(this).val();
                    if (val) {
                        let isAssigned = assignedItems.find(i => i.id == val);
                        if (!isAssigned) {
                            hasError = true;
                        }
                    }
                });
                if (hasError) {
                    alert('Error: One or more selected items are not assigned to this job worker assignment.');
                    e.preventDefault();
                    return false;
                }
            }
        });
        
        // Initial Calculation
        calculateTotals();
    });
</script>
@endsection
