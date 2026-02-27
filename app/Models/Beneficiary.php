<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Beneficiary extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'relationship',
        'address',
        'national_id',
        'username',
        'password',
        'password_set_at',
        'has_set_password',
        'residence_status',
        'number_of_members',
        'notes',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class)->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'batch_recipients')
            ->withPivot('received', 'received_at', 'approved_by')
            ->withTimestamps();
    }

    /**
     * تحويل قيمة العلاقة إلى التنسيق الصحيح
     */
    public function getFormattedRelationshipAttribute()
    {
        if (!$this->relationship) {
            return null;
        }

        // تحويل القيم القديمة إلى التنسيق الصحيح لتوحيد التنسيق
        $formatted = $this->relationship;
        
        // تحويل "زوج/زوجة" إلى "زوج/ة"
        $formatted = str_replace('زوج/زوجة', 'زوج/ة', $formatted);
        
        // تحويل "أرمل/أرملة" إلى "أرمل/ة"
        $formatted = str_replace('أرمل/أرملة', 'أرمل/ة', $formatted);
        
        return $formatted;
    }

    /**
     * الحصول على جميع القيم الممكنة للعلاقة (الجديدة والقديمة)
     * لاستخدامها في البحث
     */
    public static function getRelationshipValuesForSearch($relationship)
    {
        $values = [$relationship];
        
        // إذا كانت القيمة هي "زوج/ة"، أضف "زوج/زوجة" أيضاً
        if ($relationship === 'زوج/ة') {
            $values[] = 'زوج/زوجة';
        }
        
        // إذا كانت القيمة هي "أرمل/ة"، أضف "أرمل/أرملة" أيضاً
        if ($relationship === 'أرمل/ة') {
            $values[] = 'أرمل/أرملة';
        }
        
        // العكس: إذا كانت القيمة القديمة، أضف القيمة الجديدة
        if ($relationship === 'زوج/زوجة') {
            $values[] = 'زوج/ة';
        }
        
        if ($relationship === 'أرمل/أرملة') {
            $values[] = 'أرمل/ة';
        }
        
        return array_unique($values);
    }
}
