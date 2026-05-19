<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class CompanyController extends Controller
{
    /**
     * عرض قائمة الشركات الأساسية مع حالات التراخيص
     */
public function index(Request $request)
{
    $query = Company::query();

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function($q) use ($search) {
            // بحث في اسم الشركة (جدول الشركات)
            $q->where('name', 'like', "%{$search}%")
            
            // بحث في اسم المفوض أو الهاتف (جدول السجل التجاري)
            ->orWhereHas('commercialRegister', function($subQuery) use ($search) {
                $subQuery->where('representative_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
            });
        });
    }
    // جلب البيانات مع التقسيم لصفحات
    $companies = $query->latest()->paginate(10);

    return view('companies.index', compact('companies'));
}
    /**
 * فتح نموذج إضافة شركة جديدة (النموذج المتكامل)
 */
public function create()
{
    // عرض صفحة إضافة شركة (تأكد من مسار ملف الـ view لديك)
    return view('companies.create'); 
}

    /**
     * حفظ بيانات الشركة الأساسية مع الملحقات (النموذج المتكامل)
     */
    public function store(Request $request)
    {
        // التحقق من البيانات
    $request->validate([
        // أضفنا unique:companies,name للتأكد من عدم تكرار الاسم في جدول الشركات
        'name'                => 'required|string|max:255|unique:companies,name', 
        'activity'            => 'required|string|max:255',
        'address'             => 'required|string',
        'cr_number'           => 'required|string|unique:commercial_registers,cr_number',
        'phone'               => 'required|string',
        'representative_name' => 'required|string',
        'cr_issue_date'       => 'required|date',
        'cr_expiry_date'      => 'required|date|after:cr_issue_date',
    ], [
        // رسائل خطأ مخصصة بالعربي لتجربة مستخدم أفضل
        'name.unique' => 'خطأ: شركة ( ' . $request->name . ' ) مسجلة بالفعل في النظام ولا يمكن تكرارها.',
        'cr_number.unique' => 'رقم السجل التجاري هذا مستخدم من قبل شركة أخرى.',
    ]);

        DB::beginTransaction();

        try {
            // 1. حفظ الشركة الأساسية

            $company = Company::create([
                'name'       => $request->name,
                'activity'   => $request->activity,
                'address'    => $request->address,
                'created_by' => Auth::id(),
            ]);

            // 2. حفظ السجل التجاري (علاقة 1 لـ 1)
            $company->commercialRegister()->create([
                'cr_number'           => $request->cr_number,
                'representative_name' => $request->representative_name,
                'phone'               => $request->phone,
                'issue_date'          => $request->cr_issue_date,
                'expiry_date'         => $request->cr_expiry_date,
                'document_path'       => $this->moveTempFile($request->temp_file_path, 'registers'),
                'created_by'          => Auth::id(),
            ]);

            // 3. حفظ بقية الملحقات (اختيارية)
            $this->saveOptionalData($company, $request);

            DB::commit();
            return redirect()->route('companies.index')->with('success', 'تم تسجيل الشركة بنجاح! 🎉');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * حذف الشركة وكل ما يتعلق بها
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        
        // إذا كنت تستخدم Cascade Delete في قاعدة البيانات سيمسح الباقي تلقائياً
        // وإلا يفضل حذف الملحقات هنا
        $company->delete();

        return back()->with('success', 'تم حذف الشركة وكافة سجلاتها بنجاح ✅');
    }

    /**
     * دالة مساعدة لحفظ البيانات الاختيارية لتقليل زحمة كود الـ Store
     */
    private function saveOptionalData($company, $request)
    {
        if ($request->license_number) {
            $company->license()->create([
                'license_number' => $request->license_number,
                'tax_number'     => $request->tax_number,
                'issue_date'     => $request->issue_date,
                'expiry_date'    => $request->expiry_date,
                'document_path'  => $this->moveTempFile($request->license_temp_path, 'licenses'),
                'created_by'     => Auth::id(),
            ]);
        }

        if ($request->chamber_number) {
            $company->chamber()->create([
                'chamber_number' => $request->chamber_number,
                'issue_date'     => $request->chamber_issue_date,
                'expiry_date'    => $request->chamber_expiry_date,
                'document_path'  => $this->moveTempFile($request->chamber_temp_path, 'chambers'),
                'created_by'     => Auth::id(),
            ]);
        }

        if ($request->importer_number) {
            $company->importer()->create([
                'importer_number' => $request->importer_number,
                'issue_date'      => $request->importer_issue_date,
                'expiry_date'     => $request->importer_expiry_date,
                'document_path'   => $this->moveTempFile($request->importer_temp_path, 'importers'),
                'created_by'      => Auth::id(),
            ]);
        }
    }

    /**
     * وظائف مساعدة لمعالجة الملفات (تبقى هنا لأنها عامة للشركة)
     */
    private function moveTempFile($tempPath, $folder)
    {
        if (!$tempPath) return null;
        $cleanTempPath = ltrim($tempPath, '/');
        $fullTempPath = public_path($cleanTempPath);

        if (File::exists($fullTempPath)) {
            $fileName = basename($fullTempPath);
            $relativeDestinationPath = 'assets/admin/uploads/' . $folder . '/' . $fileName;
            $fullDestinationPath = public_path($relativeDestinationPath);

            if (!File::isDirectory(dirname($fullDestinationPath))) {
                File::makeDirectory(dirname($fullDestinationPath), 0755, true);
            }

            File::move($fullTempPath, $fullDestinationPath);
            return $relativeDestinationPath;
        }
        return null;
    }

    /**
     * دالة ثابتة للـ Badge (ستبقى هنا لاستدعائها في الـ Blade)
     */
    public static function getStatusBadge($date)
    {
        if (!$date) return null; // نرجع نل لكي يظهر زر "لا يوجد" في الـ Blade

        $expiryDate = \Carbon\Carbon::parse($date);
        $now = \Carbon\Carbon::now();

        if ($expiryDate->isPast()) {
            return '<span class="badge badge-danger" style="padding: 5px 10px;">منتهي (' . $date . ')</span>';
        } elseif ($expiryDate->diffInDays($now) <= 30) {
            return '<span class="badge badge-warning" style="padding: 5px 10px; color: white;">ينتهي قريباً (' . $date . ')</span>';
        } else {
            return '<span class="badge badge-success" style="padding: 5px 10px;">ساري (' . $date . ')</span>';
        }
    }
}