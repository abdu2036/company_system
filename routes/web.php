<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController, CompanyController, ChamberController, 
    LicenseController, ImporterRecordController, CommercialRegisterController, 
    CompanyDocumentController, FinanceController, AssetController, 
    RoleAssignmentController, ReportController, CompanyExpenseController,  
};

// Ensure RevenueController is explicitly imported to avoid undefined type errors
use App\Http\Controllers\RevenueController;
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
/*
|--------------------------------------------------------------------------
| 1 & 2. لوحة التحكم والتقارير العامة (تأمين دقيق بالصلاحيات)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // 📊 1. لوحة التحكم الرئيسية (متاحة لكل مستخدم مسجل ومفعل في المنظومة)
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // 📈 2. لوحة التقارير والإحصائيات العامة (محمية بصلاحية عرض التقارير)
    Route::middleware('permission:reports.view')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });
// نهاية مجموعة المسارات العامة
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
/*
|--------------------------------------------------------------------------
| إدارة الشركات والوثائق القانونية (روابط مباشرة وحماية دقيقة بالصلاحيات)
|--------------------------------------------------------------------------
*/ 
//1 امورة تمام 
Route::middleware(['auth'])->group(function () {

    // 👁️ 1. صلاحيات العرض والقراءة (المسار المباشر: companies)
    Route::middleware('permission:companies.view')->group(function () {
        Route::resource('companies', CompanyController::class)->only(['index',]);
        Route::resource('commercial-registers', CommercialRegisterController::class)->only(['index', 'show']);
        Route::resource('chambers', ChamberController::class)->only(['index', ]);
        Route::resource('licenses', LicenseController::class)->only(['index',]);
        Route::resource('importers', ImporterRecordController::class)->only(['index',]);
    });

    // ➕ 2. صلاحيات الإنشاء والإضافة (المسار المباشر: companies/create)
    Route::middleware('permission:companies.create')->group(function () {
        Route::resource('companies', CompanyController::class)->only(['create', 'store']);
        Route::resource('commercial-registers', CommercialRegisterController::class)->only(['create', 'store']);
        Route::resource('chambers', ChamberController::class)->only(['create', 'store']);
        Route::resource('licenses', LicenseController::class)->only(['create', 'store']);
        Route::resource('importers', ImporterRecordController::class)->only(['create', 'store']);
    });

    // 📝 3. صلاحيات التعديل والتحديث وتجديد التواريخ القانونية
    Route::middleware('permission:companies.edit')->group(function () {
        Route::resource('companies', CompanyController::class)->only(['edit', 'update']);
        Route::resource('commercial-registers', CommercialRegisterController::class)->only(['edit', 'update']);
        Route::resource('chambers', ChamberController::class)->only(['edit', 'update']);
        Route::resource('licenses', LicenseController::class)->only(['edit', 'update']);
        Route::resource('importers', ImporterRecordController::class)->only(['edit', 'update']);

        // مسارات التجديد والصيانة المباشرة
        Route::get('commercial-registers/{id}/renew', [CommercialRegisterController::class, 'renew'])->name('commercial-registers.renew');
        Route::put('commercial-registers/{id}/update-renew', [CommercialRegisterController::class, 'updateRenew'])->name('commercial-registers.updateRenew');
        
        Route::get('chambers/{id}/renew', [ChamberController::class, 'renew'])->name('chambers.renew');
        Route::put('chambers/{id}/renew-update', [ChamberController::class, 'renewUpdate'])->name('chambers.renewUpdate');
        
        Route::get('licenses/{id}/renew', [LicenseController::class, 'renew'])->name('licenses.renew');
        Route::put('licenses/{id}/renew-update', [LicenseController::class, 'renewUpdate'])->name('licenses.renewUpdate');
        
        Route::get('importers/{id}/renew', [ImporterRecordController::class, 'renew'])->name('importers.renew');
        Route::put('importers/{id}/renew', [ImporterRecordController::class, 'updateRenew'])->name('importers.update_renew');
    });

    // ❌ 4. صلاحيات الحذف النهائي
    Route::middleware('permission:companies.delete')->group(function () {
        Route::resource('companies', CompanyController::class)->only(['destroy']);
        Route::resource('commercial-registers', CommercialRegisterController::class)->only(['destroy']);
        Route::resource('chambers', ChamberController::class)->only(['destroy']);
        Route::resource('licenses', LicenseController::class)->only(['destroy']);
        Route::resource('importers', ImporterRecordController::class)->only(['destroy']);
    });
}); // نهاية مجموعة مسارات الشركات والوثائق القانونية

    // 4. أرشيف المستندات
    Route::prefix('company-archives')->group(function () {
        Route::get('/', [CompanyDocumentController::class, 'index'])->name('companies.CompanyDocument.index');
        Route::get('/{id}/documents', [CompanyDocumentController::class, 'getCompanyDocuments']);
        Route::post('/{id}/documents/upload', [CompanyDocumentController::class, 'store'])->middleware('permission:upload_docs');
        Route::delete('/{companyId}/documents/{documentId}', [CompanyDocumentController::class, 'destroy'])->middleware('role:admin');
    });
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
/*
|--------------------------------------------------------------------------
| 5. النظام المالي (تأمين دقيق ومحمي بالصلاحيات للإيرادات والمصروفات)
|--------------------------------------------------------------------------
*/
//2- امورة تمام
Route::middleware(['auth'])->prefix('finance')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | الفواتير والمدفوعات العامة (المالية)
    |--------------------------------------------------------------------------
    */
    // 👁️ عرض الفواتير والشركات مالياً
    Route::middleware('permission:financial_reports.view')->group(function () {
        Route::get('/companies', [FinanceController::class, 'index'])->name('finance.index');
        Route::get('/show/{company_id}', [FinanceController::class, 'show'])->name('finance.show');
    });

    // ➕ إنشاء وإدارة عمليات الدفع والإنشاء
    Route::middleware('permission:salaries.create')->group(function () { // أو صلاحية مالية مخصصة للإنشاء إذا توفرت
        Route::get('/create/{company_id}', [FinanceController::class, 'create'])->name('finance.create');
        Route::post('/store', [FinanceController::class, 'store'])->name('finance.store');
    });

    // 📝 تحديث عمليات الدفع
    Route::middleware('permission:salaries.edit')->group(function () {
        Route::post('/update-payment/{id}', [FinanceController::class, 'updatePayment'])->name('finance.update_payment');
    });

    // 🖨️ طباعة الفواتير المالية
    Route::middleware('permission:financial_reports.print')->group(function () {
        Route::get('/print/{id}', [FinanceController::class, 'printInvoice'])->name('finance.print');
    });


    /*
    |--------------------------------------------------------------------------
    | 📊 منظومة إدارة المصروفات التشغيلية
    |--------------------------------------------------------------------------
    */
    Route::prefix('expenses')->group(function () {
        
        // 👁️ عرض المصروفات والسجلات التاريخية
        Route::middleware('permission:expenses.view')->group(function () {
            Route::get('/', [CompanyExpenseController::class, 'index'])->name('expenses.index');
            Route::get('/history/{company_id}', [CompanyExpenseController::class, 'history'])->name('expenses.history');
        });

        // ➕ إضافة مصروفات جديدة (فردية، متعددة، وتصنيفات)
        Route::middleware('permission:expenses.create')->group(function () {
            Route::get('/create', [CompanyExpenseController::class, 'create'])->name('expenses.create');
            Route::post('/store', [CompanyExpenseController::class, 'storeExpense'])->name('expenses.store');
            Route::post('/categories/store', [CompanyExpenseController::class, 'storeCategory'])->name('expenses.categories.store');
            Route::post('/companies/expenses/store-multiple', [CompanyExpenseController::class, 'storeMultiple'])->name('expenses.store_multiple');
        });

        // ❌ حذف المصروفات التشغيلية
        Route::middleware('permission:expenses.delete')->group(function () {
            Route::delete('/companies/expenses/{id}', [CompanyExpenseController::class, 'destroy'])->name('expenses.destroy');
        });
    });


    /*
    |--------------------------------------------------------------------------
    | 💰 منظومة إدارة الإيرادات التشغيلية
    |--------------------------------------------------------------------------
    */
    Route::prefix('revenues')->group(function () {
        
        // 👁️ عرض الإيرادات والسجلات التاريخية
        Route::middleware('permission:revenues.view')->group(function () {
            Route::get('/', [RevenueController::class, 'index'])->name('revenues.index');
            Route::get('/history/{company_id}', [RevenueController::class, 'history'])->name('revenues.history');
        });

        // ➕ إضافة إيرادات جديدة (فردية، متعددة، وتصنيفات)
        Route::middleware('permission:revenues.create')->group(function () {
            Route::get('/create', [RevenueController::class, 'create'])->name('revenues.create');
            Route::post('/store', [RevenueController::class, 'storeRevenue'])->name('revenues.store');
            Route::post('/categories/store', [RevenueController::class, 'storeCategory'])->name('revenues.categories.store');
            Route::post('/companies/revenues/store-multiple', [RevenueController::class, 'storeMultiple'])->name('revenues.store_multiple');
        });

        // ❌ حذف الإيرادات التشغيلية
        Route::middleware('permission:revenues.delete')->group(function () {
            Route::delete('/companies/revenues/{id}', [RevenueController::class, 'destroy'])->name('revenues.destroy');
        });
    });
    // 👁️ عرض الفواتير والشركات مالياً + لوحة التقارير المالية والإحصائية
    Route::middleware('permission:financial_reports.view')->group(function () {
        Route::get('/companies', [FinanceController::class, 'index'])->name('finance.index');
        Route::get('/show/{company_id}', [FinanceController::class, 'show'])->name('finance.show');
        
        // ✨ تم التعديل هنا ليرتبط بـ ReportController الجديد
        Route::get('/financial-reports', [ReportController::class, 'financialReports'])->name('reports.financial');
    });
    // 📅 تقرير الأرباح والخسائر السنوي (حماية مخصصة)
