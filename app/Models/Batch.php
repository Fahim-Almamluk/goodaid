<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'batch_date',
        'notes',
        'status',
        'quantity',
    ];

    protected $casts = [
        'batch_date' => 'date',
        'quantity' => 'integer',
    ];

    public function beneficiaries()
    {
        return $this->belongsToMany(Beneficiary::class, 'batch_recipients')
            ->withPivot('received', 'received_at', 'approved_by')
            ->withTimestamps();
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function getTypeNameAttribute()
    {
        return match($this->type) {
            'food' => 'غذائي',
            'health' => 'صحي',
            'clothes' => 'ملابس',
            default => 'غير محدد',
        };
    }

    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'draft' => 'جديد',
            'active' => 'معتمد',
            default => 'غير محدد',
        };
    }
}
