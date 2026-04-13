<?php

namespace App\Http\Controllers;

use App\Models\Chamber;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ChamberController extends Controller
{
    /**
     * عرض قائمة الاشتراكات المسجلة 📋
     */
// تأكد من إضافة Request $request هنا في القوسين
public function index(Request $request) 
{
    // 1. البدء بالاستعلام مع جلب علاقة الشركة
    $query = \App\Models\Chamber::with('company');

    // 2. تطبيق البحث إذا كان الحقل ممتلئاً
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('chamber_number', 'like', '%' . $search . '%')
              ->orWhereHas('company', function($sub) use ($search) {
                  $sub->where('name', 'like', '%' . $search . '%');
              });
        });
    }

    // 3. تطبيق التصفية والترتيب (كما في كودك الأصلي)
    $chambers = $query->whereNotNull('chamber_number')
                      ->where('chamber_number', '!=', '')
                      ->orderBy('expiry_date', 'asc')
                      ->paginate(10); // استخدم paginate بدل get لدعم تقسيم الصفحات

    return view('companies.chambers.index', compact('chambers'));
}
    /**
     * عرض نموذج الإضافة مع تصفية الشركات المتاحة فقط 🔍
     */
public function create()
{
    // جلب الشركات التي ليس لها سجل غرفة، أو سجلها لا يحتوي على رقم عضوية
    $companies = \App\Models\Company::whereDoesntHave('chamber', function ($query) {
        $query->whereNotNull('chamber_number')->where('chamber_number', '!=', '');
    })->get();

    return view('companies.chambers.create', compact('companies'));
}
    /**
     * حفظ الاشتراك الجديد 💾
     */
    public function store(Request $request)
{
    // 1. التحقق من البيانات (أزلنا شرط الـ unique هنا لنسمح بالتحديث)
    $request->validate([
        'company_id'     => 'required|exists:companies,id',
        'chamber_number' => 'required|string',
        'issue_date'     => 'required|date',
        'expiry_date'    => 'required|date|after:issue_date',
    ]);

  // 2. معالجة الملف (نقل الملف من المجلد المؤقت إلى المجلد الدائم) 📁
$documentPath = null;

if ($request->filled('temp_file_path')) {
    // الحصول على المسار الكامل للملف المؤقت
    $tempPath = public_path($request->temp_file_path);

    if (file_exists($tempPath)) {
        // استخراج اسم الملف
        $fileName = basename($tempPath);
        
        // تحديد المسار النهائي (مثلاً داخل مجلد uploads/chambers)
        $finalPath = 'assets/admin/uploads/chambers/' . $fileName;

        // التأكد من وجود المجلد النهائي، وإذا لم يوجد يتم إنشاؤه
        if (!file_exists(public_path('assets/admin/uploads/chambers/'))) {
            mkdir(public_path('assets/admin/uploads/chambers/'), 0777, true);
        }

        // نقل الملف من المسار المؤقت إلى المسار الدائم
        rename($tempPath, public_path($finalPath));
        
        // تخزين المسار الجديد لاستخدامه في قاعدة البيانات
        $documentPath = $finalPath;
    }

    }

    // 3. التحديث أو الإنشاء الذكي
    \App\Models\Chamber::updateOrCreate(
        ['company_id' => $request->company_id], // البحث عن هذا المعرف
        [
            'chamber_number' => $request->chamber_number,
            'issue_date'     => $request->issue_date,
            'expiry_date'    => $request->expiry_date,
            'document_path'  => $documentPath,
            'updated_by'     => Auth::id(),
        ]
    );

    return redirect()->route('chambers.index')->with('success', 'تم حفظ بيانات الغرفة بنجاح ✅');
}

    /**
     * عرض نموذج التعديل ✏️
     */
    public function edit(string $id)
    {
        $chamber = Chamber::findOrFail($id);
        $companies = Company::all(); 
        return view('companies.chambers.edit', compact('chamber', 'companies'));
    }

    /**
     * تحديث البيانات القائمة 🔄
     */
   // 1. دالة التعديل العادية (تغير كل شيء)
public function update(Request $request, $id)
{
    $request->validate([
        'company_id'     => 'required|exists:companies,id|unique:chambers,company_id,' . $id,
        'chamber_number' => 'required|string|unique:chambers,chamber_number,' . $id,
        'issue_date'     => 'required|date',
        'expiry_date'    => 'required|date',
    ]);

    $chamber = Chamber::findOrFail($id);
    
    $chamber->update([
        'company_id'     => $request->company_id,
        'chamber_number' => $request->chamber_number,
        'issue_date'     => $request->issue_date,
        'expiry_date'    => $request->expiry_date,
        'updated_by'     => Auth::id(),
    ]);

    return redirect()->route('chambers.index')->with('success', 'تم تحديث البيانات بنجاح ✅');
}

// 2. دالة التجديد (تغير التواريخ والمرفق فقط)
public function renewUpdate(Request $request, $id)
{
    $request->validate([
        'issue_date'  => 'required|date',
        'expiry_date' => 'required|date',
    ]);

    $chamber = Chamber::findOrFail($id);

    // تحديث التواريخ فقط
    $chamber->issue_date = $request->issue_date;
    $chamber->expiry_date = $request->expiry_date;
    $chamber->updated_by = Auth::id();

    // التعامل مع المرفق الجديد إذا وجد
    if ($request->temp_file_path) {
        $chamber->document_path = $request->temp_file_path;
    }

    $chamber->save();

    return redirect()->route('chambers.index')->with('success', 'تم تجديد الاشتراك بنجاح 🔄');
}

// 3. دالة الحذف
public function destroy($id)
{
    $chamber = Chamber::findOrFail($id);
    $chamber->delete();

    return redirect()->route('chambers.index')->with('success', 'تم حذف السجل بنجاح 🗑️');
}

// 4. دالة عرض صفحة التجديد
public function renew($id)
{
    $chamber = Chamber::with('company')->findOrFail($id);
    return view('companies.chambers.renew', compact('chamber'));
}

}