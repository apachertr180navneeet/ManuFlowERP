@extends('admin.layouts.app')

@section('page_title', 'Job Assign')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-default">
            <div class="card-header pb-0 border-bottom-0 text-center">
                <h5 class="mb-0">Add Job Assign</h5>
            </div>
            
            <form action="{{ route('company.job-worker-assign.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <h6 class="mb-3">Job Assignment Information</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">JOB WORK ASSIGN NO.</label>
                            <input type="text" name="assign_no" class="form-control" value="{{ old('assign_no') }}" placeholder="Enter Assign No" required>
                            @error('assign_no') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">JOB WORKER</label>
                            <select name="job_worker_id" class="form-select" id="job_worker_id" required>
                                <option value="">Select Job Worker</option>
                                @foreach($jobWorkers as $worker)
                                    <option value="{{ $worker->id }}" data-contact="{{ $worker->phone }}" {{ old('job_worker_id') == $worker->id ? 'selected' : '' }}>{{ $worker->name }}</option>
                                @endforeach
                            </select>
                            @error('job_worker_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CONTACT NUMBER</label>
                            <input type="text" name="contact_number" id="contact_number" class="form-control" value="{{ old('contact_number') }}" placeholder="Auto" readonly>
                            @error('contact_number') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">TRANSPORT NAME</label>
                            <input type="text" name="transport_name" class="form-control" value="{{ old('transport_name') }}" placeholder="Enter Transport Name">
                            @error('transport_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <h6 class="mb-3 mt-4">Item detail</h6>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ITEM</th>
                                    <th>QTY</th>
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
                                                <input type="number" name="items[{{ $index }}][qty]" class="form-control qty-input" step="0.01" min="0.01" value="{{ $itemData['qty'] }}" required>
                                                @error("items.{$index}.qty") <small class="text-danger">{{ $message }}</small> @enderror
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
                                                <option value="">Select Item</option>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}" data-unit="{{ $item->unit->unit_name ?? '' }}">{{ $item->item_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][qty]" class="form-control qty-input" step="0.01" min="0.01" required>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][unit]" class="form-control unit-input" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][rate]" class="form-control rate-input" step="0.01" min="0" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][amount]" class="form-control amount-input" step="0.01" min="0" readonly required>
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

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('company.job-worker-assign.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save</button>
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
        // Auto-fill contact number
        $('#job_worker_id').on('change', function() {
            let contact = $(this).find(':selected').data('contact');
            $('#contact_number').val(contact ? contact : '');
        });
        if($('#job_worker_id').val()) {
            $('#job_worker_id').trigger('change');
        }

        let itemIndex = {{ old('items') ? count(old('items')) : 1 }};

        // Add Item Row
        $('#addItemBtn').on('click', function() {
            let rowCount = $('#itemsTable tbody tr').length + 1;
            let newRow = `
                <tr class="item-row">
                    <td class="row-number">${rowCount}</td>
                    <td>
                        <select name="items[${itemIndex}][item_id]" class="form-select item-select" required>
                            <option value="">Select Item</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" data-unit="{{ $item->unit->unit_name ?? '' }}">{{ $item->item_name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][qty]" class="form-control qty-input" step="0.01" min="0.01" required>
                    </td>
                    <td>
                        <input type="text" name="items[${itemIndex}][unit]" class="form-control unit-input" readonly>
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][rate]" class="form-control rate-input" step="0.01" min="0" required>
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][amount]" class="form-control amount-input" step="0.01" min="0" readonly required>
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
            }
        });

        // Auto-fill unit based on item selection
        $(document).on('change', '.item-select', function() {
            let unit = $(this).find(':selected').data('unit');
            $(this).closest('tr').find('.unit-input').val(unit ? unit : '');
        });

        // Calculate amount
        $(document).on('input', '.qty-input, .rate-input', function() {
            let row = $(this).closest('tr');
            let qty = parseFloat(row.find('.qty-input').val()) || 0;
            let rate = parseFloat(row.find('.rate-input').val()) || 0;
            let amount = qty * rate;
            row.find('.amount-input').val(amount.toFixed(2));
        });

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
    });
</script>
@endsection
