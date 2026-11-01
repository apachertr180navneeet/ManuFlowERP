<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ItemAssignment extends Model
{
    use SoftDeletes;

    protected $table = 'item_assignments';

    protected $fillable = [
        'company_id',
        'finished_item_id',
        'raw_material_id',
        'quantity',
        'unit_name',
        'status',
        'created_by',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function finishedItem()
    {
        return $this->belongsTo(Item::class, 'finished_item_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(Item::class, 'raw_material_id');
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
