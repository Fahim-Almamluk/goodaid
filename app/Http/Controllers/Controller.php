<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Check if the authenticated user has a permission.
     */
    protected function hasPermission(string $permissionKey): bool
    {
        return auth()->check() && auth()->user()->hasPermission($permissionKey);
    }

    /**
     * Abort if user doesn't have permission.
     */
    protected function requirePermission(string $permissionKey): void
    {
        if (!$this->hasPermission($permissionKey)) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
        }
    }
}
