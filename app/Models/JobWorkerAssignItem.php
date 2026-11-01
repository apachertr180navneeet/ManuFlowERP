<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobWorkerAssignItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_worker_assign_id',
        'item_id',
        'qty',
        'unit',
        'rate',
        'amount',
    ];

    public function jobWorkerAssign()
    {
        return $this->belongsTo(JobWorkerAssign::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class); // Assuming Item model exists
    }
}
