<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Purchase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'vendor_id',
        'purchase_date',
        'invoice_no',
        'bno',
        'challan_no',
        'transport',
        'lr_no',
        'subtotal',
        'tax_amount',
        'gst_percentage',
        'total_amount',
        'discount',
        'transport_charges',
        'other_charges',
        'purchase_status',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
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
