<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobWorkInward extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'job_worker_assign_id',
        'inward_date',
        'inward_no',
        'challan_no',
        'transport_name',
        'lr_number',
        'remark',
        'subtotal',
        'other_charges',
        'discount',
        'transport_charges',
        'total_amount',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function jobWorkerAssign()
    {
        return $this->belongsTo(JobWorkerAssign::class);
    }

    public function items()
    {
        return $this->hasMany(JobWorkInwardItem::class);
    }
}
