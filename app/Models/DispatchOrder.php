<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DispatchOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'dispatch_date',
        'challan_no',
        'customer_id',
        'customer_mobile',
        'transport_name',
        'dispatch_status',
        'total_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'dispatch_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(DispatchOrderItem::class);
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
