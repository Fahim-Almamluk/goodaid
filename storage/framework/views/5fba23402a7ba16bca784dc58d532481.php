<?php $__env->startSection('title', 'توزيع الطرد: ' . $batch->name); ?>

<?php $__env->startPush('styles'); ?>
<style>
.beneficiary-row {
    transition: background-color 0.2s;
}
.beneficiary-row:hover {
    background-color: #f9fafb;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">توزيع الطرد: <?php echo e($batch->name); ?></h1>
            <p class="text-gray-600 mt-2 text-lg"><?php echo e($batch->type_name); ?></p>
        </div>
        <a href="<?php echo e(route('batches.index')); ?>" class="px-5 py-2.5 bg-red-500 text-white rounded-xl hover:bg-red-600 hover:shadow-lg transition-all duration-200 text-sm font-semibold flex items-center gap-2">
            العودة للقائمة
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
    </div>

    <!-- معلومات الطرد -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">معلومات الطرد</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">اسم الطرد</label>
                <p class="text-lg font-semibold text-gray-900"><?php echo e($batch->name); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">نوع الطرد</label>
                <p class="text-lg font-semibold text-gray-900"><?php echo e($batch->type_name); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">الكمية</label>
                <p class="text-lg font-semibold text-gray-900"><?php echo e($batch->quantity ?? '-'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">حالة الطرد</label>
                <?php if($batch->status === 'draft'): ?>
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">جديد</span>
                <?php else: ?>
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">معتمد</span>
                <?php endif; ?>
            </div>
            <?php if($batch->batch_date): ?>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ الطرد</label>
                <p class="text-lg font-semibold text-gray-900"><?php echo e($batch->batch_date->format('Y-m-d')); ?></p>
            </div>
        <?php endif; ?>
        </div>
    </div>

    <!-- إحصائيات الاستلام -->
    <?php
        $totalBeneficiaries = $batch->beneficiaries()->count();
        $receivedCount = $batch->beneficiaries()->where('batch_recipients.received', true)->count();
        $notReceivedCount = $totalBeneficiaries - $receivedCount;
        $receivedPercentage = $totalBeneficiaries > 0 ? round(($receivedCount / $totalBeneficiaries) * 100, 1) : 0;
        $notReceivedPercentage = $totalBeneficiaries > 0 ? round(($notReceivedCount / $totalBeneficiaries) * 100, 1) : 0;
        ?>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- إجمالي المستفيدين -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي المستفيدين</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2 total-count"><?php echo e($totalBeneficiaries); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- تم الاستلام -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">تم الاستلام</p>
                    <p class="text-3xl font-bold text-green-600 mt-2 received-count"><?php echo e($receivedCount); ?></p>
                    <p class="text-sm text-gray-500 mt-1 received-percentage"><?php echo e($receivedPercentage); ?>%</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- لم يتم الاستلام -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">لم يتم الاستلام</p>
                    <p class="text-3xl font-bold text-red-600 mt-2 not-received-count"><?php echo e($notReceivedCount); ?></p>
                    <p class="text-sm text-gray-500 mt-1 not-received-percentage"><?php echo e($notReceivedPercentage); ?>%</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                </div>
            </div>
        </div>
        </div>
        
    <!-- قائمة المستفيدين -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">قائمة المستفيدين</h2>
        
        <!-- Filters -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label for="search-input" class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <input type="text" id="search-input" placeholder="ابحث بالاسم، رقم الهوية، أو الهاتف..." 
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-2">حالة الاستلام</label>
                        <select id="status-filter" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">جميع الحالات</option>
                    <option value="received">تم الاستلام</option>
                    <option value="not_received">لم يتم الاستلام</option>
                        </select>
                    </div>
            <div class="flex gap-2">
                <button type="button" onclick="clearFilters()" class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all font-semibold flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>تفريغ الحقول</span>
                </button>
                <button type="button" onclick="exportExcel()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-semibold flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>تصدير Excel</span>
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">#</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">الاسم</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">رقم الهوية</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">الهاتف</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">عدد الأفراد</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">حالة الاستلام</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">تاريخ الاستلام</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">المدخل</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="beneficiaries-table-body">
                    <?php $__empty_1 = true; $__currentLoopData = $beneficiaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $beneficiary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $pivot = $beneficiary->pivot;
                            $received = (bool)($pivot->received ?? false);
                            $receivedAt = $pivot->received_at ?? null;
                            $approvedBy = $pivot->approved_by ?? null;
                        ?>
                        <tr class="beneficiary-row" 
                            data-beneficiary-id="<?php echo e($beneficiary->id); ?>"
                            data-name="<?php echo e(strtolower($beneficiary->name)); ?>"
                            data-national-id="<?php echo e(strtolower($beneficiary->national_id ?? '')); ?>"
                            data-phone="<?php echo e(strtolower($beneficiary->phone ?? '')); ?>"
                            data-received="<?php echo e($received ? 'received' : 'not_received'); ?>">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center"><?php echo e($beneficiaries->firstItem() + $index); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center"><?php echo e($beneficiary->name); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center"><?php echo e($beneficiary->national_id ?? '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center"><?php echo e($beneficiary->phone ?? '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center"><?php echo e($beneficiary->family_members_count + 1); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center status-cell">
                                <?php if($received): ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">تم الاستلام</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">لم يستلم</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center received-at-cell">
                                <?php if($receivedAt): ?>
                                    <?php echo e(\Carbon\Carbon::parse($receivedAt)->format('Y-m-d H:i')); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center approved-by-cell">
                                <?php if($approvedBy): ?>
                                    <?php echo e(\App\Models\User::find($approvedBy)->name ?? '-'); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <?php if(!$received): ?>
                                    <button type="button" 
                                        onclick="toggleReceived(<?php echo e($beneficiary->id); ?>, true)"
                                        class="px-4 py-2 rounded-lg hover:bg-green-700 transition-all text-xs font-semibold whitespace-nowrap shadow-md hover:shadow-lg"
                                        style="background-color: #16a34a; color: white;">
                                        <i class="fas fa-check ml-1"></i> تسليم
                                    </button>
                                <?php else: ?>
                                    <button type="button" 
                                        onclick="toggleReceived(<?php echo e($beneficiary->id); ?>, false)"
                                        class="px-4 py-2 rounded-lg hover:bg-red-700 transition-all text-xs font-semibold whitespace-nowrap shadow-md hover:shadow-lg"
                                        style="background-color: #dc2626; color: white;">
                                        <i class="fas fa-times ml-1"></i> إلغاء التسليم
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                لا يوجد مستفيدين في هذا الطرد
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($beneficiaries->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200 mt-4">
                <div class="text-sm text-gray-600 text-center mb-2">
                    عرض <strong><?php echo e($beneficiaries->firstItem() ?? 0); ?></strong> إلى <strong><?php echo e($beneficiaries->lastItem() ?? 0); ?></strong> من <strong><?php echo e($beneficiaries->total()); ?></strong>
                </div>
                <div class="flex items-center justify-center">
                    <?php echo e($beneficiaries->onEachSide(1)->links()); ?>

                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleReceived(beneficiaryId, received) {
    const status = received ? 'true' : 'false';
    const action = received ? 'تسليم' : 'إلغاء التسليم';
    
    Swal.fire({
        title: 'تأكيد',
        text: `هل أنت متأكد من ${action}؟`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280'
    }).then((result) => {
        if (result.isConfirmed) {
            // إيجاد الصف والزر
            const row = document.querySelector(`tr[data-beneficiary-id="${beneficiaryId}"]`);
            const button = row.querySelector('button');
            const statusCell = row.querySelector('.status-cell');
            const receivedAtCell = row.querySelector('.received-at-cell');
            const approvedByCell = row.querySelector('.approved-by-cell');
            
            // تعطيل الزر مؤقتاً
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري التحديث...';
            
            fetch(`/batches/<?php echo e($batch->id); ?>/recipients/${beneficiaryId}/toggle-received`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ received: received })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showFlashNotification(data.message || (received ? 'تم التسليم بنجاح' : 'تم إلغاء التسليم بنجاح'), 'success');
                    
                    // تحديث الزر
                    if (received) {
                        button.onclick = function() { toggleReceived(beneficiaryId, false); };
                        button.className = 'px-4 py-2 rounded-lg hover:bg-red-700 transition-all text-xs font-semibold whitespace-nowrap shadow-md hover:shadow-lg';
                        button.style.backgroundColor = '#dc2626';
                        button.style.color = 'white';
                        button.innerHTML = '<i class="fas fa-times ml-1"></i> إلغاء التسليم';
                        
                        // تحديث حالة الاستلام
                        statusCell.innerHTML = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">تم الاستلام</span>';
                        
                        // تحديث تاريخ الاستلام
                        const now = new Date();
                        receivedAtCell.textContent = now.toLocaleString('ar-EG', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' });
                        
                        // تحديث اسم المعتمد
                        approvedByCell.textContent = '<?php echo e(auth()->user()->name); ?>';
                    } else {
                        button.onclick = function() { toggleReceived(beneficiaryId, true); };
                        button.className = 'px-4 py-2 rounded-lg hover:bg-green-700 transition-all text-xs font-semibold whitespace-nowrap shadow-md hover:shadow-lg';
                        button.style.backgroundColor = '#16a34a';
                        button.style.color = 'white';
                        button.innerHTML = '<i class="fas fa-check ml-1"></i> تسليم';
                        
                        // تحديث حالة الاستلام
                        statusCell.innerHTML = '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">لم يستلم</span>';
                        
                        // إعادة تعيين تاريخ الاستلام
                        receivedAtCell.textContent = '-';
                        
                        // إعادة تعيين المعتمد
                        approvedByCell.textContent = '-';
                    }
                    
                    // تحديث الإحصائيات بدون reload
                    updateStatistics(received);
                    
                } else {
                    showFlashNotification(data.message || 'حدث خطأ أثناء تحديث الحالة', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFlashNotification('حدث خطأ أثناء تحديث الحالة', 'error');
            })
            .finally(() => {
                button.disabled = false;
            });
        }
    });
}

function updateStatistics(received) {
    // جلب العدادات الحالية
    const receivedCountEl = document.querySelector('.received-count');
    const notReceivedCountEl = document.querySelector('.not-received-count');
    const totalEl = document.querySelector('.total-count');
    
    if (!receivedCountEl || !notReceivedCountEl || !totalEl) return;
    
    let receivedCount = parseInt(receivedCountEl.textContent);
    let notReceivedCount = parseInt(notReceivedCountEl.textContent);
    const total = parseInt(totalEl.textContent);
    
    // تحديث العدادات
    if (received) {
        receivedCount++;
        notReceivedCount--;
    } else {
        receivedCount--;
        notReceivedCount++;
    }
    
    // تحديث النصوص
    receivedCountEl.textContent = receivedCount;
    notReceivedCountEl.textContent = notReceivedCount;
    
    // تحديث النسب المئوية
    const receivedPercentage = total > 0 ? ((receivedCount / total) * 100).toFixed(1) : 0;
    const notReceivedPercentage = total > 0 ? ((notReceivedCount / total) * 100).toFixed(1) : 0;
    
    const receivedPercentEl = document.querySelector('.received-percentage');
    const notReceivedPercentEl = document.querySelector('.not-received-percentage');
    
    if (receivedPercentEl) receivedPercentEl.textContent = receivedPercentage + '%';
    if (notReceivedPercentEl) notReceivedPercentEl.textContent = notReceivedPercentage + '%';
}

// Search and Filter functionality
function filterTable() {
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');
    const searchTerm = searchInput?.value.toLowerCase().trim() || '';
    const statusValue = statusFilter?.value || '';
    const rows = document.querySelectorAll('.beneficiary-row');
    
    rows.forEach(row => {
        const name = (row.getAttribute('data-name') || '').toLowerCase();
        const nationalId = (row.getAttribute('data-national-id') || '').toLowerCase();
        const phone = (row.getAttribute('data-phone') || '').toLowerCase();
        const received = row.getAttribute('data-received') || '';
        
        const matchesSearch = !searchTerm || 
            name.includes(searchTerm) || 
            nationalId.includes(searchTerm) || 
            phone.includes(searchTerm);
        
        const matchesStatus = !statusValue || 
            (statusValue === 'received' && received === 'received') ||
            (statusValue === 'not_received' && received === 'not_received');
        
        if (matchesSearch && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Clear filters function
function clearFilters() {
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');
    
    if (searchInput) {
        searchInput.value = '';
    }
    
    if (statusFilter) {
        statusFilter.selectedIndex = 0;
    }
    
    filterTable();
}

// Export Excel function
function exportExcel() {
    const searchValue = document.getElementById('search-input')?.value || '';
    const statusValue = document.getElementById('status-filter')?.value || '';
    
    // Build URL with parameters
    let exportUrl = '<?php echo e(route("batches.distribution.export-excel", $batch)); ?>';
    const params = new URLSearchParams();
    
    if (searchValue) {
        params.append('search', searchValue);
    }
    if (statusValue) {
        params.append('status', statusValue);
    }
    
    if (params.toString()) {
        exportUrl += '?' + params.toString();
    }
    
    window.location.href = exportUrl;
}

// Initialize event listeners on page load
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');
    
    // Real-time search as user types
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterTable();
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterTable();
            }
        });
    }
    
    // Status filter change
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            filterTable();
        });
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\goodaid\resources\views/batches/distribution.blade.php ENDPATH**/ ?>