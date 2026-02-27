<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title>الملف الشخصي - GoodAid</title>
    
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
            <!-- Header -->
            <div>
                <h1 class="text-4xl font-bold text-gray-900"><?php echo e($beneficiary->name); ?></h1>
                <p class="text-gray-600 mt-2 text-lg">تحديث بياناتك الشخصية</p>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-10">
                <form action="<?php echo e(route('beneficiary.profile.update')); ?>" method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Number of Members -->
                        <div class="md:col-span-2">
                            <label for="number_of_members" class="block text-base font-semibold text-gray-700 mb-2">عدد الأفراد *</label>
                            <input type="number" name="number_of_members" id="number_of_members" value="<?php echo e(old('number_of_members', ($beneficiary->familyMembers->count() + 1) ?: $beneficiary->number_of_members)); ?>" min="1" required
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                            <?php $__errorArgs = ['number_of_members'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <p class="mt-1 text-sm text-gray-500">عدد الأفراد الإجمالي (بما في ذلك رب الأسرة)</p>
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-base font-semibold text-gray-700 mb-2">الاسم الكامل *</label>
                            <input type="text" name="name" id="name" value="<?php echo e(old('name', $beneficiary->name)); ?>" required
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- National ID (Read-only) -->
                        <div>
                            <label for="national_id" class="block text-base font-semibold text-gray-700 mb-2">رقم الهوية</label>
                            <input type="text" value="<?php echo e($beneficiary->national_id); ?>" disabled
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 bg-gray-100 text-gray-600 text-base">
                            <p class="mt-1 text-xs text-gray-500">لا يمكن تعديل رقم الهوية</p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-base font-semibold text-gray-700 mb-2">رقم الهاتف *</label>
                            <input type="text" name="phone" id="phone" value="<?php echo e(old('phone', $beneficiary->phone)); ?>" required
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Relationship -->
                        <div>
                            <label for="relationship" class="block text-base font-semibold text-gray-700 mb-2">العلاقة</label>
                            <select name="relationship" id="relationship"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                                <option value="">اختر العلاقة</option>
                                <option value="زوج/ة" <?php echo e(old('relationship', $beneficiary->formatted_relationship) === 'زوج/ة' ? 'selected' : ''); ?>>زوج/ة</option>
                                <option value="أرمل/ة" <?php echo e(old('relationship', $beneficiary->formatted_relationship) === 'أرمل/ة' ? 'selected' : ''); ?>>أرمل/ة</option>
                            </select>
                            <?php $__errorArgs = ['relationship'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Residence Status -->
                        <div>
                            <label for="residence_status" class="block text-base font-semibold text-gray-700 mb-2">الحالة السكنية</label>
                            <select name="residence_status" id="residence_status"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                                <option value="resident" <?php echo e(old('residence_status', $beneficiary->residence_status ?? 'resident') === 'resident' ? 'selected' : ''); ?>>مقيم</option>
                                <option value="displaced" <?php echo e(old('residence_status', $beneficiary->residence_status ?? 'resident') === 'displaced' ? 'selected' : ''); ?>>نازح</option>
                            </select>
                            <?php $__errorArgs = ['residence_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Family Members Section -->
                    <div class="mt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">أفراد الأسرة</h3>
                        <div id="family-members-container" class="space-y-4">
                            <?php if($beneficiary->familyMembers && $beneficiary->familyMembers->count() > 0): ?>
                                <?php $__currentLoopData = $beneficiary->familyMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="family-member-item bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">الاسم *</label>
                                                <input type="text" name="family_members[<?php echo e($index); ?>][name]" value="<?php echo e($member->name); ?>" required
                                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">رقم الهوية</label>
                                                <input type="text" name="family_members[<?php echo e($index); ?>][national_id]" value="<?php echo e($member->national_id); ?>"
                                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">تاريخ الميلاد</label>
                                                <input type="date" name="family_members[<?php echo e($index); ?>][birth_date]" value="<?php echo e($member->birth_date ? $member->birth_date->format('Y-m-d') : ''); ?>"
                                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">العلاقة برب الأسرة</label>
                                                <select name="family_members[<?php echo e($index); ?>][relationship]"
                                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-sm">
                                                    <option value="">اختر العلاقة</option>
                                                    <option value="زوج/ة" <?php echo e(old("family_members.$index.relationship", $member->relationship) === 'زوج/ة' ? 'selected' : ''); ?>>زوج/ة</option>
                                                    <option value="ابن/ة" <?php echo e(old("family_members.$index.relationship", $member->relationship) === 'ابن/ة' ? 'selected' : ''); ?>>ابن/ة</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-2 flex gap-4">
                                                <label class="flex items-center gap-2">
                                                    <input type="checkbox" name="family_members[<?php echo e($index); ?>][is_pregnant]" value="1" <?php echo e($member->is_pregnant ? 'checked' : ''); ?>

                                                        class="w-4 h-4 rounded border-gray-300">
                                                    <span class="text-sm text-gray-700">حامل</span>
                                                </label>
                                                <label class="flex items-center gap-2">
                                                    <input type="checkbox" name="family_members[<?php echo e($index); ?>][is_nursing]" value="1" <?php echo e($member->is_nursing ? 'checked' : ''); ?>

                                                        class="w-4 h-4 rounded border-gray-300">
                                                    <span class="text-sm text-gray-700">مرضعة</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-base font-semibold text-gray-700 mb-2">العنوان</label>
                        <input type="text" name="address" id="address" value="<?php echo e(old('address', $beneficiary->address)); ?>"
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-base">
                        <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:shadow-lg transition-all font-semibold text-base">
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>

            <!-- Batches Info -->
            <?php if($beneficiary->batches && $beneficiary->batches->count() > 0): ?>
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="px-8 py-5 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">سجل الطرود المخصصة (<?php echo e($beneficiary->batches->count()); ?>)</h2>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">اسم الطرد</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">حالة الاستلام</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">تاريخ ووقت التسليم</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php $__currentLoopData = $beneficiary->batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-semibold text-gray-900"><?php echo e($batch->name); ?></p>
                                                <p class="text-sm text-gray-500 mt-1"><?php echo e($batch->type_name); ?></p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if($batch->pivot->received ?? false): ?>
                                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">تم الاستلام</span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">لم يتم الاستلام</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if($batch->pivot->received_at): ?>
                                                <div class="text-sm text-gray-900">
                                                    <?php
                                                        $receivedDate = is_string($batch->pivot->received_at) ? \Carbon\Carbon::parse($batch->pivot->received_at) : $batch->pivot->received_at;
                                                    ?>
                                                    <p><?php echo e($receivedDate->format('d/m/Y')); ?></p>
                                                    <p class="text-gray-500"><?php echo e($receivedDate->format('H:i')); ?></p>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-gray-400 text-sm">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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

        document.addEventListener('DOMContentLoaded', function() {
            const numberInput = document.getElementById('number_of_members');
            const container = document.getElementById('family-members-container');
            
            // قائمة علاقات القرابة
            const relationships = [
                { value: 'زوج/ة', label: 'زوج/ة' },
                { value: 'ابن/ة', label: 'ابن/ة' },
            ];

            <?php
                $existingMembersArray = $beneficiary->familyMembers->map(function($member) {
                    return [
                        'name' => $member->name,
                        'national_id' => $member->national_id,
                        'birth_date' => $member->birth_date ? $member->birth_date->format('Y-m-d') : '',
                        'relationship' => $member->relationship,
                        'is_pregnant' => $member->is_pregnant,
                        'is_nursing' => $member->is_nursing,
                    ];
                })->values()->all();
            ?>

            const existingMembers = <?php echo json_encode($existingMembersArray, 15, 512) ?>;

            function renderFamilyMembersForms(count) {
                container.innerHTML = '';
                
                // عدد الأفراد الإجمالي يشمل رب الأسرة، لذلك نطرح 1 للحصول على عدد أفراد العائلة
                const familyMembersCount = Math.max(0, count - 1);
                
                if (familyMembersCount <= 0) {
                    return;
                }

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
                        const memberCards = container.querySelectorAll('[data-member-index]');
                        if (memberCards.length <= 1) {
                            showFlashNotification('لا يمكن حذف آخر فرد. يجب أن يكون هناك على الأقل رب الأسرة.', 'error');
                            return;
                        }
                        
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
                    numberInput.value = actualCount + 1;
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
</body>
</html>

<?php /**PATH C:\xampp\htdocs\goodaid\resources\views/beneficiary/profile.blade.php ENDPATH**/ ?>