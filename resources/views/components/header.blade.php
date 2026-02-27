<header class="bg-white border-b border-gray-300 fixed top-0 left-0 right-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo and Brand Name -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 space-x-reverse">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">GoodAid</span>
                </a>
            </div>
            
            @auth
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-6">
                @hasPermission('dashboard.view')
                <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white' : 'text-gray-700 hover:text-emerald-600 hover:bg-emerald-50' }}" style="display: inline-block !important;">لوحة التحكم</a>
                @endhasPermission
                @hasPermission('users.view')
                <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white' : 'text-gray-700 hover:text-emerald-600 hover:bg-emerald-50' }}" style="display: inline-block !important;">المستخدمين</a>
                @endhasPermission
                @hasPermission('users.manage_permissions')
                <a href="{{ route('admin.user-permissions.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.user-permissions.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white' : 'text-gray-700 hover:text-emerald-600 hover:bg-emerald-50' }}" style="display: inline-block !important;">صلاحيات المستخدمين</a>
                @endhasPermission
                @hasPermission('beneficiaries.view')
                <a href="{{ route('beneficiaries.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('beneficiaries.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white' : 'text-gray-700 hover:text-emerald-600 hover:bg-emerald-50' }}" style="display: inline-block !important;">المستفيدون</a>
                @endhasPermission
                @hasPermission('batches.view')
                <a href="{{ route('batches.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('batches.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white' : 'text-gray-700 hover:text-emerald-600 hover:bg-emerald-50' }}" style="display: inline-block !important;">الطرود</a>
                @endhasPermission
                {{-- Distributions link removed from main menu as requested --}}
            </nav>
            
            <!-- Desktop User Info -->
            <div class="hidden md:flex items-center gap-6">
                <span class="text-gray-600 text-sm font-medium">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center text-red-600 hover:text-red-700 text-sm font-medium">
                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        تسجيل الخروج
                    </button>
                </form>
            </div>

            <!-- Mobile Menu Button - Only visible on mobile -->
            <button id="mobile-menu-button" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-teal-500">
                <svg id="menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            @endauth
            
            @guest
            <div class="flex items-center">
                <a href="{{ route('login') }}" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-md hover:from-emerald-600 hover:to-teal-700 text-sm font-medium">تسجيل الدخول</a>
            </div>
            @endguest
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    @auth
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden fixed right-0 top-0 bottom-0 w-1/2 bg-white z-50 transform transition-transform duration-300 ease-out overflow-y-auto shadow-2xl">
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
                        <div class="text-xs text-gray-600">{{ auth()->user()->name }}</div>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex flex-col p-4 space-y-2 flex-1">
                @hasPermission('dashboard.view')
                <a href="{{ route('dashboard') }}" class="px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md' : 'text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 active:bg-emerald-100' }}">لوحة التحكم</a>
                @endhasPermission
                @hasPermission('users.view')
                <a href="{{ route('users.index') }}" class="px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md' : 'text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 active:bg-emerald-100' }}">المستخدمين</a>
                @endhasPermission
                @hasPermission('users.manage_permissions')
                <a href="{{ route('admin.user-permissions.index') }}" class="px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.user-permissions.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md' : 'text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 active:bg-emerald-100' }}">صلاحيات المستخدمين</a>
                @endhasPermission
                @hasPermission('beneficiaries.view')
                <a href="{{ route('beneficiaries.index') }}" class="px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('beneficiaries.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md' : 'text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 active:bg-emerald-100' }}">المستفيدون</a>
                @endhasPermission
                @hasPermission('batches.view')
                <a href="{{ route('batches.index') }}" class="px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('batches.*') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md' : 'text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 active:bg-emerald-100' }}">الطرود</a>
                @endhasPermission
                {{-- Distributions link removed from mobile menu as requested --}}
            </nav>

            <!-- Logout Button at Bottom -->
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
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
    @endauth

    <script>
        (function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuClose = document.getElementById('mobile-menu-close');

            function openMenu() {
                if (mobileMenu) {
                    mobileMenu.classList.remove('hidden');
                    // Force reflow to ensure transition works
                    mobileMenu.offsetHeight;
                    mobileMenu.style.transform = 'translateX(0)';
                }
            }

            function closeMenu() {
                if (mobileMenu) {
                    mobileMenu.style.transform = 'translateX(100%)';
                    // Hide menu after animation
                    setTimeout(() => {
                        if (mobileMenu) mobileMenu.classList.add('hidden');
                    }, 300);
                }
            }
            
            // Initialize menu position
            if (mobileMenu) {
                mobileMenu.style.transform = 'translateX(100%)';
            }

            // Toggle menu button
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


            // Close on link click
            if (mobileMenu) {
                const mobileLinks = mobileMenu.querySelectorAll('nav a');
                mobileLinks.forEach(link => {
                    link.addEventListener('click', closeMenu);
                });
            }

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && mobileMenu && !mobileMenu.classList.contains('hidden')) {
                    closeMenu();
                }
            });

            // Close when clicking outside the menu
            document.addEventListener('click', function(e) {
                if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                    if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target)) {
                        closeMenu();
                    }
                }
            });

            // Prevent menu from closing when clicking inside it
            if (mobileMenu) {
                mobileMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        })();
    </script>
    
    <style>
        /* Hide mobile menu button on desktop */
        @media (min-width: 768px) {
            #mobile-menu-button {
                display: none !important;
            }
        }
        
        /* Show mobile menu button only on mobile */
        @media (max-width: 767px) {
            #mobile-menu-button {
                display: block !important;
            }
        }
    </style>
</header>

