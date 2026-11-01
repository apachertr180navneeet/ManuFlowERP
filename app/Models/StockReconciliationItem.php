<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReconciliationItem extends Model
{
    protected $fillable = [
        'reconciliation_id',
        'item_id',
        'system_qty',
        'physical_qty',
        'difference_qty',
        'rate',
        'adjustment_amount',
        'remarks',
    ];

    public function reconciliation()
    {
        return $this->belongsTo(StockReconciliation::class, 'reconciliation_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
