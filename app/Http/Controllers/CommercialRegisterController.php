<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CommercialRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class CommercialRegisterController extends Controller
{
    /**
     * عرض كافة السجلات التجارية
     */
public function index(\Illuminate\Http\Request $request)
{
    // 1. جلب السجلات مع علاقة الشركة
    $query = \App\Models\CommercialRegister::with('company');

    // 2. تطبيق منطق البحث الذكي
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('cr_number', 'like', '%' . $search . '%') // رقم السجل
              ->orWhere('representative_name', 'like', '%' . $search . '%') // اسم المفوض
              ->orWhereHas('company', function($sub) use ($search) {
                  $sub->where('name', 'like', '%' . $search . '%'); // اسم الشركة
              });
        });
    }

    // 3. الترتيب حسب تاريخ الانتهاء (الأقرب أولاً) مع التقسيم لصفحات
    $registers = $query->orderBy('expiry_date', 'asc')
                       ->paginate(10);

    return view('companies.registers.index', compact('registers'));
}

    /**
     * فتح نموذج إضافة سجل جديد
     */
    public function create(Request $request)
    {
        // جلب قائمة الشركات للاختيار منها
        $companies = Company::all();
        
        // استلام رقم الشركة إذا جاء من جدول الشركات الرئيسي
        $selected_company = $request->query('company_id');

        return view('companies.registers.create', compact('companies', 'selected_company'));
    }

    /**
     * حفظ السجل التجاري الجديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id'          => 'required|exists:companies,id',
            'cr_number'           => 'required|string|unique:commercial_registers,cr_number',
            'phone'               => 'required|string',
            'representative_name' => 'required|string',
            'cr_issue_date'       => 'required|date',
            'cr_expiry_date'      => 'required|date|after:cr_issue_date',
        ], [
            'cr_number.unique' => 'رقم السجل التجاري مسجل مسبقاً!'
        ]);

        $data = [
            'company_id'          => $request->company_id,
            'cr_number'           => $request->cr_number,
            'representative_name' => $request->representative_name,
            'phone'               => $request->phone,
            'issue_date'          => $request->cr_issue_date,
            'expiry_date'         => $request->cr_expiry_date,
            'created_by'          => Auth::id(),
        ];

        // معالجة الملف المرفق (استخدام الدالة المساعدة المذكورة أدناه أو نقلها لهذا الكنترول)
        if ($request->filled('temp_file_path')) {
            $data['document_path'] = $this->moveTempFile($request->temp_file_path, 'registers');
        }

        CommercialRegister::create($data);

        return redirect()->route('commercial-registers.index')->with('success', 'تم إضافة السجل التجاري بنجاح ✅');
    }

    public function edit($id)
    {
        $register = CommercialRegister::findOrFail($id);
        $companies = Company::all();
        return view('companies.registers.edit', compact('register', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $register = CommercialRegister::findOrFail($id);

        $request->validate([
            'cr_number'           => 'required|string|unique:commercial_registers,cr_number,' . $id,
            'phone'               => 'required|string',
            'representative_name' => 'required|string',
            'cr_issue_date'       => 'required|date',
            'cr_expiry_date'      => 'required|date',
        ]);

        $data = [
            'cr_number'           => $request->cr_number,
            'phone'               => $request->phone,
            'representative_name' => $request->representative_name,
            'issue_date'          => $request->cr_issue_date,
            'expiry_date'         => $request->cr_expiry_date,
        ];

        if ($request->filled('temp_file_path')) {
            $data['document_path'] = $this->moveTempFile($request->temp_file_path, 'registers');
        }

        $register->update($data);

        return redirect()->route('commercial-registers.index')->with('success', 'تم تحديث السجل بنجاح ✅');
    }

    public function destroy($id)
    {
        $register = CommercialRegister::findOrFail($id);
        if ($register->document_path && File::exists(public_path($register->document_path))) {
            File::delete(public_path($register->document_path));
        }
        $register->delete();

        return back()->with('success', 'تم حذف السجل بنجاح ✅');
    }

    // انقل دالة moveTempFile هنا أيضاً لتعمل مع السجلات
    private function moveTempFile($tempPath, $folder)
    {
        if (!$tempPath) return null;
        $cleanTempPath = ltrim($tempPath, '/');
        $fullTempPath = public_path($cleanTempPath);

        if (File::exists($fullTempPath)) {
            $fileName = basename($fullTempPath);
            $relativeDestinationPath = 'assets/admin/uploads/' . $folder . '/' . $fileName;
            $fullDestinationPath = public_path($relativeDestinationPath);

            $dir = dirname($fullDestinationPath);
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            File::move($fullTempPath, $fullDestinationPath);
            return $relativeDestinationPath;
        }
        return null;
    }
public function renew($id)
{
    // 1. جلب بيانات السجل مع بيانات الشركة المرتبطة به لضمان ظهور الاسم
    $register = CommercialRegister::with('company')->findOrFail($id);
    
    // 2. توجيه المستخدم لملف التجديد الجديد
    return view('companies.registers.renew', compact('register'));
}
public function updateRenew(Request $request, $id)
{
    // 1. التحقق من التواريخ فقط (الملف لم يعد مطلوباً)
    $request->validate([
        'renewal_date'   => 'required|date',
        'cr_expiry_date' => 'required|date|after:renewal_date',
    ], [
        'renewal_date.required'   => 'يرجى تحديد تاريخ التجديد الجديد.',
        'cr_expiry_date.required' => 'يرجى تحديد تاريخ الانتهاء الجديد.',
        'cr_expiry_date.after'    => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ التجديد.',
    ]);

    $register = CommercialRegister::findOrFail($id);

    // 2. تجهيز البيانات للتحديث
    $data = [
        'issue_date'  => $request->renewal_date,
        'expiry_date' => $request->cr_expiry_date,
    ];

    // 3. معالجة الملف (فقط إذا قام المستخدم برفعه)
    if ($request->filled('temp_file_path')) {
        $data['document_path'] = $this->moveTempFile($request->temp_file_path, 'registers');
    }
    // ملاحظة: إذا لم يرفع ملفاً، سيبقى document_path القديم كما هو في قاعدة البيانات

    // 4. الحفظ والعودة
    $register->update($data);

    return redirect()->route('commercial-registers.index')->with('success', 'تم تجديد تواريخ السجل بنجاح ✅');
}
public function uploadTemp(Request $request)
{
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $tempPath = 'assets/admin/uploads/temp'; // مسار مؤقت
        
        $file->move(public_path($tempPath), $fileName);
        
        return response()->json([
            'status' => 'success',
            'temp_path' => '/' . $tempPath . '/' . $fileName
        ]);
    }

    return response()->json(['status' => 'error', 'message' => 'لم يتم استلام ملف'], 400);
}
}