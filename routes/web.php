<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController, CompanyController, ChamberController, 
    LicenseController, ImporterRecordController, CommercialRegisterController, 
    CompanyDocumentController, FinanceController, AssetController, 
    RoleAssignmentController, ReportController
};

/*
|--------------------------------------------------------------------------
| المسارات العامة
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| المسارات المحمية
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. لوحة التحكم
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // 2. التقارير (Admin فقط)
    Route::get('/admin/reports', [ReportController::class, 'index'])
        ->middleware('role:admin')
        ->name('reports.index');

    // 3. إدارة الشركات (صلاحية manage_companies)
    Route::middleware('permission:manage_companies')->group(function () {
        Route::resource('companies', CompanyController::class);
        Route::resource('commercial-registers', CommercialRegisterController::class);
        Route::resource('chambers', ChamberController::class);
        Route::resource('licenses', LicenseController::class);
        Route::resource('importers', ImporterRecordController::class);

        Route::get('commercial-registers/{id}/renew', [CommercialRegisterController::class, 'renew'])->name('commercial-registers.renew');
        Route::put('commercial-registers/{id}/update-renew', [CommercialRegisterController::class, 'updateRenew'])->name('commercial-registers.updateRenew');
        Route::get('chambers/{id}/renew', [ChamberController::class, 'renew'])->name('chambers.renew');
        Route::put('chambers/{id}/renew-update', [ChamberController::class, 'renewUpdate'])->name('chambers.renewUpdate');
        Route::get('licenses/{id}/renew', [LicenseController::class, 'renew'])->name('licenses.renew');
        Route::put('licenses/{id}/renew-update', [LicenseController::class, 'renewUpdate'])->name('licenses.renewUpdate');
        Route::get('importers/{id}/renew', [ImporterRecordController::class, 'renew'])->name('importers.renew');
        Route::put('importers/{id}/renew', [ImporterRecordController::class, 'updateRenew'])->name('importers.update_renew');
    });

    // 4. أرشيف المستندات
    Route::prefix('company-archives')->group(function () {
        Route::get('/', [CompanyDocumentController::class, 'index'])->name('companies.CompanyDocument.index');
        Route::get('/{id}/documents', [CompanyDocumentController::class, 'getCompanyDocuments']);
        Route::post('/{id}/documents/upload', [CompanyDocumentController::class, 'store'])->middleware('permission:upload_docs');
        Route::delete('/{companyId}/documents/{documentId}', [CompanyDocumentController::class, 'destroy'])->middleware('role:admin');
    });

    // 5. النظام المالي
    Route::middleware('role:admin|accountant')->prefix('finance')->group(function () {
        Route::get('/companies', [FinanceController::class, 'index'])->name('finance.index');
        Route::get('/create/{company_id}', [FinanceController::class, 'create'])->name('finance.create');
        Route::post('/store', [FinanceController::class, 'store'])->name('finance.store');
        Route::get('/show/{company_id}', [FinanceController::class, 'show'])->name('finance.show');
        Route::post('/update-payment/{id}', [FinanceController::class, 'updatePayment'])->name('finance.update_payment');
        Route::get('/print/{id}', [FinanceController::class, 'printInvoice'])->name('finance.print');
    });

    // 6. إدارة الأصول
    Route::middleware('can:view assets')->prefix('property-list')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('assets.index');
        Route::get('/dashboard', [AssetController::class, 'dashboard'])->name('assets.dashboard');
        Route::get('/damaged', [AssetController::class, 'damaged'])->name('assets.damaged');
        Route::get('/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/store', [AssetController::class, 'store'])->name('assets.store');
        Route::get('/edit/{id}', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('/update/{id}', [AssetController::class, 'update'])->name('assets.update');
        Route::get('/show/{id}', [AssetController::class, 'show'])->name('assets.show');
        Route::post('/restore/{id}', [AssetController::class, 'restore'])->name('assets.restore');
        Route::get('/maintenance-logs', [AssetController::class, 'maintenanceLogs'])->name('assets.maintenance_logs');
        Route::post('/{id}/maintenance', [AssetController::class, 'sendToMaintenance'])->name('assets.maintenance');
        Route::post('/{id}/complete-maintenance', [AssetController::class, 'completeMaintenance'])->name('assets.complete-maintenance');
        Route::patch('/{id}/move-to-damaged', [AssetController::class, 'moveToDamaged'])->name('assets.move_to_damaged');
        Route::post('/{id}/confirm-receipt', [AssetController::class, 'confirmReceipt'])->name('assets.confirm-receipt');
    });

    // 7. إدارة الصلاحيات والأدوار (Admin فقط)
    Route::middleware('role:admin|super-admin')->prefix('manage-roles')->group(function () {
        Route::get('/', [RoleAssignmentController::class, 'index'])->name('roles.index');
        Route::post('/store-employee', [RoleAssignmentController::class, 'store'])->name('roles.store');
        Route::post('/update/{user}', [RoleAssignmentController::class, 'update'])->name('roles.update');
        Route::post('/new-role', [RoleAssignmentController::class, 'storeRole'])->name('roles.store_new');
        Route::post('/update-permission', [RoleAssignmentController::class, 'updatePermission'])->name('roles.update_permission');
    });

    // 8. الملف الشخصي
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::post('/upload-temp', [CompanyController::class, 'uploadTempFile'])->name('upload.temp');
});

      
Route::post('/roles/update-permission', [RoleAssignmentController::class, 'updatePermission'])->name('roles.update_permission');

// قم بتغيير السطر الحالي ليصبح هكذا:
Route::post('/companies/{company}/documents/upload', [CompanyDocumentController::class, 'store'])
    ->name('companies.documents.upload');
    Route::get('/company-archives', [CompanyDocumentController::class, 'index'])
    ->name('companies.CompanyDocument.index');

// 2. جلب قائمة المستندات لشركة معينة (JSON - يحتاجه الـ JavaScript)
Route::get('/companies/{id}/documents', [CompanyDocumentController::class, 'getCompanyDocuments'])
    ->name('companies.documents.get');

// 3. رفع ملف جديد (Store)
Route::post('/companies/{id}/documents/upload', [CompanyDocumentController::class, 'store'])
    ->name('companies.documents.upload');

// 4. حذف ملف (Destroy)
Route::delete('/companies/{companyId}/documents/{documentId}', [CompanyDocumentController::class, 'destroy'])
    ->name('companies.documents.destroy');