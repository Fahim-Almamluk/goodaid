<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title>لوحة التحكم - GoodAid</title>
    
    <!-- Google Fonts - Tajawal -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <!-- jQuery (مطلوب لـ Select2) -->
    <script src="<?php echo e(asset('js/jquery-3.7.1.min.js')); ?>"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-gradient-to-br from-emerald-50 to-teal-50 min-h-screen font-sans flex flex-col">
    
    <!-- Header -->
    <?php echo $__env->make('components.beneficiary-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Flash Messages -->
    <?php if(session('success')): ?>
        <div id="flash-success" class="fixed top-20 left-4 right-4 z-40 flex items-center justify-center">
            <div class="bg-green-500 text-white px-6 py-3 rounded-xl shadow-xl flex items-center space-x-3 space-x-reverse backdrop-blur-sm animate-slide-down">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span><?php echo e(session('success')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div id="flash-error" class="fixed top-20 left-4 right-4 z-40 flex items-center justify-center">
            <div class="bg-red-500 text-white px-6 py-3 rounded-xl shadow-xl flex items-center space-x-3 space-x-reverse backdrop-blur-sm animate-slide-down">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span><?php echo e(session('error')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1" style="margin-top: 64px;">
        <div class="space-y-8">
            <!-- Quick Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">الاسم</p>
                            <p class="text-xl font-bold text-gray-900"><?php echo e($beneficiary->name); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">رقم الهوية</p>
                            <p class="text-xl font-bold text-gray-900"><?php echo e($beneficiary->national_id ?? '-'); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">الطرود المستلمة</p>
                            <p class="text-xl font-bold text-gray-900"><?php echo e($beneficiary->batches ? $beneficiary->batches->count() : 0); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">الإجراءات السريعة</h3>
                <div class="flex flex-wrap gap-4">
                    <a href="<?php echo e(route('beneficiary.profile')); ?>" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:shadow-lg transition-all font-semibold">
                        عرض وتحديث الملف الشخصي
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        // Function to show custom flash notification
        function showFlashNotification(message, type = 'success') {
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const icon = type === 'success' 
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
            
            const notification = document.createElement('div');
            notification.className = `fixed top-20 left-4 right-4 z-40 flex items-center justify-center`;
            notification.innerHTML = `
                <div class="${bgColor} text-white px-6 py-3 rounded-xl shadow-xl flex items-center space-x-3 space-x-reverse backdrop-blur-sm animate-slide-down" style="transition: all 0.3s ease;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${icon}
                    </svg>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.querySelector('div').style.opacity = '0';
                notification.querySelector('div').style.transform = 'translateY(-20px)';
                setTimeout(() => notification.remove(), 300);
            }, type === 'success' ? 3000 : 4000);
        }

        // Auto hide flash messages
        document.addEventListener('DOMContentLoaded', function() {
            const successMsg = document.getElementById('flash-success');
            const errorMsg = document.getElementById('flash-error');
            
            if (successMsg) {
                setTimeout(() => {
                    successMsg.style.opacity = '0';
                    successMsg.style.transform = 'translateY(-20px)';
                    setTimeout(() => successMsg.remove(), 300);
                }, 3000);
            }
            
            if (errorMsg) {
                setTimeout(() => {
                    errorMsg.style.opacity = '0';
                    errorMsg.style.transform = 'translateY(-20px)';
                    setTimeout(() => errorMsg.remove(), 300);
                }, 4000);
            }
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
    
    <!-- jQuery must be loaded before Select2 -->
    <script>
        // Ensure jQuery is available
        if (typeof jQuery === 'undefined') {
            console.error('jQuery is not loaded!');
        }
    </script>
<?php /**PATH C:\xampp\htdocs\goodaid\resources\views/beneficiary/dashboard.blade.php ENDPATH**/ ?>