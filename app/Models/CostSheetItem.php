<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostSheetItem extends Model
{
    protected $table = 'manufacturing_cost_sheet_items';

    protected $fillable = [
        'cost_sheet_id',
        'raw_material_id',
        'required_qty',
        'unit_name',
        'fifo_rate',
        'amount',
    ];

    public function costSheet()
    {
        return $this->belongsTo(CostSheet::class, 'cost_sheet_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(Item::class, 'raw_material_id');
    }
}
