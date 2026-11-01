<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitChild extends Model
{
    protected $fillable = [
        'unit_id',
        'child_name',
        'conversion_value',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
