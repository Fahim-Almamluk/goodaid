<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserPermissionsController extends Controller
{
    /**
     * Display the user permissions management page.
     */
    public function index()
    {
        $users = User::orderBy('username')->get(['id', 'name', 'username', 'email']);
        return view('admin.user-permissions.index', compact('users'));
    }

    /**
     * Search users for the dropdown.
     */
    public function apiUsers(Request $request)
    {
        $q = $request->get('q', '');
        
        $users = User::query()
            ->where(function ($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('email', 'LIKE', "%{$q}%")
                    ->orWhere('username', 'LIKE', "%{$q}%");
            })
            ->limit(20)
            ->get(['id', 'name', 'email', 'username'])
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                ];
            });

        return response()->json(['data' => $users]);
    }

    /**
     * Get user permissions with filters.
     */
    public function apiUserPermissions(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->get('user_id');
        $q = $request->get('q', '');
        $status = $request->get('status', 'all');

        // Get user
        $user = User::findOrFail($userId);

        // Get assigned permission IDs
        $assignedIds = $user->permissions()->pluck('permissions.id')->toArray();

        // Build query
        $query = Permission::query();

        // Search filter
        if ($q) {
            $query->where(function ($qry) use ($q) {
                $qry->where('label', 'LIKE', "%{$q}%")
                    ->orWhere('key', 'LIKE', "%{$q}%")
                    ->orWhere('module', 'LIKE', "%{$q}%");
            });
        }

        // Status filter
        if ($status === 'assigned') {
            $query->whereIn('id', $assignedIds);
        } elseif ($status === 'unassigned') {
            $query->whereNotIn('id', $assignedIds);
        }

        // Sort by module, order, then label
        $permissions = $query->orderBy('module')
            ->orderBy('order')
            ->orderBy('label')
            ->get();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
            ],
            'permissions' => $permissions,
            'assigned_ids' => $assignedIds,
        ]);
    }

    /**
     * Sync user permissions.
     */
    public function sync(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'give_ids' => 'array',
                'give_ids.*' => 'exists:permissions,id',
                'revoke_ids' => 'array',
                'revoke_ids.*' => 'exists:permissions,id',
            ], [
                'user_id.required' => 'معرف المستخدم مطلوب',
                'user_id.exists' => 'المستخدم المحدد غير موجود',
                'give_ids.array' => 'معرفات الصلاحيات المضافة يجب أن تكون مصفوفة',
                'give_ids.*.exists' => 'أحد الصلاحيات المضافة غير موجود',
                'revoke_ids.array' => 'معرفات الصلاحيات المحذوفة يجب أن تكون مصفوفة',
                'revoke_ids.*.exists' => 'أحد الصلاحيات المحذوفة غير موجود',
            ]);

            $userId = $validated['user_id'];
            $giveIds = $validated['give_ids'] ?? [];
            $revokeIds = $validated['revoke_ids'] ?? [];

            $user = User::findOrFail($userId);

            DB::beginTransaction();
            try {
                if (!empty($giveIds)) {
                    $user->permissions()->syncWithoutDetaching($giveIds);
                }

                if (!empty($revokeIds)) {
                    $user->permissions()->detach($revokeIds);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'ok' => false,
                    'message' => 'حدث خطأ أثناء حفظ الصلاحيات: ' . $e->getMessage(),
                ], 500);
            }

            $assignedCount = $user->permissions()->count();

            return response()->json([
                'ok' => true,
                'assigned_count' => $assignedCount,
                'message' => 'تم حفظ الصلاحيات بنجاح',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'ok' => false,
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'حدث خطأ غير متوقع: ' . $e->getMessage(),
            ], 500);
        }
    }
}
