<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class CostSheet extends Model
{
    use SoftDeletes;

    protected $table = 'manufacturing_cost_sheets';

    protected $fillable = [
        'company_id',
        'manufacturing_id',
        'bom_no',
        'date',
        'product_id',
        'qty',
        'unit_weight',
        'total_weight',
        'overall_mfg_weight',
        'raw_material_cost',
        'expense_cost',
        'total_cost',
        'profit_percent',
        'profit_amount',
        'selling_price',
        'unit_price',
        'status',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function product()
    {
        return $this->belongsTo(Item::class, 'product_id');
    }

    public function manufacturing()
    {
        return $this->belongsTo(Manufacturing::class);
    }

    public function items()
    {
        return $this->hasMany(CostSheetItem::class, 'cost_sheet_id');
    }

    public function expenses()
    {
        return $this->hasMany(CostSheetExpense::class, 'cost_sheet_id');
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForCompany($query)
    {
        $user = Auth::user();
        if ($user && $user->role !== 'admin') {
            return $query->where('company_id', $user->company_id);
        }
        return $query;
    }
}
