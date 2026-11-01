<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobWorkInwardItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_work_inward_id',
        'item_id',
        'assigned_qty',
        'prev_rec_qty',
        'remaining_qty',
        'qty',
        'unit',
        'rate',
        'amount',
    ];

    public function inward()
    {
        return $this->belongsTo(JobWorkInward::class, 'job_work_inward_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
