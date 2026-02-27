<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\UserImportController;
use App\Http\Controllers\BeneficiaryImportController;
use App\Http\Controllers\BeneficiaryAuthController;
use App\Http\Controllers\Admin\UserPermissionsController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

// Beneficiary Authentication Routes (Public)
Route::get('/beneficiary/verify', [BeneficiaryAuthController::class, 'showVerificationForm'])->name('beneficiary.verify');
Route::post('/beneficiary/verify', [BeneficiaryAuthController::class, 'verify']);
Route::get('/beneficiary/set-password', [BeneficiaryAuthController::class, 'showSetPasswordForm'])->name('beneficiary.set-password');
Route::post('/beneficiary/set-password', [BeneficiaryAuthController::class, 'setPassword']);
Route::get('/beneficiary/login', [BeneficiaryAuthController::class, 'showLoginForm'])->name('beneficiary.login');
Route::post('/beneficiary/login', [BeneficiaryAuthController::class, 'login']);
Route::post('/beneficiary/logout', [BeneficiaryAuthController::class, 'logout'])->name('beneficiary.logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');
    
    // Beneficiaries
    Route::get('/beneficiaries', [BeneficiaryController::class, 'index'])->middleware('permission:beneficiaries.view')->name('beneficiaries.index');
    Route::post('/beneficiaries/filter', [BeneficiaryController::class, 'applyFilters'])->middleware('permission:beneficiaries.view')->name('beneficiaries.filter');
    Route::post('/beneficiaries/filter/reset', [BeneficiaryController::class, 'resetFilters'])->middleware('permission:beneficiaries.view')->name('beneficiaries.filter.reset');
    Route::get('/beneficiaries/create', [BeneficiaryController::class, 'create'])->middleware('permission:beneficiaries.create')->name('beneficiaries.create');
    Route::post('/beneficiaries', [BeneficiaryController::class, 'store'])->middleware('permission:beneficiaries.create')->name('beneficiaries.store');
    Route::get('/beneficiaries/{beneficiary}', [BeneficiaryController::class, 'show'])->middleware('permission:beneficiaries.view')->name('beneficiaries.show');
    Route::get('/beneficiaries/{beneficiary}/edit', [BeneficiaryController::class, 'edit'])->middleware('permission:beneficiaries.edit')->name('beneficiaries.edit');
    Route::put('/beneficiaries/{beneficiary}', [BeneficiaryController::class, 'update'])->middleware('permission:beneficiaries.edit')->name('beneficiaries.update');
    Route::delete('/beneficiaries/{beneficiary}', [BeneficiaryController::class, 'destroy'])->middleware('permission:beneficiaries.delete')->name('beneficiaries.destroy');
    Route::post('/beneficiaries/{beneficiary}/toggle-status', [BeneficiaryController::class, 'toggleStatus'])->middleware('permission:beneficiaries.toggle_status')->name('beneficiaries.toggle-status');
    Route::get('/beneficiaries/export/excel', [BeneficiaryController::class, 'exportExcel'])->middleware('permission:beneficiaries.export_excel')->name('beneficiaries.export-excel');
    Route::post('/beneficiaries/load-more', [BeneficiaryController::class, 'loadMore'])->middleware('permission:beneficiaries.view')->name('beneficiaries.load-more');
    Route::get('/beneficiaries/search', [BeneficiaryController::class, 'search'])->middleware('permission:beneficiaries.view')->name('beneficiaries.search');
    Route::post('/beneficiaries/get-filtered-ids', [BeneficiaryController::class, 'getFilteredIds'])->middleware('permission:beneficiaries.view')->name('beneficiaries.get-filtered-ids');
    
    // Batches
    Route::get('/batches', [BatchController::class, 'index'])->middleware('permission:batches.view')->name('batches.index');
    Route::post('/batches/filter', [BatchController::class, 'applyFilters'])->middleware('permission:batches.view')->name('batches.filter');
    Route::post('/batches/filter/reset', [BatchController::class, 'resetFilters'])->middleware('permission:batches.view')->name('batches.filter.reset');
    Route::get('/batches/export/excel', [BatchController::class, 'exportExcel'])->middleware('permission:batches.view')->name('batches.export-excel');
    Route::get('/batches/create', [BatchController::class, 'create'])->middleware('permission:batches.create')->name('batches.create');
    Route::post('/batches', [BatchController::class, 'store'])->middleware('permission:batches.create')->name('batches.store');
    Route::get('/batches/{batch}/manage', [BatchController::class, 'manage'])->middleware('permission:batches.manage_beneficiaries')->name('batches.manage');
    Route::get('/batches/{batch}/distribution', [BatchController::class, 'distribution'])->middleware('permission:batches.view')->name('batches.distribution');
    Route::get('/batches/{batch}/distribution/export', [BatchController::class, 'exportDistributionExcel'])->middleware('permission:batches.view')->name('batches.distribution.export-excel');
    Route::put('/batches/{batch}', [BatchController::class, 'update'])->middleware('permission:batches.edit')->name('batches.update');
    Route::delete('/batches/{batch}', [BatchController::class, 'destroy'])->middleware('permission:batches.delete')->name('batches.destroy');
    Route::post('/batches/{batch}/approve', [BatchController::class, 'approve'])->middleware('permission:batches.approve')->name('batches.approve');
    Route::post('/batches/{batch}/add-beneficiaries', [BatchController::class, 'addBeneficiaries'])->middleware('permission:batches.manage_beneficiaries')->name('batches.add-beneficiaries');
    Route::delete('/batches/{batch}/beneficiaries/{beneficiary}', [BatchController::class, 'removeBeneficiary'])->middleware('permission:batches.manage_beneficiaries')->name('batches.remove-beneficiary');
    Route::post('/batches/{batch}/import-excel-preview', [BatchController::class, 'importExcelPreview'])->middleware('permission:batches.import_excel')->name('batches.import-excel-preview');
    Route::post('/batches/{batch}/confirm-excel-import', [BatchController::class, 'confirmExcelImport'])->middleware('permission:batches.import_excel')->name('batches.confirm-excel-import');
    Route::post('/batches/{batch}/recipients/{beneficiary}/toggle-received', [BatchController::class, 'toggleReceived'])->middleware('permission:batches.toggle_received')->name('batches.toggle-received');
    
    // Users
    Route::get('/users', [UserController::class, 'index'])->middleware('permission:users.view')->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->middleware('permission:users.create')->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:users.create')->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->middleware('permission:users.edit')->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:users.edit')->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('users.destroy');
    Route::post('/users/import-preview', [UserImportController::class, 'preview'])->middleware('permission:users.create')->name('users.import-preview');
    Route::post('/users/import', [UserImportController::class, 'import'])->middleware('permission:users.create')->name('users.import');
    
    // Beneficiary Import
    Route::post('/beneficiaries/import-preview', [BeneficiaryImportController::class, 'preview'])->middleware('permission:beneficiaries.create')->name('beneficiaries.import-preview');
    Route::post('/beneficiaries/import', [BeneficiaryImportController::class, 'import'])->middleware('permission:beneficiaries.create')->name('beneficiaries.import');
    
    // Distributions
    Route::get('/distributions', [DistributionController::class, 'index'])->middleware('permission:distributions.view')->name('distributions.index');
    Route::get('/distributions/create', [DistributionController::class, 'create'])->middleware('permission:distributions.create')->name('distributions.create');
    Route::post('/distributions', [DistributionController::class, 'store'])->middleware('permission:distributions.create')->name('distributions.store');
    Route::get('/distributions/{distribution}', [DistributionController::class, 'show'])->middleware('permission:distributions.view')->name('distributions.show');
    Route::delete('/distributions/{distribution}', [DistributionController::class, 'destroy'])->middleware('permission:distributions.delete')->name('distributions.destroy');
    
    // User Permissions Management
    Route::get('/admin/user-permissions', [UserPermissionsController::class, 'index'])->middleware('permission:users.manage_permissions')->name('admin.user-permissions.index');
    Route::get('/admin/api/users', [UserPermissionsController::class, 'apiUsers'])->middleware('permission:users.manage_permissions')->name('admin.api.users');
    Route::get('/admin/api/user-permissions', [UserPermissionsController::class, 'apiUserPermissions'])->middleware('permission:users.manage_permissions')->name('admin.api.user-permissions');
    Route::patch('/admin/api/user-permissions/sync', [UserPermissionsController::class, 'sync'])->middleware('permission:users.manage_permissions')->name('admin.api.user-permissions.sync');
});

