@extends('layouts.app')

@section('title', 'المستفيدين')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">المستفيدين</h1>
            <p class="text-gray-600 mt-2 text-lg">إدارة المستفيدين وأفراد أسرهم</p>
        </div>
        <div class="flex items-center gap-3">
            @hasPermission('beneficiaries.create')
            <button type="button" id="open-excel-import" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-semibold shadow-md hover:shadow-lg">
                استيراد Excel
            </button>
            <a href="{{ route('beneficiaries.create') }}" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all font-semibold">
                إضافة مستفيد جديد
            </a>
            @endhasPermission
        </div>
    </div>

    @if($batch)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-blue-800 font-semibold">تم اختيار طرد: {{ $batch->name }}</p>
                <p class="text-blue-600 text-sm mt-1">
                    العدد الحالي في الطرد: {{ $batch->beneficiaries->count() }} / {{ $batch->quantity ?? 'غير محدد' }}
                    <span id="selected-count-display" class="mr-3 text-emerald-600 font-semibold"></span>
                </p>
            </div>
            <button type="button" id="add-to-batch-btn" class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all font-semibold">
                إضافة المحدد للطرد
            </button>
        </div>
    </div>
    @endif

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
        <p class="text-green-800 font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-4 pt-6">
        <form method="POST" action="{{ route('beneficiaries.filter') }}" id="filter-form" class="space-y-3">
            @csrf
            @if($batch)
                <input type="hidden" name="batch_id" value="{{ $batch->id }}">
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المستفيد</label>
                    <select name="beneficiary_id" id="beneficiary-select" class="w-full px-3 py-1.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">الكل</option>
                        @php
                            $beneficiaryId = $filters['beneficiary_id'] ?? null;
                            $selectedBeneficiaryId = null;
                            if (!empty($beneficiaryId)) {
                                $selectedBeneficiaryId = $beneficiaryId;
                            }
                        @endphp
                        @foreach($allBeneficiaries ?? [] as $beneficiary)
                            <option value="{{ $beneficiary->id }}" {{ ($selectedBeneficiaryId && $beneficiary->id == $selectedBeneficiaryId) ? 'selected' : '' }}>
                                {{ $beneficiary->name }} 
                                @if($beneficiary->national_id) - {{ $beneficiary->national_id }} @endif
                                @if($beneficiary->phone) - {{ $beneficiary->phone }} @endif
                            </option>
                        @endforeach
                        @if($selectedBeneficiaryId)
                            @php
                                $selectedBeneficiary = \App\Models\Beneficiary::find($selectedBeneficiaryId);
                            @endphp
                            @if($selectedBeneficiary && !($allBeneficiaries ?? collect())->contains('id', $selectedBeneficiaryId))
                                <option value="{{ $selectedBeneficiary->id }}" selected>
                                    {{ $selectedBeneficiary->name }} 
                                    @if($selectedBeneficiary->national_id) - {{ $selectedBeneficiary->national_id }} @endif
                                    @if($selectedBeneficiary->phone) - {{ $selectedBeneficiary->phone }} @endif
                                </option>
                            @endif
                        @endif
                    </select>
                </div>

                <!-- Residence Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحالة السكنية</label>
                    <select name="residence_status" class="w-full px-3 py-1.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">الكل</option>
                        <option value="resident" {{ ($filters['residence_status'] ?? '') === 'resident' ? 'selected' : '' }}>مقيم</option>
                        <option value="displaced" {{ ($filters['residence_status'] ?? '') === 'displaced' ? 'selected' : '' }}>نازح</option>
                    </select>
                </div>

                <!-- Relationship -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">العلاقة</label>
                    <select name="relationship" class="w-full px-3 py-1.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">الكل</option>
                        <option value="زوج/ة" {{ ($filters['relationship'] ?? '') === 'زوج/ة' ? 'selected' : '' }}>زوج/ة</option>
                        <option value="أرمل/ة" {{ ($filters['relationship'] ?? '') === 'أرمل/ة' ? 'selected' : '' }}>أرمل/ة</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" class="w-full px-3 py-1.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">الكل</option>
                        <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>موجود</option>
                        <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>غير موجود</option>
                    </select>
                </div>

            </div>

            <!-- Special Filters Section -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">فلتر خاص</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <!-- Has Pregnant -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_pregnant" value="1" {{ ($filters['has_pregnant'] ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <span class="mr-2 text-xs font-medium text-gray-700">حوامل</span>
                        </label>
                    </div>

                    <!-- Has Nursing -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_nursing" value="1" {{ ($filters['has_nursing'] ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <span class="mr-2 text-xs font-medium text-gray-700">مرضعات</span>
                        </label>
                    </div>

                    <!-- Has Children -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_children" value="1" id="has_children_checkbox" {{ ($filters['has_children'] ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <span class="mr-2 text-xs font-medium text-gray-700">أطفال</span>
                        </label>
                    </div>

                    <!-- Number of Members -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="filter_members" value="1" id="filter_members_checkbox" {{ ($filters['filter_members'] ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <span class="mr-2 text-xs font-medium text-gray-700">عدد الأفراد</span>
                        </label>
                    </div>
                </div>

                <!-- Age Fields (Hidden by default, shown when children checkbox is checked) -->
                <div id="age-fields" class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3" style="display: {{ ($filters['has_children'] ?? false) ? 'grid' : 'none' }};">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">الحد الأدنى للعمر</label>
                        <input type="number" name="age_min" value="{{ $filters['age_min'] ?? '' }}" min="0" max="120"
                            class="w-full px-3 py-1.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">الحد الأقصى للعمر</label>
                        <input type="number" name="age_max" value="{{ $filters['age_max'] ?? '' }}" min="0" max="120"
                            class="w-full px-3 py-1.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <!-- Members Fields (Hidden by default, shown when members checkbox is checked) -->
                <div id="members-fields" class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3" style="display: {{ ($filters['filter_members'] ?? false) ? 'grid' : 'none' }};">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">الحد الأدنى لعدد الأفراد</label>
                        <input type="number" name="members_min" value="{{ $filters['members_min'] ?? '' }}" min="1"
                            class="w-full px-3 py-1.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">الحد الأقصى لعدد الأفراد</label>
                        <input type="number" name="members_max" value="{{ $filters['members_max'] ?? '' }}" min="1"
                            class="w-full px-3 py-1.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-200">
                <!-- per-page selector removed as requested -->
                <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all text-sm font-semibold flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span>بحث</span>
                </button>
                <button type="button" onclick="clearFilters()" class="px-5 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all text-sm font-semibold flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>تفريغ الحقول</span>
                </button>
                @hasPermission('beneficiaries.export_excel')
                <button type="button" onclick="exportExcel()" class="px-5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all text-sm font-semibold flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>تصدير Excel</span>
                </button>
                @endhasPermission
            </div>
        </form>
    </div>

    <!-- Beneficiaries Table -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">قائمة المستفيدين ({{ $beneficiaries->total() }})</h2>
            @if($batch)
            <div class="flex items-center space-x-3 space-x-reverse">
                <label class="flex items-center">
                    <input type="checkbox" id="select-all-beneficiaries" class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500">
                    <span class="mr-2 text-sm font-medium text-gray-700">تحديد الكل</span>
                </label>
            </div>
            @endif
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-emerald-500 to-teal-600">
                    <tr>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">#</th>
                        @if($batch)
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">
                            <input type="checkbox" id="select-all-checkbox" class="w-4 h-4 text-white rounded focus:ring-emerald-500">
                        </th>
                        @endif
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">الاسم</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">رقم الهوية</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">الهاتف</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">عدد الأفراد</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">العلاقة</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">الحالة السكنية</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">الحالة</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">المساعدات</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="beneficiaries-table-body">
                    @include('beneficiaries.partials.rows', ['beneficiaries' => $beneficiaries, 'batch' => $batch ?? null])
                </tbody>
            </table>
        </div>
        
        @if($beneficiaries->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-600 text-center mb-2">عرض <strong>{{ $beneficiaries->firstItem() ?? 0 }}</strong> إلى <strong>{{ $beneficiaries->lastItem() ?? 0 }}</strong> من <strong>{{ $beneficiaries->total() }}</strong></div>
                <div class="flex items-center justify-center">
                    {{ $beneficiaries->onEachSide(1)->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.375rem 0.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-right: 30px;
        padding-left: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 10px;
    }
    .select2-container--default .select2-selection--single .select2-selection__clear {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        font-weight: bold;
        font-size: 18px;
        line-height: 1;
        color: #6b7280;
        z-index: 10;
        width: 20px;
        text-align: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__clear:hover {
        color: #ef4444;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #10b981;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// Initialize Select2 for beneficiary search
$(document).ready(function() {
    var selectElement = $('#beneficiary-select');
    
    // Initialize Select2 - it will read the selected value from the HTML
    selectElement.select2({
        allowClear: false,
        dir: 'rtl',
        language: {
            noResults: function() {
                return "لا توجد نتائج";
            },
            searching: function() {
                return "جاري البحث...";
            }
        }
    });
    
    // Don't override the selection - let the user freely change it
    
});

// Handle form submit to remove empty beneficiary_id from URL
document.getElementById('filter-form').addEventListener('submit', function(event) {
    var beneficiarySelect = $('#beneficiary-select');
    var beneficiarySelectElement = document.getElementById('beneficiary-select');
    
    // If beneficiary_id is empty or null, remove it from form submission
    if (beneficiarySelectElement) {
        var selectedValue = beneficiarySelect.val();
        if (!selectedValue || selectedValue === '' || selectedValue === null) {
            // Remove the name attribute so it won't be submitted
            beneficiarySelectElement.removeAttribute('name');
        }
    }
    
    // Form will submit normally
});

// Export Excel function - uses session filters, just navigate to export URL
function exportExcel() {
    window.location.href = '{{ route('beneficiaries.export-excel') }}';
}

// Clear all filters function - POST to reset endpoint
function clearFilters() {
    // Create a form and submit via POST
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('beneficiaries.filter.reset') }}';
    
    // Add CSRF token
    var csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    document.body.appendChild(form);
    form.submit();
}

// Toggle Age Fields when Children checkbox is clicked
document.getElementById('has_children_checkbox')?.addEventListener('change', function() {
    const ageFields = document.getElementById('age-fields');
    if (ageFields) {
        ageFields.style.display = this.checked ? 'grid' : 'none';
        
        // Clear fields when unchecked
        if (!this.checked) {
            const minAgeInput = document.querySelector('input[name="age_min"]');
            const maxAgeInput = document.querySelector('input[name="age_max"]');
            if (minAgeInput) minAgeInput.value = '';
            if (maxAgeInput) maxAgeInput.value = '';
        }
    }
});

// Toggle Members Fields when Members checkbox is clicked
document.getElementById('filter_members_checkbox')?.addEventListener('change', function() {
    const membersFields = document.getElementById('members-fields');
    if (membersFields) {
        membersFields.style.display = this.checked ? 'grid' : 'none';
        
        // Clear fields when unchecked
        if (!this.checked) {
            const minMembersInput = document.querySelector('input[name="members_min"]');
            const maxMembersInput = document.querySelector('input[name="members_max"]');
            if (minMembersInput) minMembersInput.value = '';
            if (maxMembersInput) maxMembersInput.value = '';
        }
    }
});

// per-page selector removed — no auto-submit listener needed

@if($batch)
// متغير لتخزين جميع IDs المفلترة
let allFilteredIds = [];
let isSelectingAll = false;

// دالة لتحديث عداد المختارين
function updateSelectedCount() {
    const countDisplay = document.getElementById('selected-count-display');
    if (!countDisplay) return;
    
    let count = 0;
    if (isSelectingAll && allFilteredIds.length > 0) {
        count = allFilteredIds.length;
    } else {
        count = document.querySelectorAll('.beneficiary-checkbox:checked:not(:disabled)').length;
    }
    
    if (count > 0) {
        countDisplay.textContent = `| تم اختيار ${count} مستفيد`;
    } else {
        countDisplay.textContent = '';
    }
}

// دالة لجلب جميع IDs حسب الفلترات
async function fetchAllFilteredIds() {
    try {
        const formData = new FormData(document.getElementById('filter-form'));
        const params = new URLSearchParams(formData);
        
        const response = await fetch('{{ route("beneficiaries.get-filtered-ids") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify(Object.fromEntries(params))
        });
        
        const data = await response.json();
        if (data.success) {
            return data.ids;
        }
        return [];
    } catch (error) {
        console.error('Error fetching filtered IDs:', error);
        return [];
    }
}

// Select All - جلب جميع النتائج حسب الفلترات
document.getElementById('select-all-beneficiaries')?.addEventListener('change', async function() {
    const isChecked = this.checked;
    
    if (isChecked) {
        // جلب جميع IDs المفلترة
        const selectAllBtn = this;
        const originalLabel = selectAllBtn.nextElementSibling.textContent;
        selectAllBtn.disabled = true;
        selectAllBtn.nextElementSibling.textContent = 'جاري التحميل...';
        
        allFilteredIds = await fetchAllFilteredIds();
        isSelectingAll = true;
        
        selectAllBtn.disabled = false;
        selectAllBtn.nextElementSibling.textContent = originalLabel;
        
        // تحديد الـ checkboxes الظاهرة في الصفحة الحالية
        const checkboxes = document.querySelectorAll('.beneficiary-checkbox:not(:disabled)');
        checkboxes.forEach(cb => {
            cb.checked = true;
        });
        document.getElementById('select-all-checkbox').checked = true;
        updateSelectedCount();
    } else {
        // إلغاء تحديد الكل
        isSelectingAll = false;
        allFilteredIds = [];
        const checkboxes = document.querySelectorAll('.beneficiary-checkbox:not(:disabled)');
        checkboxes.forEach(cb => {
            cb.checked = false;
        });
        document.getElementById('select-all-checkbox').checked = false;
        updateSelectedCount();
    }
});

document.getElementById('select-all-checkbox')?.addEventListener('change', async function() {
    const isChecked = this.checked;
    
    if (isChecked) {
        // جلب جميع IDs المفلترة
        allFilteredIds = await fetchAllFilteredIds();
        isSelectingAll = true;
        
        // تحديد الـ checkboxes الظاهرة في الصفحة الحالية
        const checkboxes = document.querySelectorAll('.beneficiary-checkbox:not(:disabled)');
        checkboxes.forEach(cb => {
            cb.checked = true;
        });
        document.getElementById('select-all-beneficiaries').checked = true;
        updateSelectedCount();
    } else {
        // إلغاء تحديد الكل
        isSelectingAll = false;
        allFilteredIds = [];
        const checkboxes = document.querySelectorAll('.beneficiary-checkbox:not(:disabled)');
        checkboxes.forEach(cb => {
            cb.checked = false;
        });
        document.getElementById('select-all-beneficiaries').checked = false;
        updateSelectedCount();
    }
});

// تحديث العداد عند تغيير أي checkbox فردي
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('beneficiary-checkbox')) {
        // إذا ألغى المستخدم تحديد أي checkbox يدوياً، إلغاء وضع "تحديد الكل"
        if (!e.target.checked) {
            isSelectingAll = false;
            allFilteredIds = [];
            document.getElementById('select-all-beneficiaries').checked = false;
            document.getElementById('select-all-checkbox').checked = false;
        }
        updateSelectedCount();
    }
});

// Add to Batch
document.getElementById('add-to-batch-btn')?.addEventListener('click', function() {
    let selectedIds;
    
    // إذا كان المستخدم قد اختار "تحديد الكل"، استخدم جميع IDs المفلترة
    if (isSelectingAll && allFilteredIds.length > 0) {
        selectedIds = allFilteredIds;
    } else {
        // وإلا، استخدم الـ checkboxes المحددة فقط
        selectedIds = Array.from(document.querySelectorAll('.beneficiary-checkbox:checked:not(:disabled)')).map(cb => cb.value);
    }
    
    if (selectedIds.length === 0) {
        showFlashNotification('يرجى اختيار مستفيد واحد على الأقل', 'error');
        return;
    }
    
    Swal.fire({
        title: 'تأكيد الإضافة',
        text: `هل تريد إضافة ${selectedIds.length} مستفيد للطرد؟`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، أضف',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280'
    }).then((result) => {
        if (result.isConfirmed) {
            this.disabled = true;
            this.textContent = 'جاري الإضافة...';
    
            fetch('{{ route("batches.add-beneficiaries", $batch) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ beneficiary_ids: selectedIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showFlashNotification(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route("batches.manage", $batch) }}';
                    }, 1000);
                } else {
                    showFlashNotification(data.message || 'حدث خطأ', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFlashNotification('حدث خطأ أثناء إضافة المستفيدين', 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.textContent = 'إضافة المحدد للطرد';
            });
        }
    });
});
@endif
</script>
@endpush

<!-- Modal لاستيراد Excel -->
<div id="excel-import-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="text-2xl font-bold text-gray-900">استيراد مستفيدين من Excel</h2>
            <button type="button" id="close-excel-modal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <!-- File Input -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">رفع ملف Excel</label>
                <input type="file" id="excel-file-input-modal" accept=".xlsx,.xls,.csv" 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300">
                <p class="mt-2 text-xs text-gray-600">✓ الأعمدة المطلوبة: الاسم، رقم الهوية، رقم الجوال</p>
            </div>

            <button type="button" id="upload-excel-preview-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                عرض المعاينة
            </button>

            <!-- Preview Section -->
            <div id="previewSection" class="hidden mt-4">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-gray-900">معاينة البيانات:</h3>
                    <p id="previewCount" class="text-sm text-gray-600"></p>
                </div>
                <div class="bg-gray-50 rounded-lg overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-200 sticky top-0">
                            <tr>
                                <th class="px-3 py-2 text-right font-semibold">#</th>
                                <th class="px-3 py-2 text-right font-semibold">الاسم</th>
                                <th class="px-3 py-2 text-right font-semibold">رقم الهوية</th>
                                <th class="px-3 py-2 text-right font-semibold">رقم الجوال</th>
                            </tr>
                        </thead>
                        <tbody id="previewBody">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Error Messages -->
            <div id="errorContainer" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mt-4">
                <p class="text-sm font-semibold text-red-800 mb-2">أخطاء:</p>
                <ul id="errorList" class="text-xs text-red-700 space-y-1 max-h-32 overflow-y-auto"></ul>
            </div>

            <!-- Success Message -->
            <div id="successContainer" class="hidden bg-green-50 border border-green-200 rounded-lg p-4 mt-4">
                <p id="successMessage" class="text-sm font-semibold text-green-800"></p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="cancel-excel-modal" class="px-5 py-2.5 rounded-xl border-2 border-gray-300 text-gray-700 hover:bg-gray-50">
                إلغاء
            </button>
            <button type="button" id="confirm-excel-import" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hidden">
                تأكيد الاستيراد
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
}
.modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-content {
    background-color: #fff;
    margin: auto;
    border-radius: 1rem;
    width: 90%;
    max-width: 900px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}
.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex: 1;
}
.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>
@endpush

@push('scripts')
<script>
// Modal استيراد Excel
const excelModal = document.getElementById('excel-import-modal');
const openExcelBtn = document.getElementById('open-excel-import');
const closeExcelBtn = document.getElementById('close-excel-modal');
const cancelExcelBtn = document.getElementById('cancel-excel-modal');
const uploadExcelBtn = document.getElementById('upload-excel-preview-btn');
const confirmExcelBtn = document.getElementById('confirm-excel-import');
const excelFileInput = document.getElementById('excel-file-input-modal');

// فتح وإغلاق Modal
openExcelBtn?.addEventListener('click', () => {
    excelModal.classList.add('active');
    resetImportForm();
});

[closeExcelBtn, cancelExcelBtn].forEach(btn => {
    btn?.addEventListener('click', () => {
        excelModal.classList.remove('active');
        resetImportForm();
    });
});

// عرض معاينة البيانات
uploadExcelBtn?.addEventListener('click', function() {
    const file = excelFileInput?.files[0];
    if (!file) {
        showFlashNotification('اختر ملف Excel', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('excel_file', file);
    formData.append('_token', '{{ csrf_token() }}');

    this.disabled = true;
    this.textContent = 'جاري...';

    fetch('{{ route("beneficiaries.import-preview") }}', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const previewBody = document.getElementById('previewBody');
            previewBody.innerHTML = '';

            data.preview.forEach((row, i) => {
                if (row[0] || row[1] || row[2]) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td class="px-3 py-2">${i+1}</td><td class="px-3 py-2">${row[0]||'-'}</td><td class="px-3 py-2">${row[1]||'-'}</td><td class="px-3 py-2">${row[2]||'-'}</td>`;
                    previewBody.appendChild(tr);
                }
            });

            document.getElementById('previewSection').classList.remove('hidden');
            document.getElementById('previewCount').textContent = `(${data.preview.length})`;
            confirmExcelBtn.classList.remove('hidden');
            showFlashNotification('تم معالجة الملف', 'success');
        }
    }).finally(() => {
        this.disabled = false;
        this.textContent = 'عرض المعاينة';
    });
});

// تأكيد الاستيراد
confirmExcelBtn?.addEventListener('click', function() {
    const file = excelFileInput?.files[0];
    if (!file) {
        showFlashNotification('اختر ملف Excel', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('excel_file', file);
    formData.append('_token', '{{ csrf_token() }}');

    this.disabled = true;
    this.textContent = 'جاري...';

    fetch('{{ route("beneficiaries.import") }}', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showFlashNotification(data.message || 'تم الاستيراد بنجاح', 'success');
            setTimeout(() => {
                excelModal.classList.remove('active');
                location.reload();
            }, 1500);
        } else {
            showFlashNotification(data.message || 'خطأ في الاستيراد', 'error');
        }
    }).finally(() => {
        this.disabled = false;
        this.textContent = 'تأكيد الاستيراد';
    });
});

function resetImportForm() {
    excelFileInput.value = '';
    document.getElementById('previewSection').classList.add('hidden');
    document.getElementById('previewBody').innerHTML = '';
    document.getElementById('errorContainer').classList.add('hidden');
    document.getElementById('successContainer').classList.add('hidden');
    confirmExcelBtn.classList.add('hidden');
}
</script>
@endpush

@endsection

