<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChamberController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\ImporterRecordController; // تأكد من استدعاء الكنترول
use App\Http\Controllers\CommercialRegisterController;
use App\Http\Controllers\CompanyDocumentController; // تأكد من استدعاء الكنترولر الجديد
/*
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
// مسار عرض صفحة الإضافة
Route::get('companies/create-views-companies', [CompanyController::class, 'create'])->name('companies.create');

// مسار حفظ البيانات (الذي تستخدمه في الفورم)
Route::post('companies/store', [CompanyController::class, 'store'])->name('companies.store');
// 1. الصفحة الرئيسية والداشبورد
Route::get('/', function () {
    return view('welcome');
});
// مسار حذف الشركة وكل ما يتعلق بها
Route::delete('/companies/{id}', [CompanyController::class, 'destroy'])->name('companies.destroy');
Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// 2. مسارات المحمية بتسجيل الدخول
Route::middleware('auth')->group(function () {
    
    // --- مسارات الشركات ---
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies/store', [CompanyController::class, 'store'])->name('companies.store');

    // --- مسارات السجلات التجارية (Commercial Registers) ---
    // العرض
Route::middleware(['auth'])->group(function () {
    
// 1. أضف مسار التجديد أولاً
Route::get('commercial-registers/{id}/renew', [CommercialRegisterController::class, 'renew'])->name('commercial-registers.renew');

// 2. ثم سطر الريسورس الأساسي

    // 1. مسارات السجلات التجارية (تعمل تلقائياً مع CommercialRegisterController)
    Route::resource('commercial-registers', CommercialRegisterController::class);

    // 2. مسارات الشركات
    Route::resource('companies', CompanyController::class);
    Route::get('commercial-registers/{id}/renew', [CommercialRegisterController::class, 'renew'])->name('commercial-registers.renew');
    Route::put('commercial-registers/{id}/update-renew', [CommercialRegisterController::class, 'updateRenew'])->name('commercial-registers.updateRenew');
    // 2. مسار رفع الملفات المؤقتة (تأكد من كتابة اسم الكنترولر الصحيح هنا)
    Route::post('/upload-temp', [CommercialRegisterController::class, 'uploadTemp'])->name('upload.temp');
});
    // --- مسارات التراخيص والخدمات الأخرى ---
    Route::get('/licenses', [CompanyController::class, 'showLicenses'])->name('companies.licenses');
    Route::get('/chambers', [CompanyController::class, 'showChambers'])->name('companies.chambers');
    Route::get('/importers', [CompanyController::class, 'showImporters'])->name('companies.importers');

    // مسار رفع الملفات المؤقت (AJAX)
    Route::post('/upload-temp', [CompanyController::class, 'uploadTempFile']);

    // مسارات الملف الشخصي (اختياري إذا كنت تستخدم Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // مسارات الغرفة التجارية (Chamber of Commerce)
    

});
Route::middleware('auth')->group(function () {
    // أضف هذا السطر أولاً
Route::get('chambers/{id}/renew', [ChamberController::class, 'renew'])->name('chambers.renew');

// ثم سطر الريسورس الموجود عندك
Route::resource('chambers', ChamberController::class);
    // ... المسارات الأخرى (مثل الشركات) ...

    // هذا السطر يغنينا عن كتابة كل المسارات يدوياً
    // وسيبحث تلقائياً عن الدوال بداخل ChamberController
    Route::resource('chambers', ChamberController::class);
    Route::get('chambers/{id}/renew', [ChamberController::class, 'renew'])->name('chambers.renew');
// أضف هذا السطر أيضاً
Route::put('chambers/{id}/renew-update', [ChamberController::class, 'renewUpdate'])->name('chambers.renewUpdate');

Route::resource('chambers', ChamberController::class);
    
});
//قروب الترخيص 
Route::middleware('auth')->group(function () {
    
    // 1. مسارات إضافية للتجديد (Renew) قبل الريسورس
    Route::get('licenses/{id}/renew', [LicenseController::class, 'renew'])->name('licenses.renew');
    Route::put('licenses/{id}/renew-update', [LicenseController::class, 'renewUpdate'])->name('licenses.renewUpdate');

    // 2. مسارات الريسورس الأساسية (index, create, store, edit, update, destroy)
    Route::resource('licenses', LicenseController::class);

});
// قروب سجل المستوردين
Route::middleware(['auth'])->group(function () {
    // مجموعة مسارات سجل المستوردين
    Route::resource('importers', ImporterRecordController::class);
    Route::get('/importers/{id}/renew', [ImporterRecordController::class, 'renew'])->name('importers.renew');
    // يمكنك إضافة المسارات الأخرى هنا لتكون منظمة معاً
    // رابط عرض صفحة التجديد
Route::get('importers/{id}/renew', [ImporterRecordController::class, 'renew'])->name('importers.renew');

// رابط معالجة بيانات التجديد (الذي نستخدمه في الفورم أعلاه)
Route::put('importers/{id}/renew', [ImporterRecordController::class, 'updateRenew']);
// في ملف web.php
Route::put('importers/{id}/renew', [ImporterRecordController::class, 'updateRenew'])->name('importers.update_renew');
    // Route::resource('licenses', LicenseController::class);
});

//مسارات لصفحة العرض الرائسية للشركات
// مسارات السجل التجاري
Route::get('/commercial-registers/create', [CommercialRegisterController::class, 'create'])->name('commercial-registers.create');

// مسارات التراخيص
Route::get('/licenses/create', [LicenseController::class, 'create'])->name('licenses.create');

// مسارات الغرفة التجارية
Route::get('/chambers/create', [ChamberController::class, 'create'])->name('chambers.create');

// مسارات سجل المستوردين
Route::get('/importers/create', [ImporterRecordController::class, 'create'])->name('importers.create');

// مجموعة المسارات المحمية بـ Auth
Route::middleware(['auth'])->group(function () {

    // مسارات الشركات الأساسية
    Route::resource('companies', CompanyController::class);

    // مجموعة مسارات السجل التجاري
    Route::resource('commercial-registers', CommercialRegisterController::class);
    
    // يمكنك إضافة بقية الأقسام هنا مستقبلاً
    // Route::resource('licenses', LicenseController::class);
});
Route::post('/upload-temp', [App\Http\Controllers\LicenseController::class, 'uploadTemp']);
require __DIR__ . '/auth.php';

//كود الكنترولر الخاص بالتقارير
Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');

// بدلاً من عرض صفحة welcome، قم بالتحويل لصفحة login
Route::get('/', function () {
    return redirect()->route('login')->with('status', 'تم تسجيل الخروج بنجاح. نراك لاحقاً!');
});

Route::get('/admin/reports', [App\Http\Controllers\ReportController::class, 'index'])
    ->middleware(['auth'])
    ->name('reports.index');

// مسار عرض الصفحة (الذي يستخدمه السايد بار)
Route::get('/company-archives', [CompanyDocumentController::class, 'index'])->name('companies.CompanyDocument.index');

// مسار جلب البيانات للـ Modal (عبر AJAX)
Route::get('/companies/{id}/documents', [CompanyDocumentController::class, 'getCompanyDocuments']);

// مسار الرفع
Route::post('/companies/{id}/documents/upload', [CompanyDocumentController::class, 'store']);

// مسار الحذف
Route::delete('/companies/{id}/documents/{documentId}', [CompanyDocumentController::class, 'destroy']);