<header class="bg-white border-b border-gray-300 fixed top-0 left-0 right-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo and Brand Name -->
            <div class="flex items-center">
                <a href="<?php echo e(route('beneficiary.dashboard')); ?>" class="flex items-center space-x-2 space-x-reverse">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">GoodAid</span>
                </a>
            </div>
            
            <!-- Desktop User Info -->
            <div class="hidden md:flex items-center gap-6">
                <?php if($beneficiary): ?>
                    <span class="text-gray-600 text-sm font-medium"><?php echo e($beneficiary->name); ?></span>
                <?php endif; ?>
                <form action="<?php echo e(route('beneficiary.logout')); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="flex items-center text-red-600 hover:text-red-700 text-sm font-medium">
                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        تسجيل الخروج
                    </button>
                </form>
            </div>

            <!-- Mobile Menu Button - Only visible on mobile -->
            <button id="mobile-menu-button-beneficiary" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-teal-500 md:hidden">
                <svg id="menu-icon-beneficiary" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div id="mobile-menu-beneficiary" class="hidden md:hidden fixed right-0 top-0 bottom-0 w-1/2 bg-white z-50 transform transition-transform duration-300 ease-out overflow-y-auto shadow-2xl">
        <div class="flex flex-col min-h-full">
            <!-- Header with Logo and Name -->
            <div class="px-4 py-4 border-b border-gray-200 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">GoodAid</div>
                        <div class="text-xs text-gray-600"><?php if($beneficiary): ?><?php echo e($beneficiary->name); ?><?php endif; ?></div>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex flex-col p-4 space-y-2 flex-1">
            </nav>

            <!-- Logout Button at Bottom -->
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                <form action="<?php echo e(route('beneficiary.logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg text-xs font-medium transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button-beneficiary');
            const mobileMenu = document.getElementById('mobile-menu-beneficiary');

            function openMenu() {
                if (mobileMenu) {
                    mobileMenu.classList.remove('hidden');
                    mobileMenu.offsetHeight;
                    mobileMenu.style.transform = 'translateX(0)';
                }
            }

            function closeMenu() {
                if (mobileMenu) {
                    mobileMenu.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (mobileMenu) mobileMenu.classList.add('hidden');
                    }, 300);
                }
            }
            
            if (mobileMenu) {
                mobileMenu.style.transform = 'translateX(100%)';
            }

            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (mobileMenu && mobileMenu.classList.contains('hidden')) {
                        openMenu();
                    } else {
                        closeMenu();
                    }
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && mobileMenu && !mobileMenu.classList.contains('hidden')) {
                    closeMenu();
                }
            });

            document.addEventListener('click', function(e) {
                if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                    if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target)) {
                        closeMenu();
                    }
                }
            });

            if (mobileMenu) {
                mobileMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        })();
    </script>
    
    <style>
        @media (min-width: 768px) {
            #mobile-menu-button-beneficiary {
                display: none !important;
            }
        }
        
        @media (max-width: 767px) {
            #mobile-menu-button-beneficiary {
                display: block !important;
            }
        }
    </style>
</header>
<?php /**PATH C:\xampp\htdocs\goodaid\resources\views/components/beneficiary-header.blade.php ENDPATH**/ ?>