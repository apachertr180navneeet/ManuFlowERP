<?php

namespace App\Exports;

use App\Models\CostSheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Auth;

class CostSheetExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = CostSheet::with('product');

        $user = Auth::user();
        if ($user && $user->role !== 'admin') {
            $query->where('company_id', $user->company_id);
        }

        if ($this->request->filled('search')) {
            $s = $this->request->search;
            $query->where(function ($q) use ($s) {
                $q->where('bom_no', 'like', "%{$s}%")
                  ->orWhereHas('product', function ($qv) use ($s) {
                      $qv->where('item_name', 'like', "%{$s}%");
                  });
            });
        }

        if ($this->request->filled('product_id')) {
            $query->where('product_id', $this->request->product_id);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return ['Date', 'BOM No', 'Product', 'Qty', 'Material Cost', 'Expense Cost', 'Total Cost', 'Profit %', 'Profit Amt', 'Selling Price', 'Status'];
    }

    public function map($cs): array
    {
        return [
            $cs->date->format('d-m-Y'),
            $cs->bom_no,
            $cs->product->item_name ?? '—',
            $cs->qty,
            $cs->raw_material_cost,
            $cs->expense_cost,
            $cs->total_cost,
            $cs->profit_percent,
            $cs->profit_amount,
            $cs->selling_price,
            $cs->status,
        ];
    }
}
