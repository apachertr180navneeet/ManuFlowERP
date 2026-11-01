<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'owner_name',
        'mobile',
        'email',
        'gst_number',
        'address',
        'city',
        'state',
        'pin_code',
        'logo',
        'status',
        'created_by',
    ];

    protected $appends = ['logo_full_path'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getLogoFullPathAttribute()
    {
        if ($this->logo) {
            return asset($this->logo);
        }
        return '';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
