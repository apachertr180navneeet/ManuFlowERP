<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingDetail extends Model
{
    protected $fillable = [
        'manufacturing_id',
        'raw_material_id',
        'required_qty',
        'consumed_qty',
    ];

    public function manufacturing()
    {
        return $this->belongsTo(Manufacturing::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(Item::class, 'raw_material_id');
    }
}
