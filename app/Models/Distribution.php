<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distribution extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'beneficiary_id',
        'distributed_by',
        'distributed_at',
        'notes',
    ];

    protected $casts = [
        'distributed_at' => 'datetime',
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }

    public function items()
    {
        return $this->hasMany(DistributionItem::class);
    }
}
