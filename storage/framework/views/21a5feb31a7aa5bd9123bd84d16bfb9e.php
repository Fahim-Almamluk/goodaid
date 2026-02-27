<?php $__env->startSection('title', 'إدارة الطرود'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">إدارة الطرود</h1>
            <p class="text-gray-600 mt-2 text-lg">عرض وإدارة جميع الطرود المتاحة</p>
        </div>
        <div class="flex gap-3">
            <a href="<?php echo e(route('batches.create')); ?>" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 text-sm font-semibold">
                <i class="fas fa-plus ml-2"></i> إضافة طرد جديد
            </a>
        </div>
    </div>

    <!-- Success Message -->
    <?php if(session('success')): ?>
    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
        <p class="text-green-800 font-semibold"><?php echo e(session('success')); ?></p>
    </div>
    <?php endif; ?>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <form action="<?php echo e(route('batches.filter')); ?>" method="POST" id="filter-form" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <?php echo csrf_field(); ?>
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">اسم الطرد</label>
                <input type="text" name="search" id="search" value="<?php echo e($filters['search'] ?? ''); ?>" placeholder="ابحث بالاسم أو النوع..."
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" id="status"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">الكل</option>
                    <option value="draft" <?php echo e(($filters['status'] ?? '') === 'draft' ? 'selected' : ''); ?>>جديد</option>
                    <option value="active" <?php echo e(($filters['status'] ?? '') === 'active' ? 'selected' : ''); ?>>معتمد</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all font-semibold flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span>بحث</span>
                </button>
                <button type="button" id="clear-filters-btn" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all font-semibold flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span> تفريغ الحقول</span>
                </button>
                <button type="button" onclick="exportExcel()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-semibold flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span> تصدير Excel</span>
                </button>
            </div>
        </form>
        

    </div>

    <!-- Batches Table -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">الاسم</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">النوع</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">الكمية</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">تاريخ الطرد</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">عدد المستفيدين</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">عدد مستلمين</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">عدد الغير مستلمين</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center"><?php echo e($loop->iteration + ($batches->currentPage() - 1) * $batches->perPage()); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center"><?php echo e($batch->name); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center"><?php echo e($batch->type_name); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center"><?php echo e($batch->quantity ?? '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center"><?php echo e($batch->batch_date ? $batch->batch_date->format('Y-m-d') : '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center"><?php echo e($batch->beneficiaries_count); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-emerald-600"><?php echo e($batch->received_count ?? 0); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-600"><?php echo e(($batch->beneficiaries_count ?? 0) - ($batch->received_count ?? 0)); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if($batch->status === 'draft'): ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">جديد</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">معتمد</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <?php if($batch->status === 'active'): ?>
                                        <a href="<?php echo e(route('batches.distribution', $batch)); ?>" 
                                           class="px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-all text-xs font-semibold whitespace-nowrap"
                                           style="background-color: #16a34a; color: white; text-decoration: none; display: inline-block;"
                                           title="إدارة الطرد - التوزيع">
                                            <i class="fas fa-cog ml-1"></i> إدارة
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('batches.manage', $batch)); ?>" 
                                           class="px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-all text-xs font-semibold whitespace-nowrap"
                                           style="background-color: #16a34a; color: white; text-decoration: none; display: inline-block;"
                                           title="إدارة الطرد - استكمال الطرد">
                                            <i class="fas fa-cog ml-1"></i> إدارة
                                        </a>
                                    <?php endif; ?>
                                    <form action="<?php echo e(route('batches.destroy', $batch)); ?>" method="POST" class="inline delete-form">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" 
                                                class="px-3 py-1.5 rounded-lg hover:bg-red-700 transition-all text-xs font-semibold whitespace-nowrap"
                                                style="background-color: #dc2626; color: white; border: none; cursor: pointer;"
                                                title="حذف">
                                            <i class="fas fa-trash ml-1"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-500">لا توجد طرود متاحة.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($batches->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-600 text-center mb-2">
                    عرض <strong><?php echo e($batches->firstItem() ?? 0); ?></strong> إلى <strong><?php echo e($batches->lastItem() ?? 0); ?></strong> من <strong><?php echo e($batches->total()); ?></strong>
                </div>
                <div class="flex items-center justify-center">
                    <?php echo e($batches->onEachSide(1)->links()); ?>

                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'تأكيد الحذف',
                text: 'هل أنت متأكد من حذف هذا الطرد؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
    // Clear filters button behavior: POST to reset endpoint
    document.getElementById('clear-filters-btn')?.addEventListener('click', function() {
        // Create a form and submit via POST
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route('batches.filter.reset')); ?>';
        
        // Add CSRF token
        var csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    });
});

// Export Excel function - uses session filters, just navigate to export URL
function exportExcel() {
    window.location.href = '<?php echo e(route('batches.export-excel')); ?>';
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\goodaid\resources\views/batches/index.blade.php ENDPATH**/ ?>