<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\License;
use App\Models\CommercialRegister;
use App\Models\Chamber;
use App\Models\Importer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class ReportController extends Controller
{
    public function index()
    {
        // 1. إحصائيات الشركات
        $totalCompanies = Company::count();

        // 2. إحصائيات التراخيص
        $totalLicenses = License::count();
        $expiredLicenses = License::where('expiry_date', '<', Carbon::now())->count();
        $activeLicenses = License::where('expiry_date', '>=', Carbon::now())->count();

        // 3. إحصائيات السجل التجاري
        $totalRegisters = CommercialRegister::count();
        $expiredRegisters = CommercialRegister::where('expiry_date', '<', Carbon::now())->count();

        // 4. إحصائيات الغرفة التجارية
        $totalChamber = Chamber::count();
        $nearExpiryChamber = Chamber::whereBetween('expiry_date', [Carbon::now(), Carbon::now()->addDays(30)])->count();
          $expiredChamber = Chamber::where('expiry_date', '<', Carbon::now())->count();
        $activeChamber = Chamber::where('expiry_date', '>=', Carbon::now())->count();


        // 5. إحصائيات سجل المستوردين
        $totalImporters = Importer::count();
         $expiredImporters = Importer::where('expiry_date', '<', Carbon::now())->count();
        $activeImporters = Importer::where('expiry_date', '>=', Carbon::now())->count();


        return view('reports.index', compact(
            'totalCompanies', 
            'totalLicenses', 
            'expiredLicenses', 
            'activeLicenses',
            'totalRegisters', 
            'expiredChamber', // أضفنا هذا المتغير لعرض عدد الغرف التجارية التي ستنتهي قريبًا
            'expiredRegisters', 
            'totalChamber', 
            'nearExpiryChamber', 
            'totalImporters',
            'expiredImporters', // أضفنا هذا المتغير لعرض عدد المستوردين الذين ستنتهي تراخيصهم قريبًا
            'activeImporters'
        ));
    }

public function financialReports()
{
    // 1. حساب إجمالي الإيرادات من جدول revenues الفعلي في قاعدة الشركات
    $totalRevenues = DB::connection('mysql')->table('revenues')->sum('amount');

    // 2. حساب إجمالي المصروفات التشغيلية (والتي ستتضمن بند المرتبات المدخل يدويًا)
    $totalExpenses = DB::connection('mysql')->table('company_expenses')->sum('amount');

    // 3. ❌ تم إلغاء جلب المرتبات تلقائياً من منظومة hrms_db بناءً على رغبتك
    $totalSalaries = 0; 

    // 4. المعالجة الآمنة لحساب الضرائب من جدول المصروفات
    $totalTaxes = 0;
    if (Schema::connection('mysql')->hasColumn('company_expenses', 'category')) {
        $totalTaxes = DB::connection('mysql')->table('company_expenses')->where('category', 'taxes')->sum('amount');
    } elseif (Schema::connection('mysql')->hasColumn('company_expenses', 'expense_category_id')) {
        $totalTaxes = DB::connection('mysql')->table('company_expenses')->where('expense_category_id', 1)->sum('amount');
    }

    // 5. معادلة صافي الربح الإجمالي الاعتمادية الآن (الإيرادات - المصروفات الكلية)
    $netProfit = $totalRevenues - $totalExpenses;

    // 6. تمرير البيانات الحية إلى صفحة الـ Blade
    return view('reports.financial', compact(
        'totalRevenues', 
        'totalExpenses', 
        'totalSalaries', 
        'totalTaxes', 
        'netProfit'
    ));
}

// =========================================================================
// 📅 دالة توليد تقرير الأرباح والخسائر السنوي
// =========================================================================
public function annualReport(Request $request)
{
    // جلب السنة المطلوبة من الفلتر، أو اعتماد السنة الحالية كخيار افتراضي
    $year = $request->get('year', Carbon::now()->year);

    // 1. جلب إجمالي الإيرادات لهذه السنة فقط بناءً على حقل التاريخ (تأكد من اسم حقل التاريخ لديك، هنا فرضناه created_at)
    $totalRevenues = DB::connection('mysql')->table('revenues')
        ->whereYear('created_at', $year)
        ->sum('amount');

    // 2. جلب إجمالي المصروفات لهذه السنة فقط
    $totalExpenses = DB::connection('mysql')->table('company_expenses')
        ->whereYear('created_at', $year)
        ->sum('amount');

    // 3. حساب صافي الربح السنوي
    $netProfit = $totalRevenues - $totalExpenses;

    // 4. جلب تفصيل شهري (من شهر 1 إلى 12) لبناء جدول مالي منظم داخل التقرير
    $monthlyData = [];
    for ($m = 1; $m <= 12; $m++) {
        $monthlyRevenues = DB::connection('mysql')->table('revenues')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $m)
            ->sum('amount');

        $monthlyExpenses = DB::connection('mysql')->table('company_expenses')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $m)
            ->sum('amount');

        $monthlyData[$m] = [
            'revenues' => $monthlyRevenues,
            'expenses' => $monthlyExpenses,
            'profit'   => $monthlyRevenues - $monthlyExpenses
        ];
    }

    // 5. تمرير البيانات الحية إلى صفحة الـ Blade المخصصة للتقرير السنوي
    return view('reports.annual', compact('year', 'totalRevenues', 'totalExpenses', 'netProfit', 'monthlyData'));
}
}