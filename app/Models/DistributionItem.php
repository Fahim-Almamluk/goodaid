<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributionItem extends Model
{
    protected $fillable = [
        'distribution_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function distribution()
    {
        return $this->belongsTo(Distribution::class);
    }
}