Route::middleware('permission:financial_reports.view')->group(function () {
    Route::get('/annual-report', [ReportController::class, 'annualReport'])->name('reports.annual');
});

// 🖨️ التقارير المالية والطباعة
Route::middleware('permission:financial_reports.print')->group(function () {
    // طباعة الفواتير المالية
    Route::get('/print/{id}', [FinanceController::class, 'printInvoice'])->name('finance.print');
    
    // تقرير الأرباح والخسائر السنوي
    Route::get('/annual-report', [ReportController::class, 'annualReport'])->name('reports.annual');
});

}); // نهاية مجموعة المسارات المالية
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------


/*
|--------------------------------------------------------------------------
| 6. منظومة إدارة الأصول وعهد المخازن (تأمين دقيق ومقسم بالصلاحيات)
|--------------------------------------------------------------------------
*/ 
//3- امورة تمام
Route::middleware(['auth'])->prefix('property-list')->group(function () {

    // 👁️ أولاً: صلاحيات العرض والقراءة (لوحة التحكم، الجداول، السجلات، والتفاصيل)
    Route::middleware('permission:warehouses.view')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('assets.index');
        Route::get('/dashboard', [AssetController::class, 'dashboard'])->name('assets.dashboard');
        Route::get('/damaged', [AssetController::class, 'damaged'])->name('assets.damaged');
        Route::get('/show/{id}', [AssetController::class, 'show'])->name('assets.show');
        Route::get('/maintenance-logs', [AssetController::class, 'maintenanceLogs'])->name('assets.maintenance_logs');
    });

    // ➕ ثانياً: صلاحيات الإنشاء والإضافة للأصول والعهد الجديدة
    Route::middleware('permission:warehouses.create')->group(function () {
        Route::get('/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/store', [AssetController::class, 'store'])->name('assets.store');
    });

    // 📝 ثالثاً: صلاحيات التعديل، التحديث، وإعادة الأصول المسترجعة
    Route::middleware('permission:warehouses.edit')->group(function () {
        Route::get('/edit/{id}', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('/update/{id}', [AssetController::class, 'update'])->name('assets.update');
        Route::post('/restore/{id}', [AssetController::class, 'restore'])->name('assets.restore');
    });

    // 🔄 رابعاً: عمليات النقل، الصيانة، تغيير الحالة، وتأكيد الاستلام
    Route::middleware('permission:warehouses.transfer')->group(function () {
        Route::post('/{id}/maintenance', [AssetController::class, 'sendToMaintenance'])->name('assets.maintenance');
        Route::post('/{id}/complete-maintenance', [AssetController::class, 'completeMaintenance'])->name('assets.complete-maintenance');
        Route::patch('/{id}/move-to-damaged', [AssetController::class, 'moveToDamaged'])->name('assets.move_to_damaged');
        Route::post('/{id}/confirm-receipt', [AssetController::class, 'confirmReceipt'])->name('assets.confirm-receipt');
    });
});// نهاية مجموعة مسارات الأصول وعهد المخازن
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
/*


|--------------------------------------------------------------------------
| 7. إدارة الصلاحيات، الأدوار، وتعديل مستخدمي النظام (تأمين صارم)
|--------------------------------------------------------------------------
*/
//4- امورة تمام
Route::middleware(['auth'])->prefix('manage-roles')->group(function () {

    // 👁️ أولاً: صلاحية عرض لوحة التحكم والأدوار الحالية
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('/', [RoleAssignmentController::class, 'index'])->name('roles.index');
    });

    // ➕ ثانياً: صلاحية إنشاء أدوار جديدة أو ربط موظف لأول مرة
    Route::middleware('permission:roles.create')->group(function () {
        Route::post('/store-employee', [RoleAssignmentController::class, 'store'])->name('roles.store');
        Route::post('/new-role', [RoleAssignmentController::class, 'storeRole'])->name('roles.store_new');
    });

    // 📝 ثالثاً: صلاحية تعديل الأدوار، تحديث صلاحيات الـ Checkboxes، وتعديل الموظفين
    Route::middleware('permission:roles.edit')->group(function () {
        Route::post('/update/{user}', [RoleAssignmentController::class, 'update'])->name('roles.update');
        Route::post('/update-permission', [RoleAssignmentController::class, 'updatePermission'])->name('roles.update_permission');
    });
});// نهاية مجموعة مسارات إدارة الأدوار والصلاحيات
//----------------------------- نهاية المسارات المحمية -------------------------------------


    // 8. الملف الشخصي
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::post('/upload-temp', [CompanyController::class, 'uploadTempFile'])->name('upload.temp');
});

      
/*
|--------------------------------------------------------------------------
| 4. أرشيف مستندات الشركات (تنظيف التكرار وحماية الصلاحيات)
|--------------------------------------------------------------------------
*/
//5- امورة تمام
Route::middleware(['auth'])->group(function () {

    // 👁️ أولاً: صلاحية عرض الأرشيف وجلب قائمة المستندات (متاح لمن يملك صلاحية عرض الشركات)
    Route::middleware('permission:companies.view')->group(function () {
        // عرض صفحة الأرشيف الرئيسية
        Route::get('/company-archives', [CompanyDocumentController::class, 'index'])
            ->name('companies.CompanyDocument.index');

        // جلب قائمة المستندات لشركة معينة بصيغة JSON (الذي يحتاجه الـ JavaScript في الواجهة)
        Route::get('/companies/{id}/documents', [CompanyDocumentController::class, 'getCompanyDocuments'])
            ->name('companies.documents.get');
    });

    // ➕ ثانياً: صلاحية رفع ملف أو مستند جديد للشركة
    Route::middleware('permission:companies.create')->group(function () {
        Route::post('/companies/{id}/documents/upload', [CompanyDocumentController::class, 'store'])
            ->name('companies.documents.upload');
    });

    // ❌ ثالثاً: صلاحية حذف مستند رسمي أو ملف من الأرشيف
    Route::middleware('permission:companies.delete')->group(function () {
        Route::delete('/companies/{companyId}/documents/{documentId}', [CompanyDocumentController::class, 'destroy'])
            ->name('companies.documents.destroy');
    });
});// نهاية مجموعة مسارات أرشيف مستندات الشركات
//----------------------------- نهاية المسارات المحمية --------------------------------------------------------------------------------


