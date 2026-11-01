<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchOrderItem extends Model
{
    protected $fillable = [
        'dispatch_order_id',
        'lot_no',
        'item_id',
        'qty',
        'weight',
        'rate',
        'amount',
    ];

    public function dispatchOrder()
    {
        return $this->belongsTo(DispatchOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