// Beneficiary Protected Routes
Route::middleware([\App\Http\Middleware\BeneficiaryAuth::class])->group(function () {
    Route::get('/beneficiary/dashboard', function() {
        $beneficiary = BeneficiaryAuthController::getCurrentBeneficiary();
        if (!$beneficiary) {
            return redirect()->route('beneficiary.verify');
        }
        return view('beneficiary.dashboard', compact('beneficiary'));
    })->name('beneficiary.dashboard');
    
    Route::get('/beneficiary/profile', function() {
        $beneficiary = BeneficiaryAuthController::getCurrentBeneficiary();
        if (!$beneficiary) {
            return redirect()->route('beneficiary.verify');
        }
        $beneficiary->load('familyMembers', 'batches');
        return view('beneficiary.profile', compact('beneficiary'));
    })->name('beneficiary.profile');
    
    Route::put('/beneficiary/profile', [BeneficiaryController::class, 'updateProfile'])->name('beneficiary.profile.update');
});

// Redirect root to dashboard if authenticated, otherwise to login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    if (session()->has('beneficiary_id')) {
        return redirect()->route('beneficiary.dashboard');
    }
    return redirect()->route('login');
});

// Dev-only preview (bypass auth) for quick local checking of beneficiaries page
if (app()->environment('local')) {
    Route::get('/dev/preview-beneficiaries', function (\Illuminate\Http\Request $request) {
        $request->merge(['per_page' => $request->get('per_page', 100)]);
        return app(App\Http\Controllers\BeneficiaryController::class)->index($request);
    });
}