/*
|--------------------------------------------------------------------------
| مسارات إدارة وهندسة الأدوار المتقدمة (تأمين كامل ومحكم بالصلاحيات)
|--------------------------------------------------------------------------
*/
//6- امورة تمام
Route::middleware(['auth'])->group(function () {

    // 👁️ 1. صلاحية عرض الأدوار المسجلة في النظام
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('/roles', [RoleAssignmentController::class, 'index'])->name('roles.index');
    });

    // ➕ 2. صلاحية إنشاء دور جديد بالكامل في النظام
    Route::middleware('permission:roles.create')->group(function () {
        Route::get('/roles/create', [RoleAssignmentController::class, 'createRole'])->name('roles.create');
        Route::post('/roles/store', [RoleAssignmentController::class, 'storeRole'])->name('roles.store');
        
        // استيراد وربط الموظفين القادمين من منظومة HRMS لأول مرة
        Route::post('/roles/store-employee', [RoleAssignmentController::class, 'store'])->name('roles.store_employee');
    });

    // 📝 3. صلاحية تعديل الأدوار، تحديث مصفوفة الـ Checkboxes، وتحديث بيانات موظف
    Route::middleware('permission:roles.edit')->group(function () {
        Route::get('/roles/{id}/edit', [RoleAssignmentController::class, 'editRole'])->name('roles.edit');
        Route::put('/roles/{id}/update', [RoleAssignmentController::class, 'updateRole'])->name('roles.update');
        
        // تحديث الصلاحيات الفردية وتحديث بيانات ربط الموظفين
        Route::post('/roles/update-employee/{userId}', [RoleAssignmentController::class, 'update'])->name('roles.update_employee');
        Route::post('/roles/update-permission', [RoleAssignmentController::class, 'updatePermission'])->name('roles.update_permission');
    });

    // ❌ 4. صلاحية حذف الدور نهائياً من النظام
    Route::middleware('permission:roles.delete')->group(function () {
        Route::delete('/roles/{id}/destroy', [RoleAssignmentController::class, 'destroyRole'])->name('roles.destroy');
    });
});// نهاية مجموعة مسارات إدارة الأدوار المتقدمة
//----------------------------- نهاية المسارات المحمية -------------------------------------------------------------------------------- 