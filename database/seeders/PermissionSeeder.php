<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // لوحة التحكم
            [
                'key' => 'dashboard.view',
                'label' => 'عرض لوحة التحكم',
                'module' => 'لوحة التحكم',
                'description' => 'إمكانية عرض لوحة التحكم والإحصائيات',
                'order' => 1,
            ],

            // إدارة المستفيدين
            [
                'key' => 'beneficiaries.view',
                'label' => 'عرض المستفيدين',
                'module' => 'إدارة المستفيدين',
                'description' => 'إمكانية عرض قائمة المستفيدين',
                'order' => 1,
            ],
            [
                'key' => 'beneficiaries.create',
                'label' => 'إنشاء مستفيد',
                'module' => 'إدارة المستفيدين',
                'description' => 'إمكانية إضافة مستفيد جديد',
                'order' => 2,
            ],
            [
                'key' => 'beneficiaries.edit',
                'label' => 'تعديل مستفيد',
                'module' => 'إدارة المستفيدين',
                'description' => 'إمكانية تعديل بيانات المستفيد',
                'order' => 3,
            ],
            [
                'key' => 'beneficiaries.delete',
                'label' => 'حذف مستفيد',
                'module' => 'إدارة المستفيدين',
                'description' => 'إمكانية حذف مستفيد',
                'order' => 4,
            ],
            [
                'key' => 'beneficiaries.export_excel',
                'label' => 'تصدير بيانات المستفيدين',
                'module' => 'إدارة المستفيدين',
                'description' => 'إمكانية تصدير بيانات المستفيدين',
                'order' => 5,
            ],
            [
                'key' => 'beneficiaries.toggle_status',
                'label' => 'تفعيل/تعطيل حالة المستفيد',
                'module' => 'إدارة المستفيدين',
                'description' => 'إمكانية تغيير حالة المستفيد',
                'order' => 6,
            ],

            // إدارة الطرود
            [
                'key' => 'batches.view',
                'label' => 'عرض الطرود',
                'module' => 'إدارة الطرود',
                'description' => 'إمكانية عرض قائمة الطرود',
                'order' => 1,
            ],
            [
                'key' => 'batches.create',
                'label' => 'إنشاء طرد',
                'module' => 'إدارة الطرود',
                'description' => 'إمكانية إنشاء طرد جديد',
                'order' => 2,
            ],
            [
                'key' => 'batches.edit',
                'label' => 'تعديل طرد',
                'module' => 'إدارة الطرود',
                'description' => 'إمكانية تعديل بيانات الطرد',
                'order' => 3,
            ],
            [
                'key' => 'batches.delete',
                'label' => 'حذف طرد',
                'module' => 'إدارة الطرود',
                'description' => 'إمكانية حذف طرد',
                'order' => 4,
            ],
            [
                'key' => 'batches.manage_beneficiaries',
                'label' => 'إدارة مستلمي الطرد',
                'module' => 'إدارة الطرود',
                'description' => 'إمكانية إدارة قائمة المستلمين للطرد',
                'order' => 5,
            ],
            [
                'key' => 'batches.approve',
                'label' => 'الموافقة على الطرد',
                'module' => 'إدارة الطرود',
                'description' => 'إمكانية الموافقة على الطرود',
                'order' => 6,
            ],
            [
                'key' => 'batches.toggle_received',
                'label' => 'تغيير حالة الاستلام',
                'module' => 'إدارة الطرود',
                'description' => 'إمكانية تغيير حالة استلام الطرد',
                'order' => 7,
            ],
            [
                'key' => 'batches.import_excel',
                'label' => 'استيراد مستفيدين (Excel)',
                'module' => 'إدارة الطرود',
                'description' => 'إمكانية استيراد مستفيدين من ملف Excel',
                'order' => 8,
            ],

            // إدارة التوزيعات
            [
                'key' => 'distributions.view',
                'label' => 'عرض التوزيعات',
                'module' => 'إدارة التوزيعات',
                'description' => 'إمكانية عرض قائمة التوزيعات',
                'order' => 1,
            ],
            [
                'key' => 'distributions.create',
                'label' => 'إنشاء توزيع',
                'module' => 'إدارة التوزيعات',
                'description' => 'إمكانية إنشاء توزيع جديد',
                'order' => 2,
            ],
            [
                'key' => 'distributions.delete',
                'label' => 'حذف توزيع',
                'module' => 'إدارة التوزيعات',
                'description' => 'إمكانية حذف توزيع',
                'order' => 3,
            ],

            // إدارة المستخدمين
            [
                'key' => 'users.view',
                'label' => 'عرض المستخدمين',
                'module' => 'إدارة المستخدمين',
                'description' => 'إمكانية عرض قائمة المستخدمين',
                'order' => 1,
            ],
            [
                'key' => 'users.create',
                'label' => 'إنشاء مستخدم',
                'module' => 'إدارة المستخدمين',
                'description' => 'إمكانية إضافة مستخدم جديد',
                'order' => 2,
            ],
            [
                'key' => 'users.edit',
                'label' => 'تعديل مستخدم',
                'module' => 'إدارة المستخدمين',
                'description' => 'إمكانية تعديل بيانات المستخدم',
                'order' => 3,
            ],
            [
                'key' => 'users.delete',
                'label' => 'حذف مستخدم',
                'module' => 'إدارة المستخدمين',
                'description' => 'إمكانية حذف مستخدم',
                'order' => 4,
            ],
            [
                'key' => 'users.manage_permissions',
                'label' => 'إدارة صلاحيات المستخدمين',
                'module' => 'إدارة المستخدمين',
                'description' => 'إمكانية إدارة صلاحيات المستخدمين',
                'order' => 5,
            ],

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['key' => $permission['key']],
                $permission
            );
        }
    }
}
