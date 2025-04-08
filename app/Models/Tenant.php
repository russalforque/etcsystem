<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'id_number',
        'date_of_birth',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone'
    ];

    protected $casts = [
        'date_of_birth' => 'date'
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function currentContract()
    {
        return $this->hasOne(Contract::class)->where('status', 'active');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
} 