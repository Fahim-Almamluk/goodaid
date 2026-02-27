<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'id_number',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the permissions that belong to the user.
     */
    public function permissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->withTimestamps();
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permissionKey): bool
    {
        // المدير يمتلك جميع الصلاحيات بشكل افتراضي
        if ($this->role === 'admin') {
            return true;
        }

        return $this->permissions()->where('key', $permissionKey)->exists();
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissionKeys): bool
    {
        // المدير يمتلك جميع الصلاحيات بشكل افتراضي
        if ($this->role === 'admin') {
            return true;
        }

        return $this->permissions()->whereIn('key', $permissionKeys)->exists();
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissionKeys): bool
    {
        // المدير يمتلك جميع الصلاحيات بشكل افتراضي
        if ($this->role === 'admin') {
            return true;
        }

        $userPermissionKeys = $this->permissions()->pluck('key')->toArray();
        return count(array_intersect($permissionKeys, $userPermissionKeys)) === count($permissionKeys);
    }

    /**
     * Get all permission keys for the user.
     */
    public function getPermissionKeys(): array
    {
        return $this->permissions()->pluck('key')->toArray();
    }
}
