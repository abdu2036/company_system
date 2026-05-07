<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Company;
use App\Models\MaintenanceLog; // تم إصلاح المسافة المخفية هنا
use Illuminate\Http\Request;

class AssetController extends Controller
{
    // 1. لوحة معلومات الأصول
    public function dashboard()
    {
        // تم استدعاء الموديل مباشرة بعد إصلاح الـ use
        $total_maintenance_cost = MaintenanceLog::sum('cost');

        $companies_data = Company::withCount(['assets' => function($query) {
            $query->where('status', '!=', 'تالف');
        }])->get();

        $data = [
            'total_assets' => Asset::count(),
            'active_assets' => Asset::where('status', '!=', 'تالف')->count(),
            'damaged_assets' => Asset::where('status', 'تالف')->count(),
            'total_value' => Asset::sum('purchase_price'),
            'total_maintenance_cost' => $total_maintenance_cost, 
            'chart_labels' => $companies_data->pluck('name'),
            'chart_values' => $companies_data->pluck('assets_count'),
        ];
        
        return view('companies.assets.dashboard', $data);
    }

    // 2. قائمة الأصول العاملة
    public function index(Request $request)
    {
        $companies = Company::all();
        $query = Asset::with('company');

        if ($request->has('company_id') && $request->company_id != '') {
            $query->where('company_id', $request->company_id);
        }

        $assets = $query->where('status', '!=', 'تالف')->get();

        return view('companies.assets.index', compact('assets', 'companies'));
    }

    // 3. مخزن الأصول التالفة
    public function damaged(Request $request)
    {
        $companies = Company::all();
        $query = Asset::where('status', 'تالف')
                      ->with(['company', 'maintenanceLogs.technician']); 

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('asset_code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('month')) {
            $query->whereMonth('updated_at', $request->month);
        }
        
        if ($request->filled('year')) {
            $query->whereYear('updated_at', $request->year);
        }

        $damaged_assets = $query->orderBy('updated_at', 'desc')->get();

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

        return redirect()->route('assets.damaged')->with('success', 'تم استعادة الأصل بنجاح');
    }

    // --- الدوال الخاصة بالصيانة ---

    public function sendToMaintenance($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->update(['status' => 'تحت الصيانة']);

        return redirect()->back()->with('success', 'تم تحويل حالة الأصل إلى تحت الصيانة');
    }

    public function completeMaintenance(Request $request, $id)
    {
        $request->validate([
            'maintenance_type' => 'required|string',
            'cost' => 'required|numeric|min:0',
        ]);

        $asset = Asset::findOrFail($id);

        MaintenanceLog::create([
            'asset_id' => $asset->id,
            'technician_id' => auth()->user()->employee_id, // ربط التقني الذي أكمل العمل
            'maintenance_type' => $request->maintenance_type,
            'cost' => $request->cost,
            'details' => $request->details,
            'start_date' => $request->start_date ?? now(),
            'end_date' => $request->end_date ?? now(),
        ]);

        $asset->update(['status' => 'مستعمل']);

        return redirect()->back()->with('success', 'تم إنهاء الصيانة وتوثيقها');
    }

    public function maintenanceLogs(Request $request)
    {
        $companies = Company::all();
        $query = MaintenanceLog::with('asset.company');

        if ($request->has('company_id') && $request->company_id != '') {
            $query->whereHas('asset', function($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

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

public function moveToDamaged($id)
{
    $asset = Asset::findOrFail($id);
    
    // 1. جلب ID الموظف من قاعدة بيانات HRMS بناءً على إيميل المستخدم الحالي
    // هذا يضمن أن الاسم سيظهر حتى لو كان حقل employee_id في جدول users فارغاً
    $hrEmployeeId = \Illuminate\Support\Facades\DB::connection('mysql_hrms')
        ->table('users')
        ->where('email', auth()->user()->email)
        ->value('id');

    if (!$hrEmployeeId) {
        return redirect()->back()->with('error', 'عذراً، لم يتم العثور على بريدك الإلكتروني في نظام HRMS لتوثيق المستلم.');
    }

    // 2. تحديث حالة الأصل إلى تالف
    $asset->update([
        'status' => 'تالف',
        'notes' => $asset->notes . "\n [تم النقل للمخزن التالف في " . date('Y-m-d') . "]"
    ]);

    // 3. إنشاء سجل الصيانة لربط الاسم بالدائرة (المسؤول الفني) في الجدول
    \App\Models\MaintenanceLog::create([
        'asset_id' => $asset->id,
        'technician_id' => $hrEmployeeId, // تم استخدام الـ ID الموثوق من HRMS
        'maintenance_type' => 'نقل للتوالف',
        'cost' => 0,
        'start_date' => now(),
    ]);

    return redirect()->back()->with('success', 'تم نقل الأصل وتوثيق اسم المستلم بنجاح.');
}
   public function confirmReceipt($id)
{
    $asset = Asset::findOrFail($id);

    // البحث عن ID الموظف في قاعدة بيانات HRMS باستخدام إيميل المستخدم الحالي
    $hrEmployeeId = \Illuminate\Support\Facades\DB::connection('mysql_hrms')
        ->table('users')
        ->where('email', auth()->user()->email)
        ->value('id');

    if (!$hrEmployeeId) {
        return redirect()->back()->with('error', 'عذراً، بريدك الإلكتروني غير مسجل في نظام الموارد البشرية (HRMS).');
    }

    $asset->update([
        'status' => 'تالف', 
        'received_at' => now(),
        'received_by_emp_id' => $hrEmployeeId 
    ]);

    MaintenanceLog::create([
        'asset_id' => $asset->id,
        'technician_id' => $hrEmployeeId, 
        'maintenance_type' => 'فحص أولي',
        'cost' => 0,
        'details' => 'تم استلام الأصل لبدء الفحص.',
        'start_date' => now(),
    ]);

    return redirect()->back()->with('success', 'تم استلام الأصل بنجاح وتوثيقك كمسؤول.');
}
// --- دوال خاصة بإدارة الصلاحيات (للمدير فقط) ---
public function updatePermission(Request $request)
{
    $role = \Spatie\Permission\Models\Role::findById($request->role_id);
    
    if ($request->status) {
        $role->givePermissionTo($request->permission);
    } else {
        $role->revokePermissionTo($request->permission);
    }

    return response()->json(['success' => true]);
}
}