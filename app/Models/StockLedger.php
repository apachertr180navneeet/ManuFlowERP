<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLedger extends Model
{
    protected $fillable = [
        'company_id',
        'item_id',
        'transaction_type',
        'reference_id',
        'batch_id',
        'qty_in',
        'qty_out',
        'balance_qty',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function batch()
    {
        return $this->belongsTo(PurchaseBatch::class, 'batch_id');
    }
}
