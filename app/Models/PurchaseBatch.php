<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseBatch extends Model
{
    protected $fillable = [
        'company_id',
        'purchase_id',
        'item_id',
        'received_qty',
        'consumed_qty',
        'balance_qty',
        'purchase_date',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
