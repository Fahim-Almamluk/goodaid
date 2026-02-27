<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'beneficiary_id',
        'name',
        'national_id',
        'birth_date',
        'relationship',
        'is_pregnant',
        'is_nursing',
        'order',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_pregnant' => 'boolean',
        'is_nursing' => 'boolean',
    ];

    public function getAgeAttribute()
    {
        if ($this->birth_date) {
            return $this->birth_date->age;
        }
        return null;
    }

    public function getIsChildAttribute()
    {
        if ($this->birth_date) {
            return $this->birth_date->age < 18;
        }
        return false;
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
