/**
 * User Permissions Management
 * Laravel + Tailwind CSS v4 + RTL
 */

(function() {
    'use strict';

    // State
    const state = {
        selectedUserId: null,
        permissions: [],
        assignedOriginal: new Set(),
        assignedDraft: new Set(),
        q: '',
        status: 'all',
        expandedModules: new Set(),
        userSearchTimeout: null,
        permissionSearchTimeout: null,
    };

    // API URLs
    const API = {
        users: '/admin/api/users',
        userPermissions: '/admin/api/user-permissions',
        sync: '/admin/api/user-permissions/sync',
    };

    // DOM Elements
    const elements = {
        userSelect: document.getElementById('user-select'),
        statusFilterBtns: document.querySelectorAll('.status-filter-btn'),
        permissionSearchInput: document.getElementById('permission-search-input'),
        assignVisibleBtn: document.getElementById('assign-visible-btn'),
        revokeVisibleBtn: document.getElementById('revoke-visible-btn'),
        expandAllBtn: document.getElementById('expand-all-btn'),
        collapseAllBtn: document.getElementById('collapse-all-btn'),
        saveBtn: document.getElementById('save-btn'),
        permissionsContainer: document.getElementById('permissions-container'),
        toastContainer: document.getElementById('toast-container'),
    };

    // Initialize
    function init() {
        setupEventListeners();
        disableUI();
    }

    // Setup Event Listeners
    function setupEventListeners() {
        // Status filter
        elements.statusFilterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const status = this.dataset.status;
                setStatusFilter(status);
            });
        });

        // Permission search - Live search
        elements.permissionSearchInput.addEventListener('input', function() {
            clearTimeout(state.permissionSearchTimeout);
            state.permissionSearchTimeout = setTimeout(() => {
                state.q = this.value.trim();
                // If user is selected, reload permissions from server with new search query
                if (state.selectedUserId) {
                    loadUserPermissions();
                } else {
                    renderPermissions();
                }
            }, 300);
        });

        // Action buttons
        elements.assignVisibleBtn.addEventListener('click', handleAssignVisible);
        elements.revokeVisibleBtn.addEventListener('click', handleRevokeVisible);
        elements.expandAllBtn.addEventListener('click', handleExpandAll);
        elements.collapseAllBtn.addEventListener('click', handleCollapseAll);
        elements.saveBtn.addEventListener('click', handleSave);
    }

    // User Selection (called from Select2)
    function selectUser(userId, userName, username) {
        state.selectedUserId = userId;
        loadUserPermissions();
    }

    // Handle Change User (called from Select2 clear)
    function handleChangeUser() {
        state.selectedUserId = null;
        state.permissions = [];
        state.assignedOriginal.clear();
        state.assignedDraft.clear();
        renderPermissions();
        disableUI();
    }

    // Make functions globally available for Select2
    window.selectUser = selectUser;
    window.handleChangeUser = handleChangeUser;
    window.showToast = showToast; // Make toast available globally for testing

    // Load User Permissions
    async function loadUserPermissions() {
        try {
            enableUI();
            elements.permissionsContainer.innerHTML = '<div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 text-center text-gray-500">جاري التحميل...</div>';

            const url = `${API.userPermissions}?user_id=${state.selectedUserId}&q=${encodeURIComponent(state.q)}&status=${state.status}`;
            const response = await fetch(url);
            const data = await response.json();

            state.permissions = data.permissions;
            state.assignedOriginal = new Set(data.assigned_ids.map(id => parseInt(id)));
            state.assignedDraft = new Set(state.assignedOriginal);

            renderPermissions();
            updateSaveButton();
        } catch (error) {
            console.error('Error loading permissions:', error);
            showToast('خطأ في تحميل الصلاحيات', 'error');
            elements.permissionsContainer.innerHTML = '<div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 text-center text-red-500">حدث خطأ في تحميل الصلاحيات</div>';
        }
    }

    // Render Permissions Tree
    function renderPermissions() {
        if (!state.selectedUserId || state.permissions.length === 0) {
            elements.permissionsContainer.innerHTML = '<div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 text-center text-gray-500">لا توجد صلاحيات</div>';
            return;
        }

        // Group by module
        const modules = {};
        state.permissions.forEach(perm => {
            if (!modules[perm.module]) {
                modules[perm.module] = [];
            }
            modules[perm.module].push(perm);
        });

        // Filter visible permissions
        const visiblePermissions = state.permissions.filter(perm => {
            const isAssigned = state.assignedDraft.has(perm.id);
            if (state.status === 'assigned') return isAssigned;
            if (state.status === 'unassigned') return !isAssigned;
            return true;
        });

        if (visiblePermissions.length === 0) {
            elements.permissionsContainer.innerHTML = '<div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 text-center text-gray-500">لا توجد صلاحيات تطابق الفلتر</div>';
            return;
        }

        const visibleModules = new Set(visiblePermissions.map(p => p.module));

        elements.permissionsContainer.innerHTML = Object.keys(modules)
            .filter(module => visibleModules.has(module))
            .sort()
            .map(module => {
                const modulePerms = modules[module].filter(p => visiblePermissions.some(vp => vp.id === p.id));
                const assignedCount = modulePerms.filter(p => state.assignedDraft.has(p.id)).length;
                const totalCount = modulePerms.length;
                const isExpanded = state.expandedModules.has(module);
                const moduleState = getModuleTriState(modulePerms);

                return renderModuleCard(module, modulePerms, assignedCount, totalCount, isExpanded, moduleState);
            })
            .join('');

        // Attach event listeners
        attachModuleEventListeners();
    }

    function renderModuleCard(module, permissions, assignedCount, totalCount, isExpanded, moduleState) {
        return `
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden" data-module="${module}">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 flex-1">
                            <div class="checkbox-tri-state">
                                <input 
                                    type="checkbox" 
                                    class="module-checkbox"
                                    data-module="${module}"
                                    ${moduleState === 'checked' ? 'checked' : ''}
                                    ${moduleState === 'indeterminate' ? 'data-indeterminate="true"' : ''}
                                />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900 text-lg">${module}</h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded">${totalCount} صلاحية</span>
                                    <span class="text-xs text-gray-600">${assignedCount} / ${totalCount} مفعلة</span>
                                </div>
                            </div>
                        </div>
                        <button 
                            class="module-toggle-btn text-gray-600 hover:text-gray-900 p-2"
                            data-module="${module}"
                        >
                            <svg class="w-5 h-5 transform transition-transform ${isExpanded ? '' : '-rotate-90'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="module-body ${isExpanded ? '' : 'hidden'}" data-module="${module}">
                    <div class="p-4 space-y-3">
                        ${permissions.map(perm => renderPermissionItem(perm)).join('')}
                    </div>
                </div>
            </div>
        `;
    }

    function renderPermissionItem(perm) {
        const isAssigned = state.assignedDraft.has(perm.id);
        return `
            <div class="flex items-start justify-between gap-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex-1">
                    <div class="font-bold text-gray-900 mb-1">${perm.label}</div>
                    ${perm.description ? `<div class="text-sm text-gray-600 mb-2">${perm.description}</div>` : ''}
                    <div class="text-xs font-mono text-gray-500">${perm.key}</div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs px-2 py-1 rounded ${isAssigned ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-600'}">
                        ${isAssigned ? 'مفعلة' : 'غير مفعلة'}
                    </span>
                    <label class="switch">
                        <input 
                            type="checkbox" 
                            class="permission-switch"
                            data-permission-id="${perm.id}"
                            ${isAssigned ? 'checked' : ''}
                        />
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        `;
    }

    function getModuleTriState(permissions) {
        const assigned = permissions.filter(p => state.assignedDraft.has(p.id)).length;
        if (assigned === 0) return 'unchecked';
        if (assigned === permissions.length) return 'checked';
        return 'indeterminate';
    }

    function attachModuleEventListeners() {
        // Module checkboxes
        document.querySelectorAll('.module-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const module = this.dataset.module;
                const isChecked = this.checked;
                toggleModulePermissions(module, isChecked);
            });
        });

        // Module toggle buttons
        document.querySelectorAll('.module-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const module = this.dataset.module;
                toggleModuleExpansion(module);
            });
        });

        // Permission switches
        document.querySelectorAll('.permission-switch').forEach(switchEl => {
            switchEl.addEventListener('change', function() {
                const permissionId = parseInt(this.dataset.permissionId);
                togglePermission(permissionId, this.checked);
            });
        });
    }

    // Toggle Permission
    function togglePermission(permissionId, isAssigned) {
        if (isAssigned) {
            state.assignedDraft.add(permissionId);
        } else {
            state.assignedDraft.delete(permissionId);
        }
        renderPermissions();
        updateSaveButton();
    }

    // Toggle Module Permissions
    function toggleModulePermissions(module, isChecked) {
        const moduleBody = document.querySelector(`.module-body[data-module="${module}"]`);
        if (!moduleBody) return;

        const switches = moduleBody.querySelectorAll('.permission-switch');
        switches.forEach(switchEl => {
            const permissionId = parseInt(switchEl.dataset.permissionId);
            if (isChecked) {
                state.assignedDraft.add(permissionId);
                switchEl.checked = true;
            } else {
                state.assignedDraft.delete(permissionId);
                switchEl.checked = false;
            }
        });

        renderPermissions();
        updateSaveButton();
    }

    // Toggle Module Expansion
    function toggleModuleExpansion(module) {
        if (state.expandedModules.has(module)) {
            state.expandedModules.delete(module);
        } else {
            state.expandedModules.add(module);
        }
        renderPermissions();
    }

    // Status Filter
    function setStatusFilter(status) {
        state.status = status;
        
        elements.statusFilterBtns.forEach(btn => {
            if (btn.dataset.status === status) {
                // Active button - gradient style
                btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                btn.classList.add('bg-gradient-to-r', 'from-emerald-500', 'to-teal-600', 'text-white', 'shadow-lg', 'hover:shadow-xl');
            } else {
                // Inactive button - gray style
                btn.classList.remove('bg-gradient-to-r', 'from-emerald-500', 'to-teal-600', 'text-white', 'shadow-lg', 'hover:shadow-xl');
                btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            }
        });

        renderPermissions();
    }

    // Bulk Actions
    function handleAssignVisible() {
        const visiblePermissions = getVisiblePermissionIds();
        if (visiblePermissions.length === 0) return;

        if (visiblePermissions.length > 30) {
            if (!confirm(`هل تريد تفعيل ${visiblePermissions.length} صلاحية؟`)) {
                return;
            }
        }

        visiblePermissions.forEach(id => state.assignedDraft.add(id));
        renderPermissions();
        updateSaveButton();
    }

    function handleRevokeVisible() {
        const visiblePermissions = getVisiblePermissionIds();
        if (visiblePermissions.length === 0) return;

        if (visiblePermissions.length > 30) {
            if (!confirm(`هل تريد تعطيل ${visiblePermissions.length} صلاحية؟`)) {
                return;
            }
        }

        visiblePermissions.forEach(id => state.assignedDraft.delete(id));
        renderPermissions();
        updateSaveButton();
    }

    function getVisiblePermissionIds() {
        const visible = [];
        document.querySelectorAll('.permission-switch').forEach(switchEl => {
            const permissionId = parseInt(switchEl.dataset.permissionId);
            visible.push(permissionId);
        });
        return visible;
    }

    function handleExpandAll() {
        const modules = new Set();
        state.permissions.forEach(perm => modules.add(perm.module));
        modules.forEach(module => state.expandedModules.add(module));
        renderPermissions();
    }

    function handleCollapseAll() {
        state.expandedModules.clear();
        renderPermissions();
    }

    // Save Changes
    async function handleSave() {
        const giveIds = [...state.assignedDraft].filter(id => !state.assignedOriginal.has(id));
        const revokeIds = [...state.assignedOriginal].filter(id => !state.assignedDraft.has(id));

        if (giveIds.length === 0 && revokeIds.length === 0) {
            showToast('لا توجد تغييرات للحفظ', 'error');
            return;
        }

        try {
            elements.saveBtn.disabled = true;
            const originalHTML = elements.saveBtn.innerHTML;
            elements.saveBtn.innerHTML = `
                <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                جاري الحفظ...
            `;

            const response = await fetch(API.sync, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    user_id: state.selectedUserId,
                    give_ids: giveIds,
                    revoke_ids: revokeIds,
                }),
            });

            // Get response data
            let data;
            try {
                const text = await response.text();
                data = text ? JSON.parse(text) : {};
            } catch (e) {
                throw new Error('فشل في قراءة استجابة السيرفر');
            }

            // Check if response is ok
            if (!response.ok || !data.ok) {
                let errorMessage = 'حدث خطأ أثناء حفظ الصلاحيات';
                
                // Get error message from response
                if (data.message) {
                    errorMessage = data.message;
                } else if (data.errors) {
                    // Handle validation errors
                    const firstError = Object.values(data.errors)[0];
                    errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                } else if (!response.ok) {
                    errorMessage = `خطأ ${response.status}: ${response.statusText || 'فشل الحفظ'}`;
                }
                
                throw new Error(errorMessage);
            }

            // Success
            state.assignedOriginal = new Set(state.assignedDraft);
            updateSaveButton();
            
            // Build success message with details
            let successMessage = 'تم حفظ التغييرات بنجاح';
            const changes = [];
            
            if (giveIds.length > 0) {
                changes.push(`تم تفعيل ${giveIds.length} صلاحية`);
            }
            if (revokeIds.length > 0) {
                changes.push(`تم تعطيل ${revokeIds.length} صلاحية`);
            }
            
            if (changes.length > 0) {
                successMessage += ' - ' + changes.join(' و ');
            }
            
            if (data.assigned_count !== undefined) {
                successMessage += ` (إجمالي الصلاحيات المفعلة: ${data.assigned_count})`;
            }
            
            // Show toast notification
            showToast(successMessage, 'success');
        } catch (error) {
            console.error('Error saving permissions:', error);
            
            // Show detailed error message
            const errorMessage = error.message || 'حدث خطأ غير متوقع أثناء حفظ الصلاحيات';
            showToast(errorMessage, 'error');
        } finally {
            elements.saveBtn.disabled = false;
            elements.saveBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                حفظ التغييرات
            `;
        }
    }

    // Update Save Button
    function updateSaveButton() {
        const hasChanges = !areSetsEqual(state.assignedOriginal, state.assignedDraft);
        elements.saveBtn.disabled = !hasChanges || !state.selectedUserId;
    }

    function areSetsEqual(set1, set2) {
        if (set1.size !== set2.size) return false;
        for (const item of set1) {
            if (!set2.has(item)) return false;
        }
        return true;
    }

    // UI State
    function disableUI() {
        if (elements.permissionSearchInput) {
            elements.permissionSearchInput.disabled = true;
            elements.permissionSearchInput.value = '';
            state.q = '';
        }
        elements.assignVisibleBtn.disabled = true;
        elements.revokeVisibleBtn.disabled = true;
        elements.expandAllBtn.disabled = true;
        elements.collapseAllBtn.disabled = true;
        elements.saveBtn.disabled = true;
    }

    function enableUI() {
        if (elements.permissionSearchInput) {
            elements.permissionSearchInput.disabled = false;
        }
        elements.assignVisibleBtn.disabled = false;
        elements.revokeVisibleBtn.disabled = false;
        elements.expandAllBtn.disabled = false;
        elements.collapseAllBtn.disabled = false;
    }

    // Toast
    function showToast(message, type = 'success') {
        // Ensure toast container exists
        let toastContainer = elements.toastContainer || document.getElementById('toast-container');
        
        if (!toastContainer) {
            // Create toast container if it doesn't exist
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.setAttribute('style', 'position: fixed !important; bottom: 1rem !important; left: 1rem !important; right: 1rem !important; z-index: 99999 !important; display: flex !important; flex-direction: column !important; gap: 0.5rem !important; align-items: flex-end !important; pointer-events: none !important;');
            document.body.appendChild(toastContainer);
        }
        
        elements.toastContainer = toastContainer;

        const bgColor = type === 'success' ? 'bg-emerald-500' : 'bg-rose-500';
        const icon = type === 'success' 
            ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
            : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        
        const toast = document.createElement('div');
        toast.className = `${bgColor} text-white px-6 py-4 rounded-xl shadow-xl flex items-start gap-3 max-w-md`;
        // Set all styles inline - use margin-top instead of position relative
        const bgColorValue = type === 'success' ? '#10b981' : '#ef4444';
        toast.setAttribute('style', `
            opacity: 1 !important;
            transform: translateY(0) !important;
            transition: all 0.3s ease !important;
            position: static !important;
            z-index: 100000 !important;
            display: flex !important;
            visibility: visible !important;
            pointer-events: auto !important;
            background-color: ${bgColorValue} !important;
            color: white !important;
            padding: 1rem 1.5rem !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            margin-top: 0.5rem !important;
            width: auto !important;
            max-width: 28rem !important;
        `);
        toast.innerHTML = `
            <div class="flex-shrink-0 mt-0.5">
                ${icon}
            </div>
            <div class="flex-1">
                <p class="font-medium">${type === 'success' ? 'نجح الحفظ' : 'خطأ في الحفظ'}</p>
                <p class="text-sm mt-1 opacity-95">${message}</p>
            </div>
            <button class="flex-shrink-0 text-white hover:text-gray-200 transition-colors" onclick="this.closest('div').remove()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        
        toastContainer.appendChild(toast);

        // Auto remove after delay
        const delay = type === 'success' ? 5000 : 6000;
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 300);
        }, delay);
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

