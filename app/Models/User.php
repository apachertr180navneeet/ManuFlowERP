<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'full_name',
        'username',
        'slug',
        'email',
        'phone',
        'password',
        'role',
        'address',
        'area',
        'city',
        'state',
        'country',
        'country_code',
        'zipcode',
        'latitude',
        'longitude',
        'timezone',
        'avatar',
        'bio',
        'device_token',
        'device_type',
        'status',
    ];

    protected $appends = ['avatar_full_path'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    public function getAvatarFullPathAttribute()
    {
        if($this->avatar != ''){
            return asset($this->avatar);
        }else{
            return "";
        }
    }

    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

}
