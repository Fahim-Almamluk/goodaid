@extends('layouts.app')

@section('title', 'تعديل مستفيد')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-10">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900">تعديل مستفيد</h1>
            <p class="text-gray-600 mt-2 text-lg">تحديث بيانات المستفيد: {{ $beneficiary->name }}</p>
        </div>

        <form action="{{ route('beneficiaries.update', $beneficiary) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Number of Members -->
            <div class="md:col-span-2">
                <label for="number_of_members" class="block text-base font-semibold text-gray-700 mb-2">عدد الأفراد *</label>
                <input type="number" name="number_of_members" id="number_of_members" value="{{ old('number_of_members', ($beneficiary->familyMembers->count() + 1) ?: $beneficiary->number_of_members) }}" min="1" required
                    class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                @error('number_of_members')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">عدد الأفراد الإجمالي (بما في ذلك رب الأسرة)</p>
            </div>

            <!-- Name -->
            <div>
                <label for="name" class="block text-base font-semibold text-gray-700 mb-2">الاسم الكامل *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $beneficiary->name) }}" required
                    class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- National ID -->
            <div>
                <label for="national_id" class="block text-base font-semibold text-gray-700 mb-2">رقم الهوية</label>
                <input type="text" name="national_id" id="national_id" value="{{ old('national_id', $beneficiary->national_id) }}"
                    class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                @error('national_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-base font-semibold text-gray-700 mb-2">رقم الهاتف *</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $beneficiary->phone) }}" required
                    class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Relationship -->
            <div>
                <label for="relationship" class="block text-base font-semibold text-gray-700 mb-2">العلاقة *</label>
                <select name="relationship" id="relationship" required
                    class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                    <option value="">اختر العلاقة</option>
                    <option value="زوج/ة" {{ old('relationship', $beneficiary->formatted_relationship) === 'زوج/ة' ? 'selected' : '' }}>زوج/ة</option>
                    <option value="أرمل/ة" {{ old('relationship', $beneficiary->formatted_relationship) === 'أرمل/ة' ? 'selected' : '' }}>أرمل/ة</option>
                </select>
                @error('relationship')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Residence Status -->
            <div>
                <label for="residence_status" class="block text-base font-semibold text-gray-700 mb-2">الحالة السكنية *</label>
                <select name="residence_status" id="residence_status" required
                    class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                    <option value="resident" {{ old('residence_status', $beneficiary->residence_status ?? 'resident') === 'resident' ? 'selected' : '' }}>مقيم</option>
                    <option value="displaced" {{ old('residence_status', $beneficiary->residence_status ?? 'resident') === 'displaced' ? 'selected' : '' }}>نازح</option>
                </select>
                @error('residence_status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            </div>

            <!-- Family Members Forms -->
            <div id="family-members-container" class="space-y-6"></div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-base font-semibold text-gray-700 mb-2">العنوان</label>
                <input type="text" name="address" id="address" value="{{ old('address', $beneficiary->address) }}"
                    class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-base font-semibold text-gray-700 mb-2">ملاحظات</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">{{ old('notes', $beneficiary->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Active -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $beneficiary->is_active) ? 'checked' : '' }}
                    class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                <label for="is_active" class="mr-2 text-base font-medium text-gray-700">موجود</label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200">
                <a href="{{ route('beneficiaries.index') }}" class="px-5 py-2.5 bg-red-500 text-white rounded-xl hover:bg-red-600 hover:shadow-lg transition-all duration-200 text-sm font-semibold whitespace-nowrap">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 text-sm font-semibold whitespace-nowrap">
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const numberInput = document.getElementById('number_of_members');
    const container = document.getElementById('family-members-container');
    
    // قائمة علاقات القرابة
    const relationships = [
        { value: 'زوج/ة', label: 'زوج/ة' },
        { value: 'ابن/ة', label: 'ابن/ة' },
    ];

    @php
        $existingMembersArray = $beneficiary->familyMembers->map(function($member) {
            return [
                'name' => old("family_members.{$member->order}.name", $member->name),
                'national_id' => old("family_members.{$member->order}.national_id", $member->national_id),
                'birth_date' => old("family_members.{$member->order}.birth_date", $member->birth_date ? $member->birth_date->format('Y-m-d') : ''),
                'relationship' => old("family_members.{$member->order}.relationship", $member->relationship),
                'is_pregnant' => old("family_members.{$member->order}.is_pregnant", $member->is_pregnant),
                'is_nursing' => old("family_members.{$member->order}.is_nursing", $member->is_nursing),
            ];
        })->values()->all();
    @endphp

    const existingMembers = @json($existingMembersArray);

    function renderFamilyMembersForms(count) {
        container.innerHTML = '';
        
        // عدد الأفراد الإجمالي يشمل رب الأسرة، لذلك نطرح 1 للحصول على عدد أفراد العائلة
        const familyMembersCount = Math.max(0, count - 1);
        
        if (familyMembersCount <= 0) {
            return;
        }

        const title = document.createElement('div');
        title.className = 'mb-4';
        title.innerHTML = '<h3 class="text-xl font-bold text-gray-900">بيانات أفراد الأسرة</h3>';
        container.appendChild(title);

        for (let i = 0; i < familyMembersCount; i++) {
            const memberData = existingMembers[i] || {};
            const memberCard = document.createElement('div');
            memberCard.className = 'bg-gray-50 rounded-xl border-2 border-gray-200 p-6 relative';
            memberCard.setAttribute('data-member-index', i);
            memberCard.innerHTML = `
                <div class="mb-4 pb-4 border-b border-gray-300 flex justify-between items-center">
                    <h4 class="text-lg font-semibold text-gray-900">فرد ${i + 1}</h4>
                    <button type="button" class="delete-member-btn px-3 py-1.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-semibold flex items-center">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        حذف
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-base font-semibold text-gray-700 mb-2">الاسم الكامل *</label>
                        <input type="text" name="family_members[${i}][name]" value="${memberData.name || ''}" required
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                    </div>
                    <div>
                        <label class="block text-base font-semibold text-gray-700 mb-2">رقم الهوية</label>
                        <input type="text" name="family_members[${i}][national_id]" value="${memberData.national_id || ''}"
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                    </div>
                    <div>
                        <label class="block text-base font-semibold text-gray-700 mb-2">تاريخ الميلاد</label>
                        <input type="date" name="family_members[${i}][birth_date]" value="${memberData.birth_date || ''}"
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                    </div>
                    <div>
                        <label class="block text-base font-semibold text-gray-700 mb-2">العلاقة برب الأسرة</label>
                        <select name="family_members[${i}][relationship]" id="relationship_${i}"
                            class="relationship-select w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                            <option value="">اختر العلاقة</option>
                            ${relationships.map(rel => `<option value="${rel.value}" ${memberData.relationship === rel.value ? 'selected' : ''}>${rel.label}</option>`).join('')}
                        </select>
                    </div>
                    <div class="md:col-span-2 pregnancy-nursing-container" id="pregnancy_nursing_${i}" style="display: ${memberData.relationship === 'زوج/ة' ? 'block' : 'none'};">
                        <div class="flex items-center space-x-6 space-x-reverse pt-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="family_members[${i}][is_pregnant]" id="pregnant_${i}" value="1" ${memberData.is_pregnant ? 'checked' : ''}
                                    class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                <label for="pregnant_${i}" class="mr-2 text-base font-medium text-gray-700">حامل</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="family_members[${i}][is_nursing]" id="nursing_${i}" value="1" ${memberData.is_nursing ? 'checked' : ''}
                                    class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                <label for="nursing_${i}" class="mr-2 text-base font-medium text-gray-700">مرضعة</label>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(memberCard);
            
            // إضافة event listener لاختيار العلاقة
            const relationshipSelect = memberCard.querySelector(`#relationship_${i}`);
            const pregnancyContainer = memberCard.querySelector(`#pregnancy_nursing_${i}`);
            
            if (relationshipSelect && pregnancyContainer) {
                relationshipSelect.addEventListener('change', function() {
                    if (this.value === 'زوج/ة') {
                        pregnancyContainer.style.display = 'block';
                    } else {
                        pregnancyContainer.style.display = 'none';
                        // إلغاء تحديد الحقول عند تغيير العلاقة
                        const pregnantCheckbox = memberCard.querySelector(`#pregnant_${i}`);
                        const nursingCheckbox = memberCard.querySelector(`#nursing_${i}`);
                        if (pregnantCheckbox) pregnantCheckbox.checked = false;
                        if (nursingCheckbox) nursingCheckbox.checked = false;
                    }
                });
            }
            
            // إضافة event listener لزر الحذف
            const deleteBtn = memberCard.querySelector('.delete-member-btn');
            deleteBtn.addEventListener('click', function() {
                // التحقق من عدد الأفراد قبل الحذف (يجب أن يكون هناك على الأقل 1 = رب الأسرة)
                const memberCards = container.querySelectorAll('[data-member-index]');
                if (memberCards.length <= 1) {
                    showFlashNotification('لا يمكن حذف آخر فرد. يجب أن يكون هناك على الأقل رب الأسرة.', 'error');
                    return;
                }
                
                // رسالة تأكيد قبل الحذف
                Swal.fire({
                    title: 'تأكيد الحذف',
                    text: 'هل أنت متأكد من حذف هذا الفرد؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280'
                }).then((result) => {
                    if (result.isConfirmed) {
                memberCard.remove();
                updateMemberLabels();
                updateNumberInput();
                    }
                });
            });
        }
        
        // تحديث تسميات الأفراد بعد الحذف
        function updateMemberLabels() {
            const memberCards = container.querySelectorAll('[data-member-index]');
            memberCards.forEach((card, index) => {
                const title = card.querySelector('h4');
                if (title) {
                    title.textContent = `فرد ${index + 1}`;
                }
                card.setAttribute('data-member-index', index);
                
                // تحديث أسماء الحقول
                const inputs = card.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(/family_members\[\d+\]/, `family_members[${index}]`);
                    }
                    if (input.id) {
                        input.id = input.id.replace(/\d+/, index);
                    }
                });
                const labels = card.querySelectorAll('label');
                labels.forEach(label => {
                    if (label.getAttribute('for')) {
                        label.setAttribute('for', label.getAttribute('for').replace(/\d+/, index));
                    }
                });
            });
        }
        
        // تحديث حقل عدد الأفراد بناءً على عدد النماذج الموجودة
        function updateNumberInput() {
            const memberCards = container.querySelectorAll('[data-member-index]');
            const actualCount = memberCards.length;
            numberInput.value = actualCount + 1; // +1 لرب الأسرة
        }
    }

    // تحديث النماذج عند تغيير العدد
    numberInput.addEventListener('input', function() {
        const count = parseInt(this.value) || 0;
        renderFamilyMembersForms(count);
    });

    // عرض النماذج عند تحميل الصفحة
    const initialCount = parseInt(numberInput.value) || 0;
    if (initialCount > 0) {
        renderFamilyMembersForms(initialCount);
    }
});
</script>
@endsection
