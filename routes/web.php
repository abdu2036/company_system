<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController; 
use App\Http\Controllers\ChamberController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\ImporterRecordController;
use App\Http\Controllers\CommercialRegisterController;
use App\Http\Controllers\CompanyDocumentController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StatisticalRegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndustrialRegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes - روابط المنظومة العامة والتحويلات
|--------------------------------------------------------------------------
*/

// بدلاً من عرض صفحة welcome، قم بالتحويل لصفحة login تلقائياً
Route::get('/', function () {
    return redirect()->route('login')->with('status', 'تم تسجيل الخروج بنجاح. نراك لاحقاً!');
});

// مسار رفع الملفات المؤقتة العامة (AJAX)
Route::post('/upload-temp', [CompanyController::class, 'uploadTempFile']);

require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| Protected Routes - المسارات المحمية بتسجيل الدخول (Auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. لوحة التحكم (Dashboard) والتقرير الرئيسي
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');


    // 2. مجموعة مسارات "الرمز الإحصائي الجديد" (يجب أن تظل في الأعلى)
    Route::get('companies/statistical/create', [StatisticalRegisterController::class, 'create'])->name('statistical.create');
    Route::post('companies/statistical/store', [StatisticalRegisterController::class, 'store'])->name('statistical.store');
    Route::get('companies/statistical', [StatisticalRegisterController::class, 'index'])->name('statistical.index');
    Route::get('companies/statistical/{id}/edit', [StatisticalRegisterController::class, 'edit'])->name('statistical.edit');
    Route::put('companies/statistical/{id}/update', [StatisticalRegisterController::class, 'update'])->name('statistical.update');
    Route::get('companies/statistical/{id}/renew', [StatisticalRegisterController::class, 'renew'])->name('statistical.renew');
    Route::post('companies/statistical/{id}/renew', [StatisticalRegisterController::class, 'processRenew'])->name('statistical.processRenew');
    Route::delete('companies/statistical/{id}', [StatisticalRegisterController::class, 'destroy'])->name('statistical.destroy');

// 3. مجموعة مسارات "السجل الصناعي" (تم نقلها هنا للأعلى لمنع التداخل مع الـ Resource)
    Route::get('companies/industrial/create', [IndustrialRegisterController::class, 'create'])->name('industrial.create');
    Route::post('companies/industrial/store', [IndustrialRegisterController::class, 'store'])->name('industrial.store');
    Route::get('companies/industrial', [IndustrialRegisterController::class, 'index'])->name('industrial.index');
    Route::get('companies/industrial/{id}/edit', [IndustrialRegisterController::class, 'edit'])->name('industrial.edit');
    Route::put('companies/industrial/{id}/update', [IndustrialRegisterController::class, 'update'])->name('industrial.update');
    Route::get('companies/industrial/{id}/renew', [IndustrialRegisterController::class, 'renew'])->name('industrial.renew');
    Route::post('companies/industrial/{id}/renew', [IndustrialRegisterController::class, 'processRenew'])->name('industrial.processRenew');
    Route::delete('companies/industrial/{id}', [IndustrialRegisterController::class, 'destroy'])->name('industrial.destroy');
    // 3. مسارات إضافية لإنشاء السجلات الفرعية (تمنع التداخل)
    Route::get('/commercial-registers/create', [CommercialRegisterController::class, 'create'])->name('commercial-registers.create');
    Route::get('/licenses/create', [LicenseController::class, 'create'])->name('licenses.create');
    Route::get('/chambers/create', [ChamberController::class, 'create'])->name('chambers.create');
    Route::get('/importers/create', [ImporterRecordController::class, 'create'])->name('importers.create');


    // 4. مسارات التجديد (Renew) للسجلات الأخرى (توضع قبل الـ Resource الخاص بها)
    Route::get('commercial-registers/{id}/renew', [CommercialRegisterController::class, 'renew'])->name('commercial-registers.renew');
    Route::put('commercial-registers/{id}/update-renew', [CommercialRegisterController::class, 'updateRenew'])->name('commercial-registers.updateRenew');

    Route::get('chambers/{id}/renew', [ChamberController::class, 'renew'])->name('chambers.renew');
    Route::put('chambers/{id}/renew-update', [ChamberController::class, 'renewUpdate'])->name('chambers.renewUpdate');

    Route::get('licenses/{id}/renew', [LicenseController::class, 'renew'])->name('licenses.renew');
    Route::put('licenses/{id}/renew-update', [LicenseController::class, 'renewUpdate'])->name('licenses.renewUpdate');

    Route::get('importers/{id}/renew', [ImporterRecordController::class, 'renew'])->name('importers.renew');
    Route::put('importers/{id}/renew', [ImporterRecordController::class, 'updateRenew'])->name('importers.update_renew');


    // 5. روابط الـ Resource الأساسية للنظام بالكامل (مجمعة ومنظفة بدون تكرار)
    Route::resource('companies', CompanyController::class);
    Route::resource('commercial-registers', CommercialRegisterController::class);
    Route::resource('chambers', ChamberController::class);
    Route::resource('licenses', LicenseController::class);
    Route::resource('importers', ImporterRecordController::class);


    // 6. مسارات الخدمات الإضافية داخل الكومباني (الربط القديم)
    Route::get('/licenses-list', [CompanyController::class, 'showLicenses'])->name('companies.licenses');
    Route::get('/chambers-list', [CompanyController::class, 'showChambers'])->name('companies.chambers');
    Route::get('/importers-list', [CompanyController::class, 'showImporters'])->name('companies.importers');


    // 7. أرشيف مستندات الشركة (Company Documents)
    Route::get('/company-archives', [CompanyDocumentController::class, 'index'])->name('companies.CompanyDocument.index');
    Route::get('/companies/{id}/documents', [CompanyDocumentController::class, 'getCompanyDocuments']);
    Route::post('/companies/{id}/documents/upload', [CompanyDocumentController::class, 'store']);
    Route::delete('/companies/{id}/documents/{documentId}', [CompanyDocumentController::class, 'destroy']);


    // 8. مسارات النظام المالي للشركات (Finance)
    Route::prefix('finance')->group(function () {
        Route::get('/companies', [FinanceController::class, 'index'])->name('finance.index');
        Route::get('/create/{company_id}', [FinanceController::class, 'create'])->name('finance.create');
        Route::post('/store', [FinanceController::class, 'store'])->name('finance.store');
        Route::get('/show/{company_id}', [FinanceController::class, 'show'])->name('finance.show');
        Route::post('/update-payment/{id}', [FinanceController::class, 'updatePayment'])->name('finance.update_payment');
        Route::get('/print/{id}', [FinanceController::class, 'printInvoice'])->name('finance.print');
    });


    // 9. مسارات الملف الشخصي للموظفين (Profile)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


 
});