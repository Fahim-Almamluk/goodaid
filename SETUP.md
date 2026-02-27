# دليل إعداد نظام GoodAid

## المتطلبات
- PHP 8.2 أو أحدث
- Composer
- Node.js و npm
- قاعدة بيانات (SQLite مدمجة)

## خطوات الإعداد

### 1. تثبيت Dependencies
```bash
composer install
npm install
```

### 2. إعداد البيئة
```bash
# نسخ ملف البيئة
cp .env.example .env

# إنشاء مفتاح التطبيق
php artisan key:generate
```

### 3. إعداد قاعدة البيانات
```bash
# تشغيل Migrations
php artisan migrate

# إنشاء مستخدم افتراضي (اختياري)
php artisan db:seed
```

### 4. بناء Assets
```bash
# للبيئة التطويرية
npm run dev

# للبيئة الإنتاجية
npm run build
```

### 5. تشغيل الخادم
```bash
php artisan serve
```

ثم افتح المتصفح على: `http://localhost:8000`

## المميزات

✅ إدارة المستفيدين (إضافة، تعديل، حذف، تفعيل/تعطيل)
✅ إدارة الطرود (غذائية، صحية، ملابس) مع تتبع الكميات
✅ سجل التوزيعات مع خصم تلقائي من المخزون
✅ لوحة تحكم بإحصائيات فورية
✅ تصميم عصري بألوان خضراء مع خط Tajawal
✅ واجهة RTL كاملة باللغة العربية

## ملاحظات مهمة

- تأكد من وجود مستخدم واحد على الأقل في قاعدة البيانات للتوزيعات
- يمكنك إنشاء مستخدم من خلال Tinker:
  ```bash
  php artisan tinker
  User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')]);
  ```
















