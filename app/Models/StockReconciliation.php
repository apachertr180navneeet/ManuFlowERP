<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class StockReconciliation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'reconciliation_date',
        'reference_no',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'reconciliation_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function items()
    {
        return $this->hasMany(StockReconciliationItem::class, 'reconciliation_id');
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
