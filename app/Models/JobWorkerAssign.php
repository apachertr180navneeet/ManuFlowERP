<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobWorkerAssign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'assign_no',
        'date',
        'job_worker_id',
        'contact_number',
        'transport_name',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function jobWorker()
    {
        return $this->belongsTo(JobWorker::class);
    }

    public function items()
    {
        return $this->hasMany(JobWorkerAssignItem::class);
    }
}
