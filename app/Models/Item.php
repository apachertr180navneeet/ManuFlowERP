<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'short_code',
        'item_name',
        'item_type',
        'unit_id',
        'size',
        'status',
        'created_by',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
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
