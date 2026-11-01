<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cost Sheet - {{ $costSheet->bom_no }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 18px; color: #4338ca; }
        .header p { margin: 2px 0; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 6px 8px; border: 1px solid #ddd; text-align: left; font-size: 11px; }
        th { background: #f3f4f6; font-weight: 700; font-size: 10px; text-transform: uppercase; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: 700; }
        .text-success { color: #059669; }
        .text-indigo { color: #4338ca; }
        .summary-box { border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; }
        .summary-box .label { font-size: 10px; color: #666; font-weight: 600; }
        .summary-box .value { font-size: 14px; font-weight: 700; }
        .footer { text-align: center; font-size: 10px; color: #999; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Manufacturing Cost Sheet</h2>
        <p>BOM No: <strong>{{ $costSheet->bom_no }}</strong> | Date: {{ $costSheet->date->format('d-m-Y') }}</p>
        <p>Product: <strong>{{ $costSheet->product->item_name ?? '—' }}</strong> | Quantity: {{ $costSheet->qty }}</p>
        <p>Status: <strong>{{ $costSheet->status }}</strong></p>
    </div>

    <h4 style="margin:10px 0 5px;font-size:13px">Raw Material Consumption</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Raw Material</th>
                <th class="text-center">Required Qty</th>
                <th class="text-center">FIFO Rate</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($costSheet->items as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $item->rawMaterial->item_name ?? '—' }}</td>
                <td class="text-center">{{ $item->required_qty }} {{ $item->unit_name }}</td>
                <td class="text-center">{{ number_format($item->fifo_rate, 2) }}</td>
                <td class="text-end">{{ number_format($item->amount, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">No items.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background:#f3f4f6">
                <td colspan="4" class="text-end fw-bold">Raw Material Total:</td>
                <td class="text-end fw-bold">{{ number_format($costSheet->raw_material_cost, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <h4 style="margin:10px 0 5px;font-size:13px">Factory Expenses</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Expense</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($costSheet->expenses as $i => $exp)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $exp->expense_name }}</td>
                <td class="text-end">{{ number_format($exp->amount, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center">No expenses.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background:#f3f4f6">
                <td colspan="2" class="text-end fw-bold">Expense Total:</td>
                <td class="text-end fw-bold">{{ number_format($costSheet->expense_cost, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="display:flex;gap:15px;margin-top:15px">
        <div class="summary-box" style="flex:1">
            <div class="label">Raw Material Cost</div>
            <div class="value">₹ {{ number_format($costSheet->raw_material_cost, 2) }}</div>
        </div>
        <div class="summary-box" style="flex:1">
            <div class="label">Factory Expense Cost</div>
            <div class="value">₹ {{ number_format($costSheet->expense_cost, 2) }}</div>
        </div>
        <div class="summary-box" style="flex:1">
            <div class="label">Total Manufacturing Cost</div>
            <div class="value">₹ {{ number_format($costSheet->total_cost, 2) }}</div>
        </div>
        <div class="summary-box" style="flex:1">
            <div class="label">Profit ({{ $costSheet->profit_percent }}%)</div>
            <div class="value text-success">₹ {{ number_format($costSheet->profit_amount, 2) }}</div>
        </div>
        <div class="summary-box" style="flex:1;border-color:#c7d2fe;background:#eef2ff">
            <div class="label" style="color:#4338ca">SELLING PRICE</div>
            <div class="value text-indigo">₹ {{ number_format($costSheet->selling_price, 2) }}</div>
        </div>
    </div>

    <div class="footer">
        Generated on {{ date('d-m-Y H:i:s') }} | Manufacturing Cost Sheet System
    </div>
</body>
</html>
