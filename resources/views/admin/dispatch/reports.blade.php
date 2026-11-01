@extends('admin.layouts.app')
@section('page_title', 'Dispatch Reports')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.dispatch.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-bar-chart"></i> Dispatch Reports</h5>
    </div>
    <div class="p-4">
        <form method="GET" action="{{ route('admin.dispatch.reports') }}" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Report Type</label>
                    <select name="report_type" class="form-select form-select-sm">
                        <option value="summary" {{ $reportType == 'summary' ? 'selected' : '' }}>Summary</option>
                        <option value="customer" {{ $reportType == 'customer' ? 'selected' : '' }}>Customer-wise</option>
                        <option value="transport" {{ $reportType == 'transport' ? 'selected' : '' }}>Transport-wise</option>
                        <option value="status" {{ $reportType == 'status' ? 'selected' : '' }}>Status-wise</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-select form-select-sm">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->customer_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Transit" {{ request('status') == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Transport</label>
                    <input type="text" name="transport_name" class="form-control form-control-sm" placeholder="Transport name" value="{{ request('transport_name') }}">
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3" style="border-radius:.75rem;font-weight:600"><i class="bi bi-filter me-1"></i> Generate Report</button>
                <a href="{{ route('admin.dispatch.reports') }}" class="btn btn-light btn-sm px-3" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
                <button type="button" class="btn btn-success btn-sm px-3 ms-auto" style="border-radius:.75rem;font-weight:600" onclick="exportExcel()"><i class="bi bi-file-earmark-excel me-1"></i> Excel</button>
                <button type="button" class="btn btn-danger btn-sm px-3" style="border-radius:.75rem;font-weight:600" onclick="exportPDF()"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</button>
                <button type="button" class="btn btn-secondary btn-sm px-3" style="border-radius:.75rem;font-weight:600" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print</button>
            </div>
        </form>

        @if($reportType == 'summary' || !request('report_type'))
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:.8125rem" id="reportTable">
                <thead>
                    <tr class="text-uppercase text-secondary" style="font-size:.6875rem;font-weight:700;background:#f8fafc">
                        <th class="px-3 py-2">#</th>
                        <th class="px-3 py-2">Date</th>
                        <th class="px-3 py-2">Challan No</th>
                        <th class="px-3 py-2">Customer</th>
                        <th class="px-3 py-2">Transport</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2 text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dispatchOrders as $dispatch)
                    <tr>
                        <td class="px-3 text-secondary">{{ $loop->iteration }}</td>
                        <td>{{ $dispatch->dispatch_date->format('d-m-Y') }}</td>
                        <td class="fw-bold text-dark">{{ $dispatch->challan_no }}</td>
                        <td>{{ $dispatch->customer->customer_name ?? '—' }}</td>
                        <td>{{ $dispatch->transport_name }}</td>
                        <td>
                            <span class="badge bg-{{ $dispatch->dispatch_status == 'Delivered' ? 'success' : ($dispatch->dispatch_status == 'Cancelled' ? 'danger' : ($dispatch->dispatch_status == 'In Transit' ? 'info' : 'warning')) }} rounded-pill px-3 py-1" style="font-size:.6875rem">{{ $dispatch->dispatch_status }}</span>
                        </td>
                        <td class="text-end fw-bold text-dark">₹ {{ number_format($dispatch->total_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-secondary">No dispatch records found.</td></tr>
                    @endforelse
                </tbody>
                @if($dispatchOrders->count())
                <tfoot>
                    <tr class="fw-bold" style="background:#f8fafc">
                        <td colspan="6" class="px-3 py-2 text-end text-dark">Grand Total:</td>
                        <td class="px-3 py-2 text-end text-dark">₹ {{ number_format($dispatchOrders->sum('total_amount'), 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @endif

        @if($reportType == 'customer')
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:.8125rem" id="reportTable">
                <thead>
                    <tr class="text-uppercase text-secondary" style="font-size:.6875rem;font-weight:700;background:#f8fafc">
                        <th class="px-3 py-2">#</th>
                        <th class="px-3 py-2">Customer Name</th>
                        <th class="px-3 py-2 text-center">Total Orders</th>
                        <th class="px-3 py-2 text-end">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customerWise as $cw)
                    <tr>
                        <td class="px-3 text-secondary">{{ $loop->iteration }}</td>
                        <td class="fw-bold text-dark">{{ $cw['customer_name'] }}</td>
                        <td class="text-center">{{ $cw['total_orders'] }}</td>
                        <td class="text-end fw-bold text-dark">₹ {{ number_format($cw['total_amount'], 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-secondary">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        @if($reportType == 'transport')
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:.8125rem" id="reportTable">
                <thead>
                    <tr class="text-uppercase text-secondary" style="font-size:.6875rem;font-weight:700;background:#f8fafc">
                        <th class="px-3 py-2">#</th>
                        <th class="px-3 py-2">Transport Name</th>
                        <th class="px-3 py-2 text-center">Total Orders</th>
                        <th class="px-3 py-2 text-end">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transportWise as $tw)
                    <tr>
                        <td class="px-3 text-secondary">{{ $loop->iteration }}</td>
                        <td class="fw-bold text-dark">{{ $tw['transport_name'] }}</td>
                        <td class="text-center">{{ $tw['total_orders'] }}</td>
                        <td class="text-end fw-bold text-dark">₹ {{ number_format($tw['total_amount'], 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-secondary">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        @if($reportType == 'status')
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:.8125rem" id="reportTable">
                <thead>
                    <tr class="text-uppercase text-secondary" style="font-size:.6875rem;font-weight:700;background:#f8fafc">
                        <th class="px-3 py-2">#</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2 text-center">Total Orders</th>
                        <th class="px-3 py-2 text-end">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statusWise as $sw)
                    <tr>
                        <td class="px-3 text-secondary">{{ $loop->iteration }}</td>
                        <td>
                            <span class="badge bg-{{ $sw['status'] == 'Delivered' ? 'success' : ($sw['status'] == 'Cancelled' ? 'danger' : ($sw['status'] == 'In Transit' ? 'info' : 'warning')) }} rounded-pill px-3 py-1" style="font-size:.6875rem">{{ $sw['status'] }}</span>
                        </td>
                        <td class="text-center">{{ $sw['total_orders'] }}</td>
                        <td class="text-end fw-bold text-dark">₹ {{ number_format($sw['total_amount'], 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-secondary">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
@section('script')
<script>
function exportExcel() {
    const table = document.getElementById('reportTable');
    if (!table) return;
    let html = '<table>' + table.outerHTML + '</table>';
    const blob = new Blob(['\uFEFF' + html], { type: 'application/vnd.ms-excel' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'dispatch_report_' + new Date().toISOString().slice(0,10) + '.xls';
    a.click();
}

function exportPDF() {
    const table = document.getElementById('reportTable');
    if (!table) return;
    const win = window.open('', '_blank');
    win.document.write(`
        <html><head><title>Dispatch Report</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>body{padding:20px;font-size:12px}table{width:100%}td,th{padding:6px 10px}</style>
        </head><body>
        <h4 style="margin-bottom:20px">Dispatch Report</h4>
        ${table.outerHTML}
        </body></html>
    `);
    win.document.close();
    setTimeout(() => { win.print(); }, 500);
}
</script>
@endsection
