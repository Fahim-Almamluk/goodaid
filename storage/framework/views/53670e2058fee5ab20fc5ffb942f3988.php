<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title><?php echo $__env->yieldContent('title', 'نظام إدارة المساعدات'); ?> - GoodAid</title>
    
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
    <?php echo $__env->make('components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
        <?php echo $__env->yieldContent('content'); ?>
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
</body>
</html>

<?php /**PATH C:\xampp\htdocs\goodaid\resources\views/layouts/app.blade.php ENDPATH**/ ?>