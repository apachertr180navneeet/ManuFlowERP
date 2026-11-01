<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostSheetExpense extends Model
{
    protected $table = 'manufacturing_cost_sheet_expenses';

    protected $fillable = [
        'cost_sheet_id',
        'expense_name',
        'amount',
        'overall_amount',
    ];

    public function costSheet()
    {
        return $this->belongsTo(CostSheet::class, 'cost_sheet_id');
    }
}
