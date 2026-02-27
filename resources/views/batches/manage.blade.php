@extends('layouts.app')

@section('title', 'إدارة الطرد: ' . $batch->name)

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
.beneficiary-item {
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
    transition: all 0.2s;
}
.beneficiary-item:hover {
    background-color: #f9fafb;
    border-color: #10b981;
}
.beneficiary-item.selected {
    background-color: #ecfdf5;
    border-color: #10b981;
}
.preview-section {
    margin-top: 1rem;
    padding: 1rem;
    border-radius: 0.5rem;
    background-color: #f9fafb;
}
</style>
@endpush

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">إدارة الطرد: {{ $batch->name }}</h1>
            <p class="text-gray-600 mt-2 text-lg">
                @if($batch->status === 'draft')
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">حالة: جديد</span>
                @else
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">حالة: معتمد</span>
                @endif
            </p>
        </div>
        <a href="{{ route('batches.index') }}" class="px-5 py-2.5 bg-red-500 text-white rounded-xl hover:bg-red-600 hover:shadow-lg transition-all duration-200 text-sm font-semibold flex items-center gap-2">
            العودة للقائمة
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
    </div>

    <!-- القسم A: معلومات الطرد -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">معلومات الطرد</h2>
        
        @if($batch->isDraft())
            <form action="{{ route('batches.update', $batch) }}" method="POST" class="space-y-4" id="batch-edit-form">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم الطرد *</label>
                        <input type="text" name="name" value="{{ old('name', $batch->name) }}" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع الطرد *</label>
                        <select name="type" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 @error('type') border-red-500 @enderror">
                            <option value="food" {{ old('type', $batch->type) === 'food' ? 'selected' : '' }}>غذائي</option>
                            <option value="health" {{ old('type', $batch->type) === 'health' ? 'selected' : '' }}>صحي</option>
                            <option value="clothes" {{ old('type', $batch->type) === 'clothes' ? 'selected' : '' }}>ملابس</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الكمية *</label>
                        <input type="number" name="quantity" value="{{ old('quantity', $batch->quantity ?? '') }}" required min="{{ $batch->beneficiaries->count() }}"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 @error('quantity') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">عدد الطرود المتاحة (الحد الأقصى لعدد المستفيدين)</p>
                        @if($batch->beneficiaries->count() > 0)
                            <p class="mt-1 text-xs text-amber-600">الحد الأدنى: {{ $batch->beneficiaries->count() }} (عدد المستفيدين الحاليين)</p>
                        @endif
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الطرد/الدفعة</label>
                        <input type="date" name="batch_date" value="{{ old('batch_date', $batch->batch_date ? $batch->batch_date->format('Y-m-d') : '') }}"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 @error('batch_date') border-red-500 @enderror">
                        @error('batch_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 @error('notes') border-red-500 @enderror">{{ old('notes', $batch->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all font-semibold text-sm shadow-lg hover:shadow-xl">
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">اسم الطرد</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $batch->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">نوع الطرد</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $batch->type_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">الكمية</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $batch->quantity ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ الطرد/الدفعة</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $batch->batch_date ? $batch->batch_date->format('Y-m-d') : '-' }}</p>
                </div>
                @if($batch->notes)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500 mb-1">ملاحظات</label>
                    <p class="text-gray-900">{{ $batch->notes }}</p>
                </div>
                @endif
            </div>
        @endif
    </div>

    <!-- القسم B: إضافة مستفيدين -->
    @if($batch->isDraft())
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">إضافة مستفيدين للطرد</h2>
            <div class="text-sm text-gray-600">
                <span class="font-semibold">العدد الحالي:</span> 
                <span id="current-beneficiaries-count">{{ $batch->beneficiaries->count() }}</span> 
                / 
                <span class="font-semibold">{{ $batch->quantity ?? 'غير محدد' }}</span>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-4">
            <!-- طريقة 1: اختيار من قائمة المستفيدين -->
            <a href="{{ route('beneficiaries.index', ['batch_id' => $batch->id]) }}" 
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-semibold inline-block">
                إضافة من المستفيدين
            </a>
            
            <!-- طريقة 2: استيراد Excel -->
            <button type="button" id="open-excel-import" 
                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-semibold shadow-md hover:shadow-lg"
                style="background-color: #16a34a !important; border: none;">
                استيراد Excel
            </button>
        </div>
    </div>
    @else
    <!-- ملاحظة: هذا القسم يظهر فقط للطرد في حالة مسودة -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-yellow-800">
        <p class="text-sm">ملاحظة: لا يمكن إضافة مستفيدين لطرد تم اعتماده. يجب أن يكون الطرد في حالة "جديد" لإضافة مستفيدين.</p>
    </div>
    @endif

    <!-- القسم C: المستفيدين من الطرد -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">المستفيدين من الطرد ({{ $batch->beneficiaries()->count() }})</h2>
            @if($batch->isDraft() && $batch->beneficiaries()->count() > 0)
                <form action="{{ route('batches.approve', $batch) }}" method="POST" onsubmit="event.preventDefault(); const form = this; Swal.fire({title: 'تأكيد الاعتماد', text: 'هل أنت متأكد من اعتماد هذا الطرد؟ بعد الاعتماد يمكنك تحديد من استلم ومن لم يستلم.', icon: 'question', showCancelButton: true, confirmButtonText: 'نعم، اعتمد', cancelButtonText: 'إلغاء', confirmButtonColor: '#10b981', cancelButtonColor: '#ef4444'}).then((result) => { if (result.isConfirmed) { form.submit(); } });">
                    @csrf
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all font-semibold text-sm">
                        اعتماد الطرد
                    </button>
                </form>
            @endif
        </div>
        
        <!-- Search Box -->
        <div class="mb-4">
            <input type="text" id="beneficiary-search-table" placeholder="ابحث بالاسم، رقم الهوية، أو رقم الهاتف..." 
                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="beneficiaries-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">#</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">الاسم</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">رقم الهوية</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">الهاتف</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">عدد الأفراد</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">الحالة</th>
                        @if($batch->isDraft())
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">الإجراءات</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="beneficiaries-table-body">
                    @forelse($batchBeneficiaries as $index => $beneficiary)
                        <tr class="beneficiary-row" 
                            data-name="{{ strtolower($beneficiary->name) }}"
                            data-national-id="{{ strtolower($beneficiary->national_id ?? '') }}"
                            data-phone="{{ strtolower($beneficiary->phone ?? '') }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-600">{{ $batchBeneficiaries->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium text-gray-900">{{ $beneficiary->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-600">{{ $beneficiary->national_id ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-600">{{ $beneficiary->phone ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-600">{{ $beneficiary->family_members_count + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($beneficiary->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">معتمد</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">معطل</span>
                                @endif
                            </td>
                            @if($batch->isDraft())
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <button type="button" onclick="removeBeneficiary({{ $beneficiary->id }})" 
                                    class="text-red-600 hover:text-red-900 transition-colors" title="إزالة من الطرد">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $batch->isDraft() ? '7' : '6' }}" class="px-6 py-12 text-center text-gray-500">
                                لا يوجد مستفيدين في هذا الطرد
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($batchBeneficiaries->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 mt-4">
                <div class="text-sm text-gray-600 text-center mb-2">
                    عرض <strong>{{ $batchBeneficiaries->firstItem() ?? 0 }}</strong> إلى <strong>{{ $batchBeneficiaries->lastItem() ?? 0 }}</strong> من <strong>{{ $batchBeneficiaries->total() }}</strong>
                </div>
                <div class="flex items-center justify-center">
                    {{ $batchBeneficiaries->onEachSide(1)->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Modal لاستيراد Excel -->
<div id="excel-import-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="text-2xl font-bold text-gray-900">استيراد من Excel</h2>
            <button type="button" id="close-excel-modal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">رفع ملف Excel</label>
                <input type="file" id="excel-file-input-modal" accept=".xls,.xlsx" 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300">
                <p class="mt-2 text-xs text-gray-600">يجب أن يحتوي الملف على عمود بأرقام الهوية الوطنية</p>
                <button type="button" id="upload-excel-preview-btn" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    عرض المعاينة
                </button>
            </div>
            
            <!-- Preview Results -->
            <div id="excel-preview-results" class="hidden">
                <div class="preview-section">
                    <div class="mb-4 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900">بيانات Excel (<span id="total-rows-count">0</span> صف)</h3>
                        <div class="flex gap-2">
                            <button type="button" id="select-all-rows" class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                تحديد الكل
                            </button>
                            <button type="button" id="deselect-all-rows" class="px-3 py-1 text-sm bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                إلغاء التحديد
                            </button>
                        </div>
                    </div>
                    
                    <!-- Summary Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="bg-green-50 p-3 rounded-lg">
                            <div class="text-sm text-gray-600">تم العثور عليه</div>
                            <div class="text-2xl font-bold text-green-700" id="matches-count">0</div>
                        </div>
                        <div class="bg-red-50 p-3 rounded-lg">
                            <div class="text-sm text-gray-600">غير موجود</div>
                            <div class="text-2xl font-bold text-red-700" id="not-found-count">0</div>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded-lg">
                            <div class="text-sm text-gray-600">مكرر</div>
                            <div class="text-2xl font-bold text-yellow-700" id="duplicates-count">0</div>
                        </div>
                    </div>
                    
                    <!-- Excel Data Table -->
                    <div class="overflow-x-auto max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">
                                        <input type="checkbox" id="select-all-checkbox" class="w-4 h-4 text-emerald-600 rounded">
                                    </th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">الحالة</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">رقم الهوية</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">الاسم (من Excel)</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">الاسم (من النظام)</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 uppercase">الهاتف</th>
                                </tr>
                            </thead>
                            <tbody id="excel-data-table-body" class="bg-white divide-y divide-gray-200">
                                <!-- سيتم ملؤها بواسطة JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 text-sm text-gray-600">
                        <span>المحدد: <span id="selected-rows-count">0</span> من <span id="total-rows-display">0</span></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="cancel-excel-modal" class="px-5 py-2.5 rounded-xl border-2 border-gray-300 text-gray-700 hover:bg-gray-50">
                إلغاء
            </button>
            <button type="button" id="confirm-excel-import" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hidden">
                تأكيد الإضافة
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// إزالة مستفيد
function removeBeneficiary(beneficiaryId) {
    Swal.fire({
        title: 'تأكيد الإزالة',
        text: 'هل أنت متأكد من إزالة هذا المستفيد من الطرد؟',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280'
    }).then((result) => {
        if (result.isConfirmed) {
    const url = `/batches/{{ $batch->id }}/beneficiaries/${beneficiaryId}`;
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
                    showFlashNotification(data.message || 'تم إزالة المستفيد بنجاح', 'success');
                    setTimeout(() => location.reload(), 1000);
        } else {
                    showFlashNotification(data.message || 'حدث خطأ', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
                showFlashNotification('حدث خطأ أثناء إزالة المستفيد', 'error');
            });
        }
    });
}

// Modal استيراد Excel
const excelModal = document.getElementById('excel-import-modal');
const openExcelBtn = document.getElementById('open-excel-import');
const closeExcelBtn = document.getElementById('close-excel-modal');
const cancelExcelBtn = document.getElementById('cancel-excel-modal');
const uploadExcelBtn = document.getElementById('upload-excel-preview-btn');
const confirmExcelBtn = document.getElementById('confirm-excel-import');
const excelFileInput = document.getElementById('excel-file-input-modal');

let excelPreviewData = null;

openExcelBtn?.addEventListener('click', () => {
    excelModal.classList.add('active');
});

[closeExcelBtn, cancelExcelBtn].forEach(btn => {
    btn?.addEventListener('click', () => {
        excelModal.classList.remove('active');
        document.getElementById('excel-preview-results').classList.add('hidden');
        confirmExcelBtn.classList.add('hidden');
        excelPreviewData = null;
    });
});

// رفع ومعاينة Excel
uploadExcelBtn?.addEventListener('click', function() {
    const file = excelFileInput.files[0];
    if (!file) {
        showFlashNotification('يرجى اختيار ملف Excel', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('excel_file', file);
    formData.append('_token', '{{ csrf_token() }}');
    
    this.disabled = true;
    this.textContent = 'جاري المعالجة...';
    
    fetch('{{ route("batches.import-excel-preview", $batch) }}', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'حدث خطأ في الخادم');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            excelPreviewData = data;
            displayExcelPreview(data);
            confirmExcelBtn.classList.remove('hidden');
            showFlashNotification('تم معالجة الملف بنجاح', 'success');
        } else {
            showFlashNotification('خطأ: ' + (data.message || 'حدث خطأ غير معروف'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showFlashNotification('حدث خطأ أثناء معالجة الملف. تأكد من:\n- أن الملف بصيغة .xls أو .xlsx\n- أن الملف يحتوي على عمود بأرقام الهوية الوطنية\n- أن الملف غير محمي بكلمة مرور', 'error');
    })
    .finally(() => {
        this.disabled = false;
        this.textContent = 'عرض المعاينة';
    });
});

// عرض معاينة Excel
let excelRowsData = [];
function displayExcelPreview(data) {
    document.getElementById('excel-preview-results').classList.remove('hidden');
    excelRowsData = data.rows || [];
    
    // تحديث الإحصائيات
    document.getElementById('total-rows-count').textContent = data.total_rows || 0;
    document.getElementById('total-rows-display').textContent = data.total_rows || 0;
    document.getElementById('matches-count').textContent = data.matches_count || 0;
    document.getElementById('not-found-count').textContent = data.not_found_count || 0;
    document.getElementById('duplicates-count').textContent = data.duplicates_count || 0;
    
    // ملء الجدول
    const tbody = document.getElementById('excel-data-table-body');
    tbody.innerHTML = '';
    
    excelRowsData.forEach((row, index) => {
        const tr = document.createElement('tr');
        tr.className = 'excel-row';
        tr.dataset.rowIndex = index;
        tr.dataset.beneficiaryId = row.beneficiary_id || '';
        tr.dataset.status = row.status;
        
        // تحديد لون الصف حسب الحالة
        if (row.is_duplicate) {
            tr.className += ' bg-yellow-50';
        } else if (row.status === 'found') {
            tr.className += row.exists_in_batch ? ' bg-gray-100' : ' bg-green-50';
        } else if (row.status === 'not_found') {
            tr.className += ' bg-red-50';
        }
        
        // Checkbox
        const checkboxTd = document.createElement('td');
        checkboxTd.className = 'px-3 py-2';
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.className = 'row-checkbox w-4 h-4 text-emerald-600 rounded';
        checkbox.dataset.rowIndex = index;
        checkbox.dataset.beneficiaryId = row.beneficiary_id || '';
        checkbox.disabled = !row.beneficiary_id || row.exists_in_batch || row.is_duplicate;
        if (!checkbox.disabled && row.status === 'found') {
            checkbox.checked = true;
        }
        checkbox.addEventListener('change', updateSelectedCount);
        checkboxTd.appendChild(checkbox);
        tr.appendChild(checkboxTd);
        
        // الحالة
        const statusTd = document.createElement('td');
        statusTd.className = 'px-3 py-2 text-sm';
        if (row.is_duplicate) {
            statusTd.innerHTML = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800">مكرر</span>';
        } else if (row.status === 'found') {
            if (row.exists_in_batch) {
                statusTd.innerHTML = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-800">موجود في الطرد</span>';
            } else {
                statusTd.innerHTML = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-200 text-green-800">موجود</span>';
            }
        } else {
            statusTd.innerHTML = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-200 text-red-800">غير موجود</span>';
        }
        tr.appendChild(statusTd);
        
        // رقم الهوية
        const nationalIdTd = document.createElement('td');
        nationalIdTd.className = 'px-3 py-2 text-sm font-medium text-gray-900';
        nationalIdTd.textContent = row.national_id || '-';
        tr.appendChild(nationalIdTd);
        
        // الاسم من Excel
        const nameFromExcelTd = document.createElement('td');
        nameFromExcelTd.className = 'px-3 py-2 text-sm text-gray-600';
        let nameFromExcel = '-';
        for (let col in row) {
            if (col.startsWith('header_') && (row[col].toLowerCase().includes('name') || row[col].toLowerCase().includes('اسم'))) {
                const colLetter = col.replace('header_', '');
                nameFromExcel = row[colLetter] || '-';
                break;
            }
        }
        nameFromExcelTd.textContent = nameFromExcel;
        tr.appendChild(nameFromExcelTd);
        
        // الاسم من النظام
        const nameFromSystemTd = document.createElement('td');
        nameFromSystemTd.className = 'px-3 py-2 text-sm font-medium text-gray-900';
        nameFromSystemTd.textContent = row.beneficiary_name || '-';
        tr.appendChild(nameFromSystemTd);
        
        // الهاتف
        const phoneTd = document.createElement('td');
        phoneTd.className = 'px-3 py-2 text-sm text-gray-600';
        phoneTd.textContent = row.beneficiary_phone || '-';
        tr.appendChild(phoneTd);
        
        tbody.appendChild(tr);
    });
    
    updateSelectedCount();
    }
    
// تحديث عدد المحدد
function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked:not(:disabled)');
    const count = checkboxes.length;
    document.getElementById('selected-rows-count').textContent = count;
}

// تحديد/إلغاء تحديد الكل
document.getElementById('select-all-checkbox')?.addEventListener('change', function() {
    document.querySelectorAll('.row-checkbox:not(:disabled)').forEach(cb => {
        cb.checked = this.checked;
    });
    updateSelectedCount();
});

document.getElementById('select-all-rows')?.addEventListener('click', function() {
    document.querySelectorAll('.row-checkbox:not(:disabled)').forEach(cb => {
        cb.checked = true;
    });
    document.getElementById('select-all-checkbox').checked = true;
    updateSelectedCount();
});

document.getElementById('deselect-all-rows')?.addEventListener('click', function() {
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.checked = false;
    });
    document.getElementById('select-all-checkbox').checked = false;
    updateSelectedCount();
        });

// تأكيد إضافة Excel
confirmExcelBtn?.addEventListener('click', function() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked:not(:disabled)');
    
    if (selectedCheckboxes.length === 0) {
        showFlashNotification('يرجى تحديد مستفيد واحد على الأقل لإضافته', 'error');
        return;
    }
    
    const beneficiaryIds = Array.from(selectedCheckboxes)
        .map(cb => cb.dataset.beneficiaryId)
        .filter(id => id && id !== '');
    
    if (beneficiaryIds.length === 0) {
        showFlashNotification('لا يوجد مستفيدين صالحين لإضافتهم', 'error');
        return;
    }
    
    Swal.fire({
        title: 'تأكيد الإضافة',
        text: `هل تريد إضافة ${beneficiaryIds.length} مستفيد للطرد؟`,
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
    
    fetch('{{ route("batches.confirm-excel-import", $batch) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ beneficiary_ids: beneficiaryIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
                    const currentCountEl = document.getElementById('current-beneficiaries-count');
                    if (currentCountEl) {
                        const currentCount = parseInt(currentCountEl.textContent) || 0;
                        currentCountEl.textContent = currentCount + data.added;
                    }
                    showFlashNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
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
                this.textContent = 'تأكيد الإضافة';
            });
        }
    });
});

// Search functionality for beneficiaries table
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('beneficiary-search-table');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.beneficiary-row');
            
            rows.forEach(row => {
                const name = row.dataset.name || '';
                const nationalId = row.dataset.nationalId || '';
                const phone = row.dataset.phone || '';
                
                if (searchTerm === '' || 
                    name.includes(searchTerm) || 
                    nationalId.includes(searchTerm) || 
                    phone.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endpush

@endsection
