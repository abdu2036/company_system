<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Company;
// تأكد من استيراد موديل سجل الصيانة إذا أنشأته
// use App\Models\MaintenanceLog; 
use Illuminate\Http\Request;

class AssetController extends Controller
{
    // 1. لوحة معلومات الأصول
   public function dashboard()
{
    // 1. حساب تكاليف الصيانة الإجمالية (لحل مشكلة 0.00 د.ل)
    $total_maintenance_cost = \App\Models\MaintenanceLog::sum('cost');

    // 2. تجهيز بيانات الرسم البياني (توزيع الأصول حسب الشركة)
    $companies_data = Company::withCount(['assets' => function($query) {
        $query->where('status', '!=', 'تالف');
    }])->get();

    $data = [
        'total_assets' => Asset::count(),
        'active_assets' => Asset::where('status', '!=', 'تالف')->count(),
        'damaged_assets' => Asset::where('status', 'تالف')->count(),
        'total_value' => Asset::sum('purchase_price'),
        'total_maintenance_cost' => $total_maintenance_cost, // القيمة الجديدة
        'chart_labels' => $companies_data->pluck('name'),    // أسماء الشركات للرسم
        'chart_values' => $companies_data->pluck('assets_count'), // أعداد الأصول للرسم
    ];
    
    return view('companies.assets.dashboard', $data);
}

    // 2. قائمة الأصول العاملة
    public function index(Request $request)
{
    // جلب قائمة الشركات لعرضها في قائمة منسدلة للفلترة
    $companies = Company::all();

    // البدء باستعلام الأصول مع الشركة التابعة لها
    $query = Asset::with('company');

    // إذا اختار المستخدم شركة معينة من القائمة المنسدلة
    if ($request->has('company_id') && $request->company_id != '') {
        $query->where('company_id', $request->company_id);
    }

    $assets = $query->where('status', '!=', 'تالف')->get();

    return view('companies.assets.index', compact('assets', 'companies'));
}
    // 3. مخزن الأصول التالفة
  public function damaged(Request $request)
{
    // 1. جلب قائمة الشركات لتعريفها في قائمة الفلتر المنسدلة
    $companies = Company::all();

    // 2. البدء ببناء الاستعلام للأصول التالفة فقط
    $query = Asset::where('status', 'تالف')->with('company');

    // 3. الفلترة حسب الشركة إذا تم اختيارها
    if ($request->filled('company_id')) {
        $query->where('company_id', $request->company_id);
    }

    // 4. البحث بالاسم أو الكود إذا تم إدخال نص في حقل البحث
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('asset_code', 'like', '%' . $request->search . '%');
        });
    }

    // 5. الفلترة حسب الشهر والسنة (بناءً على تاريخ التحديث updated_at)
    if ($request->filled('month')) {
        $query->whereMonth('updated_at', $request->month);
    }
    
    if ($request->filled('year')) {
        $query->whereYear('updated_at', $request->year);
    }

    // 6. تنفيذ الاستعلام وترتيب النتائج من الأحدث إلى الأقدم
    $damaged_assets = $query->orderBy('updated_at', 'desc')->get();

    // 7. تمرير البيانات للـ View (تأكد من تمرير $companies أيضاً)
    return view('companies.assets.damaged', compact('damaged_assets', 'companies'));
}

    // 4. عرض صفحة إضافة أصل جديد
    public function create()
    {
        $companies = Company::all();
        return view('companies.assets.create', compact('companies'));
    }

    // 5. حفظ الأصل
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'category' => 'required',
            'status' => 'required',
        ]);

        do {
            $assetCode = 'AST-' . strtoupper(substr(md5(microtime()), 0, 6));
        } while (Asset::where('asset_code', $assetCode)->exists());

        Asset::create([
            'company_id' => $request->company_id,
            'name' => $request->name,
            'category' => $request->category,
            'location' => $request->location,
            'status' => $request->status,
            'purchase_price' => $request->purchase_price ?? 0,
            'notes' => $request->notes,
            'asset_code' => $assetCode,
        ]);

        return redirect()->route('assets.index')->with('success', 'تم إضافة الأصل بنجاح بترميز: ' . $assetCode);
    }

    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $companies = Company::all();
        return view('companies.assets.edit', compact('asset', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'category' => 'required',
            'location' => 'required',
            'status' => 'required',
            'notes' => 'nullable|string',
        ]);

        $asset->update($validated);

        return redirect()->route('assets.index')->with('success', 'تم تحديث بيانات الأصل بنجاح');
    }

    public function show($id)
    {
        $asset = Asset::with('company')->findOrFail($id);
        return response()->json($asset);
    }

    public function restore($id)
    {
        $asset = Asset::findOrFail($id);
        
        $asset->update([
            'status' => 'مستعمل',
            'notes' => $asset->notes . "\n [تمت الاستعادة من مخزن التالف في " . date('Y-m-d') . "]"
        ]);

        return redirect()->route('assets.damaged')->with('success', 'تم استعادة الأصل بنجاح إلى القائمة النشطة');
    }

    // --- الدوال الجديدة الخاصة بالصيانة ---

    // تحويل للأصل للصيانة
    public function sendToMaintenance($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->update(['status' => 'تحت الصيانة']);

        return redirect()->back()->with('success', 'تم تحويل حالة الأصل إلى تحت الصيانة بنجاح');
    }

    // إتمام الصيانة واستلام بيانات المودال (التكلفة والنوع)
   public function completeMaintenance(Request $request, $id)
{
    // 1. التحقق من البيانات القادمة من المودال
    $request->validate([
        'maintenance_type' => 'required|string',
        'cost' => 'required|numeric|min:0',
        'details' => 'nullable|string',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
    ]);

    $asset = Asset::findOrFail($id);

    // 2. حفظ السجل في جدول الصيانة المستقل (الحل الاحترافي)
    \App\Models\MaintenanceLog::create([
        'asset_id' => $asset->id,
        'maintenance_type' => $request->maintenance_type,
        'cost' => $request->cost,
        'details' => $request->details,
        'start_date' => $request->start_date ?? now(), // إذا لم يدخل تاريخ البدء نضع تاريخ اليوم
        'end_date' => $request->end_date ?? now(),
    ]);

    // 3. تحديث حالة الأصل إلى "مستعمل" ليظهر في القائمة النشطة مرة أخرى
    $asset->update([
        'status' => 'مستعمل'
    ]);

    return redirect()->back()->with('success', 'تم إنهاء الصيانة وتوثيقها في سجلات النظام بنجاح');
}

