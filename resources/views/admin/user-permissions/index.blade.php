@extends('layouts.app')

@section('title', 'إدارة صلاحيات المستخدم')

@push('styles')
<style>
    /* Switch Styles */
    .switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: 0.3s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #10b981;
    }

    input:checked + .slider:before {
        transform: translateX(24px);
    }

    input:disabled + .slider {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Tri-state checkbox */
    .checkbox-tri-state {
        position: relative;
    }

    .checkbox-tri-state input[type="checkbox"] {
        appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #cbd5e1;
        border-radius: 4px;
        cursor: pointer;
        position: relative;
    }

    .checkbox-tri-state input[type="checkbox"]:checked {
        background-color: #10b981;
        border-color: #10b981;
    }

    .checkbox-tri-state input[type="checkbox"]:checked::after {
        content: "✓";
        position: absolute;
        color: white;
        font-size: 14px;
        font-weight: bold;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .checkbox-tri-state input[type="checkbox"][data-indeterminate="true"] {
        background-color: #f59e0b;
        border-color: #f59e0b;
    }

    .checkbox-tri-state input[type="checkbox"][data-indeterminate="true"]::after {
        content: "−";
        position: absolute;
        color: white;
        font-size: 18px;
        font-weight: bold;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* Select2 Styles - Matching site design */
    .select2-container {
        width: 100% !important;
    }
    
    .select2-container--default .select2-selection--single {
        height: 48px;
        border: 2px solid #d1d5db;
        border-radius: 0.75rem;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }
    
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 44px;
        padding-right: 50px;
        padding-left: 50px;
        color: #111827;
        font-size: 16px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #9ca3af;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
        right: 16px;
        width: 20px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #6b7280 transparent transparent transparent;
        border-width: 6px 6px 0 6px;
        margin-top: -3px;
        margin-right: -10px;
    }
    
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #6b7280 transparent;
        border-width: 0 6px 6px 6px;
        margin-top: -3px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__clear {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        font-weight: bold;
        font-size: 18px;
        line-height: 1;
        color: #9ca3af;
        z-index: 10;
        width: 20px;
        text-align: center;
        transition: color 0.2s ease;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__clear:hover {
        color: #ef4444;
    }
    
    .select2-dropdown {
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        margin-top: 4px;
    }
    
    .select2-container--default .select2-results > .select2-results__options {
        max-height: 300px;
        padding: 8px;
    }
    
    .select2-container--default .select2-results__option {
        padding: 12px 16px;
        border-radius: 0.5rem;
        margin: 2px 0;
        transition: all 0.15s ease;
        font-size: 16px;
    }
    
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #f3f4f6;
        color: #111827;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #10b981;
        color: #ffffff;
    }
    
    .select2-container--default .select2-search--dropdown {
        padding: 6px;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 6px 12px;
        margin: 4px;
        font-size: 14px;
        height: 36px;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #10b981;
        outline: none;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
    }
    
    /* Ensure user select and search input have same height */
    #user-select,
    #permission-search-input {
        height: 48px !important;
        min-height: 48px !important;
    }
    
    /* Hide the original select element when Select2 is initialized */
    select.select2-hidden-accessible {
        display: none !important;
    }
    
    /* Ensure Select2 container doesn't duplicate */
    .select2-container {
        width: 100% !important;
        display: block !important;
    }
    
    /* Prevent double display */
    .select2-container--default {
        position: relative;
    }
    
    /* Ensure only one Select2 instance is visible */
    .select2-container.select2-container--default {
        display: inline-block !important;
    }
    
    .select2-container--default .select2-selection--single {
        height: 48px !important;
        min-height: 48px !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 48px !important;
    }
    
    /* Ensure dropdown search field is properly styled */
    .select2-container--default .select2-search--dropdown {
        padding: 8px;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field {
        width: 100% !important;
        box-sizing: border-box !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">إدارة صلاحيات المستخدم</h1>
            <p class="text-gray-600 mt-2 text-lg">اختر مستخدم وقم بإدارة صلاحياته</p>
        </div>
    </div>


    <!-- Toolbar -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="space-y-6">
            <!-- User Selection and Permission Search - Same Row on Desktop -->
            <div class="flex flex-col md:flex-row gap-6">
                <!-- User Selection -->
                <div class="flex-1 w-full md:w-auto">
                    <label class="block text-base font-semibold text-gray-700 mb-2">اختر المستخدم</label>
                    <select name="user_id" id="user-select" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base h-[48px]">
                        <option value="">اختر مستخدم...</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}" data-username="{{ $user->username }}" data-name="{{ $user->name }}">
                                {{ $user->username }} - {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Permission Search -->
                <div class="flex-1 w-full md:w-auto">
                    <label class="block text-base font-semibold text-gray-700 mb-2">بحث في الصلاحيات</label>
                    <input 
                        type="text" 
                        id="permission-search-input"
                        placeholder="ابحث في الصلاحيات..."
                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base h-[48px]"
                    />
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-base font-semibold text-gray-700 mb-2">فلترة الحالة</label>
                <div class="flex gap-2 flex-wrap">
                    <button 
                        data-status="all"
                        class="status-filter-btn px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg hover:shadow-xl"
                    >
                        الكل
                    </button>
                    <button 
                        data-status="assigned"
                        class="status-filter-btn px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200"
                    >
                        مفعلة
                    </button>
                    <button 
                        data-status="unassigned"
                        class="status-filter-btn px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200"
                    >
                        غير مفعلة
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row gap-3 items-center justify-between">
            <!-- Left Side: Other Buttons -->
            <div class="grid grid-cols-2 md:flex md:flex-wrap gap-3 items-center w-full md:w-auto">
                <!-- Row 1: تفعيل الكل و تعطيل الكل -->
                <button 
                    id="assign-visible-btn"
                    class="flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-lg text-sm font-semibold"
                    disabled
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    تفعيل الكل
                </button>
                <button 
                    id="revoke-visible-btn"
                    class="flex items-center justify-center gap-2 px-5 py-2.5 bg-red-500 text-white rounded-xl hover:bg-red-600 hover:shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-semibold"
                    disabled
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    تعطيل الكل
                </button>
                <!-- Row 2: توسيع الكل و طي الكل -->
                <button 
                    id="expand-all-btn"
                    class="flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-semibold"
                    disabled
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    توسيع الكل
                </button>
                <button 
                    id="collapse-all-btn"
                    class="flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-semibold"
                    disabled
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                    طي الكل
                </button>
            </div>
            <!-- Right Side: Save Button -->
            <div class="w-full md:w-auto flex justify-end md:ml-auto">
                <button 
                    id="save-btn"
                    class="flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-lg font-semibold whitespace-nowrap"
                    disabled
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    حفظ التغييرات
                </button>
            </div>
        </div>
    </div>

    <!-- Permissions Tree -->
    <div id="permissions-container" class="space-y-4">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 text-center text-gray-500">
            <p>يرجى اختيار مستخدم لعرض الصلاحيات</p>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" style="position: fixed !important; bottom: 1rem !important; left: 1rem !important; right: 1rem !important; z-index: 99999 !important; display: flex !important; flex-direction: column !important; gap: 0.5rem !important; align-items: flex-end !important; pointer-events: none !important;"></div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/user-permissions.js') }}"></script>
<script>
// Initialize Select2 for user search
$(document).ready(function() {
    var selectElement = $('#user-select');
    
    // Initialize Select2 with AJAX
    selectElement.select2({
        allowClear: true,
        dir: 'rtl',
        placeholder: 'ابحث واختر مستخدم...',
        language: {
            noResults: function() {
                return "لا توجد نتائج";
            },
            searching: function() {
                return "جاري البحث...";
            }
        },
        ajax: {
            url: '{{ route("admin.api.users") }}',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.data.map(function(user) {
                        return {
                            id: user.id,
                            text: user.username + ' - ' + user.name,
                            username: user.username,
                            name: user.name
                        };
                    }),
                    pagination: {
                        more: false
                    }
                };
            },
            cache: true
        },
        templateResult: function(user) {
            if (user.loading) {
                return 'جاري البحث...';
            }
            return $('<div>').text(user.username + ' - ' + user.name);
        },
        templateSelection: function(user) {
            if (user.username) {
                return user.username + ' - ' + user.name;
            }
            return user.text;
        }
    });
    
    // Handle user selection change
    selectElement.on('select2:select', function (e) {
        const userId = e.params.data.id;
        const userName = e.params.data.name;
        const username = e.params.data.username;
        
        // Update JavaScript state
        if (typeof window.selectUser === 'function') {
            window.selectUser(userId, userName, username);
        } else {
            // Fallback: call the function from user-permissions.js
            setTimeout(function() {
                if (window.selectUser) {
                    window.selectUser(userId, userName, username);
                }
            }, 100);
        }
    });
    
    // Handle clear
    selectElement.on('select2:clear', function (e) {
        if (typeof window.handleChangeUser === 'function') {
            window.handleChangeUser();
        }
    });
});
</script>
@endpush

