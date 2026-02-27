<?php $__empty_1 = true; $__currentLoopData = $beneficiaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $beneficiary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr class="beneficiary-row hover:bg-gray-50 transition-colors" 
        data-name="<?php echo e(strtolower($beneficiary->name)); ?>"
        data-national-id="<?php echo e(strtolower($beneficiary->national_id ?? '')); ?>"
        data-phone="<?php echo e(strtolower($beneficiary->phone ?? '')); ?>"
        data-residence="<?php echo e(strtolower($beneficiary->residence_status ?? '')); ?>"
        data-relationship="<?php echo e(strtolower($beneficiary->relationship ?? '')); ?>"
        data-status="<?php echo e($beneficiary->is_active ? 'active' : 'inactive'); ?>">
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center"><?php echo e($loop->iteration + ($beneficiaries->currentPage() - 1) * $beneficiaries->perPage()); ?></td>
        <?php if($batch): ?>
        <td class="px-6 py-4 whitespace-nowrap text-center">
                <input type="checkbox" name="beneficiary_ids[]" value="<?php echo e($beneficiary->id); ?>" 
                class="beneficiary-checkbox w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500"
                <?php echo e($batch && $batch->beneficiaries->contains($beneficiary->id) ? 'checked disabled' : ''); ?>>
        </td>
        <?php endif; ?>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
            <a href="<?php echo e(route('beneficiaries.show', $beneficiary)); ?>" class="text-emerald-600 hover:text-emerald-900 hover:underline">
                <?php echo e($beneficiary->name); ?>

            </a>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center"><?php echo e($beneficiary->national_id ?? '-'); ?></td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center"><?php echo e($beneficiary->phone ?? '-'); ?></td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center"><?php echo e($beneficiary->number_of_members ?? '-'); ?></td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center"><?php echo e($beneficiary->formatted_relationship ?? '-'); ?></td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center"><?php echo e($beneficiary->residence_status === 'displaced' ? 'نازح' : 'مقيم'); ?></td>
        <td class="px-6 py-4 whitespace-nowrap text-center">
            <?php if($beneficiary->is_active): ?>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">موجود</span>
            <?php else: ?>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">غير موجود</span>
            <?php endif; ?>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-center">
            <div class="flex flex-col gap-1">
                <span class="text-gray-600 text-xs">مستفادة: <span class="font-semibold text-gray-900"><?php echo e($beneficiary->total_batches_count ?? 0); ?></span></span>
                <span class="text-emerald-600 text-xs">مسلمة: <span class="font-semibold text-emerald-700"><?php echo e($beneficiary->received_batches_count ?? 0); ?></span></span>
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
            <div class="flex items-center justify-center gap-2">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'beneficiaries.view')): ?>
                <a href="<?php echo e(route('beneficiaries.show', $beneficiary)); ?>" class="px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200 text-xs font-semibold flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    عرض
                </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'beneficiaries.edit')): ?>
                <a href="<?php echo e(route('beneficiaries.edit', $beneficiary)); ?>" class="px-3 py-1.5 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-all duration-200 text-xs font-semibold flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    تعديل
                </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'beneficiaries.delete')): ?>
                <form action="<?php echo e(route('beneficiaries.destroy', $beneficiary)); ?>" method="POST" class="inline" onsubmit="event.preventDefault(); const form = this; Swal.fire({title: 'تأكيد الحذف', text: 'هل أنت متأكد من حذف هذا المستفيد؟', icon: 'warning', showCancelButton: true, confirmButtonText: 'نعم، احذف', cancelButtonText: 'إلغاء', confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280'}).then((result) => { if (result.isConfirmed) { form.submit(); } });">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="px-3 py-1.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200 text-xs font-semibold flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        حذف
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr>
        <td colspan="<?php echo e($batch ? '11' : '10'); ?>" class="px-6 py-12 text-center text-gray-500">
            لا يوجد مستفيدين
        </td>
    </tr>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\goodaid\resources\views/beneficiaries/partials/rows.blade.php ENDPATH**/ ?>