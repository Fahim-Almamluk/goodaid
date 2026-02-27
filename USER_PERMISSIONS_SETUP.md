# إعداد صفحة إدارة صلاحيات المستخدم

## خطوات التشغيل

### 1. تشغيل Migrations
```bash
php artisan migrate
```

### 2. تشغيل Seeders
```bash
php artisan db:seed --class=PermissionSeeder
```

أو لتشغيل جميع الـ seeders:
```bash
php artisan migrate --seed
```

### 3. الوصول للصفحة
بعد تسجيل الدخول، قم بزيارة:
```
/admin/user-permissions
```

## الملفات المُنشأة

### Migrations
- `database/migrations/2025_12_29_183258_create_permissions_table.php`
- `database/migrations/2025_12_29_183309_create_user_permissions_table.php`

### Models
- `app/Models/Permission.php`
- تحديث `app/Models/User.php` (إضافة علاقة permissions)

### Seeders
- `database/seeders/PermissionSeeder.php`

### Controllers
- `app/Http/Controllers/Admin/UserPermissionsController.php`

### Routes
تمت إضافة Routes في `routes/web.php`:
- `GET /admin/user-permissions` - صفحة إدارة الصلاحيات
- `GET /admin/api/users` - البحث عن المستخدمين
- `GET /admin/api/user-permissions` - جلب صلاحيات المستخدم
- `PATCH /admin/api/user-permissions/sync` - حفظ التغييرات

### Views
- `resources/views/admin/user-permissions/index.blade.php`

### JavaScript
- `public/js/user-permissions.js`

### Tests
- `tests/Feature/UserPermissionsTest.php`

## الميزات

1. **بحث المستخدمين**: ابحث بالاسم أو البريد الإلكتروني
2. **فلترة الصلاحيات**: الكل / مفعلة / غير مفعلة
3. **بحث في الصلاحيات**: ابحث في أسماء أو مفاتيح الصلاحيات
4. **تفعيل/تعطيل جماعي**: للمرئي فقط
5. **Tri-state checkbox**: لكل قسم (Module)
6. **Expand/Collapse**: توسيع وطي الأقسام
7. **حفظ التغييرات**: زر حفظ يرسل التغييرات فقط

## الصلاحيات المُنشأة

الـ Seeder ينشئ الصلاحيات التالية:

1. **الامتثال والسلامة** (5 صلاحيات)
2. **إدارة المستفيدين** (6 صلاحيات)
3. **إدارة الطرود** (6 صلاحيات)
4. **إدارة التوزيعات** (3 صلاحيات)
5. **إدارة المستخدمين** (5 صلاحيات)
6. **التقارير والإحصائيات** (3 صلاحيات)

## الاختبارات

لتشغيل الاختبارات:
```bash
php artisan test --filter UserPermissionsTest
```