public function maintenanceLogs(Request $request)
{
    // جلب قائمة الشركات لعرضها في الفلتر
    $companies = Company::all();

    $query = \App\Models\MaintenanceLog::with('asset.company');

    // 1. الفلترة حسب الشركة (هذا هو الجزء المفقود لديك)
    if ($request->has('company_id') && $request->company_id != '') {
        $query->whereHas('asset', function($q) use ($request) {
            $q->where('company_id', $request->company_id);
        });
    }

    // 2. الفلترة حسب البحث والشهر والسنة (كما فعلنا سابقاً)
    if ($request->search) {
        $query->whereHas('asset', function($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('asset_code', 'like', '%' . $request->search . '%');
        });
    }
    
    if ($request->month) $query->whereMonth('start_date', $request->month);
    if ($request->year) $query->whereYear('start_date', $request->year);

    $logs = $query->orderBy('start_date', 'desc')->get();
    $total_maintenance_cost = $logs->sum('cost');

    return view('companies.assets.maintenance_report', compact('logs', 'total_maintenance_cost', 'companies'));
}
// دالة نقل الأصل إلى مخزن التوالف مع توثيق التاريخ في الملاحظات
public function moveToDamaged($id)
{
    $asset = Asset::findOrFail($id);
    
    // تحديث الحالة إلى تالف وتوثيق التاريخ في الملاحظات
    $asset->update([
        'status' => 'تالف',
        'notes' => $asset->notes . "\n [تم النقل للمخزن التالف في " . date('Y-m-d') . "]"
    ]);

    return redirect()->back()->with('success', 'تم نقل الأصل إلى مخزن التوالف بنجاح');
}
}