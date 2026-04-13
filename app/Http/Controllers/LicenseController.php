<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LicenseController extends Controller
{
    // 1. عرض قائمة التراخيص مرتبة حسب تاريخ الانتهاء
public function index(\Illuminate\Http\Request $request)
{
    // 1. نبدأ الاستعلام مع جلب علاقة الشركة
    $query = \App\Models\License::with('company');

    // 2. تطبيق البحث الذكي
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('license_number', 'like', '%' . $search . '%') // البحث برقم الترخيص
              ->orWhereHas('company', function($sub) use ($search) {
                  $sub->where('name', 'like', '%' . $search . '%'); // البحث باسم الشركة
              });
        });
    }

    // 3. التصفية والترتيب (كما في كودك الأصلي) والتقسيم لصفحات
    $licenses = $query->whereNotNull('license_number')
                      ->where('license_number', '!=', '')
                      ->orderBy('expiry_date', 'asc')
                      ->paginate(10); // التقسيم لصفحات يمنع ثقل الصفحة

    return view('companies.licenses.index', compact('licenses'));
}

    // 2. عرض صفحة إضافة ترخيص جديد
    public function create()
    {
        // جلب الشركات التي ليس لها ترخيص ساري
        $companies = Company::whereDoesntHave('license', function ($query) {
            $query->whereNotNull('license_number')->where('license_number', '!=', '');
        })->get();

        return view('companies.licenses.create', compact('companies'));
    }

    // 3. حفظ بيانات الترخيص الجديد
    public function store(Request $request)
{
    // التحقق من البيانات
    $request->validate([
        'company_id'     => 'required|exists:companies,id',
        'license_number' => 'required|string',
        'issue_date'     => 'required|date',
        'expiry_date'    => 'required|date',
        'file_path'      => 'nullable|string', // تأكد من وجود المرفق في الـ validate
    ]);

    // جلب المسار سواء جاء باسم file_path أو temp_file_path
    $filePath = $request->input('file_path') ?? $request->input('temp_file_path');

    License::create([
        'company_id'     => $request->company_id,
        'license_number' => $request->license_number,
        'issue_date'     => $request->issue_date,
        'expiry_date'    => $request->expiry_date,
        'document_path'  => $filePath, // العمود الصحيح في الداتابيز كما في الصورة
        'created_by'     => Auth::id(), // من المهم تسجيل من أنشأ السجل
    ]);

    return redirect()->route('licenses.index')->with('success', 'تم حفظ بيانات الترخيص والمرفق بنجاح ✅');
}

    // 4. عرض صفحة التجديد
    public function renew($id)
    {
        $license = License::with('company')->findOrFail($id);
        return view('companies.licenses.renew', compact('license'));
    }

    // 5. معالجة بيانات التجديد
// 2. دالة التجديد (RenewUpdate) - قم بتطبيق نفس المنطق
public function renewUpdate(Request $request, $id)
{
    // ... كود الـ validate كما هو ...

    $license = License::findOrFail($id);
    
    $data = [
        'issue_date'  => $request->issue_date,
        'expiry_date' => $request->expiry_date,
        'updated_by'  => Auth::id(),
    ];

    // جلب المسار الجديد
    $newDocumentPath = $request->input('temp_file_path');

    if (!empty($newDocumentPath)) {
        $data['document_path'] = $newDocumentPath;
    }

    $license->update($data);

    return redirect()->route('licenses.index')->with('success', 'تم تجديد الترخيص بنجاح 🔄');
}

    // 6. حذف الترخيص
    public function destroy($id)
    {
        $license = License::findOrFail($id);
        $license->delete();
        return redirect()->route('licenses.index')->with('success', 'تم حذف الترخيص بنجاح 🗑️');
    }

    // 7. عرض صفحة التعديل
    public function edit($id)
    {
        $license = License::findOrFail($id);
        $companies = Company::all();
        return view('companies.licenses.edit', compact('license', 'companies'));
    }

    // 8. تحديث البيانات المعدلة
// 1. دالة التحديث (Update)
public function update(Request $request, $id)
{
    $request->validate([
        'company_id'     => 'required|exists:companies,id',
        'license_number' => 'required|string',
        'issue_date'     => 'required|date',
        'expiry_date'    => 'required|date',
    ]);

    $license = License::findOrFail($id);
    
    $data = [
        'company_id'     => $request->company_id,
        'license_number' => $request->license_number,
        'issue_date'     => $request->issue_date,
        'expiry_date'    => $request->expiry_date,
        'updated_by'     => Auth::id(),
    ];

    // جلب مسار الملف الجديد إن وجد
    $newPath = $request->input('temp_file_path') ?? $request->input('file_path');

    if (!empty($newPath)) {
        $data['document_path'] = $newPath;
    }

    $license->update($data);

    return redirect()->route('licenses.index')->with('success', 'تم تحديث الترخيص بنجاح ✅');
}

    // 9. دالة الرفع المؤقت (AJAX) - تم التأكد من مسار المجلد
    public function uploadTemp(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // المسار المعتمد في مشروعك
            $destinationPath = public_path('assets/admin/uploads/licenses');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $fileName);

            return response()->json([
                'status' => 'success',
                'temp_path' => 'assets/admin/uploads/licenses/' . $fileName
            ]);
        }
        return response()->json(['status' => 'error', 'message' => 'لم يتم اختيار ملف'], 400);
    }
}