<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\StatisticalRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StatisticalRegisterController extends Controller
{
    /**
     * عرض شاشة إضافة رمز إحصائي جديد
     */
/**
 * عرض جدول السجلات الإحصائية
 */
public function index(Request $request)
{
    // التقاط قيمة البحث إن وجدت
    $search = $request->input('search');

    // بناء الاستعلام مع العلاقات
    $registers = StatisticalRegister::with('company')
        ->when($search, function ($query) use ($search) {
            $query->where('statistical_code', 'LIKE', "%{$search}%")
                  ->orWhereHas('company', function ($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
        })
        ->latest()
        ->get();

    return view('companies.statistical.index', compact('registers'));
}

    public function create()
    {
        // جلب جميع الشركات لكي تظهر في القائمة المنسدلة للاختيار منها
        $companies = Company::all();
        
        return view('companies.statistical.create', compact('companies'));
    }

    /**
     * حفظ بيانات الرمز الإحصائي في قاعدة البيانات
     */
    public function store(Request $request)
    {
        // 1. التحقق من صحة البيانات المدخلة (Validation)
        $request->validate([
            'company_id'       => 'required|exists:companies,id',
            'statistical_code' => 'required|string|max:255',
            'issue_date'       => 'required|date',
            'duration'         => 'required|string',
            'attachment'       => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048', // كحد أقصى 2 ميجابايت
        ], [
            // رسائل التنبيه باللغة العربية (اختياري حسب رغبتك)
            'company_id.required'       => 'يرجى اختيار الشركة.',
            'statistical_code.required' => 'يرجى إدخال رقم الرمز الإحصائي.',
            'issue_date.required'       => 'يرجى تحديد تاريخ الإصدار.',
            'duration'                  => 'يرجى اختيار مدة الصلاحية.',
        ]);

        // 2. معالجة وتجهيز تاريخ الانتهاء تلقائياً بناءً على تاريخ الإصدار والمدة
        $issueDate = \Carbon\Carbon::parse($request->issue_date);
        $expiryDate = match ($request->duration) {
            'سنة واحدة' => $issueDate->copy()->addYear(),
            'سنتين'    => $issueDate->copy()->addYears(2),
            '3 سنوات'  => $issueDate->copy()->addYears(3),
            default    => $issueDate->copy()->addYear(), // القيمة الافتراضية في حال اختلف النص
        };

        // 3. معالجة رفع الملف المرفق (في حال تم إرفاقه)
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            // تخزين الملف داخل مجلد مخصص في الـ Storage وتسميته بشكل منظم
            $attachmentPath = $request->file('attachment')->store('attachments/statistical', 'public');
        }

        // 4. إدخال البيانات في قاعدة البيانات عن طريق الموديل
        StatisticalRegister::create([
            'company_id'       => $request->company_id,
            'statistical_code' => $request->statistical_code,
            'issue_date'       => $request->issue_date,
            'duration'         => $request->duration,
            'expiry_date'      => $expiryDate->format('Y-m-d'), // الحفظ التلقائي لتاريخ الانتهاء
            'attachment'       => $attachmentPath,
        ]);

        // 5. إعادة التوجيه مع رسالة نجاح
        return redirect()->route('statistical.create')
                         ->with('success', 'تم حفظ بيانات الرمز الإحصائي بنجاح.');
    }

    /**
 * عرض شاشة تعديل الرمز الإحصائي
 */
public function edit($id)
{
    // جلب السجل المطلوب تعديله أو إظهار خطأ 404 إذا لم يكن موجوداً
    $register = StatisticalRegister::findOrFail($id);
    
    // جلب الشركات لكي تظهر في القائمة المنسدلة
    $companies = Company::all();

    return view('companies.statistical.edit', compact('register', 'companies'));
}

/**
 * تحديث البيانات في قاعدة البيانات
 */
public function update(Request $request, $id)
{
    $register = StatisticalRegister::findOrFail($id);

    // التحقق من صحة البيانات
    $request->validate([
        'company_id'       => 'required|exists:companies,id',
        'statistical_code' => 'required|string|max:255',
        'issue_date'       => 'required|date',
        'duration'         => 'required|string',
        'attachment'       => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
    ]);

    // إعادة احتساب تاريخ الانتهاء تلقائياً بناءً على تاريخ الإصدار الجديد والمدة
    $issueDate = \Carbon\Carbon::parse($request->issue_date);
    $expiryDate = match ($request->duration) {
        'سنة واحدة' => $issueDate->copy()->addYear(),
        'سنتين'    => $issueDate->copy()->addYears(2),
        '3 سنوات'  => $issueDate->copy()->addYears(3),
        default    => $issueDate->copy()->addYear(),
    };

    // معالجة الملف المرفق الجديد (إذا قام المستخدم برفعه)
    $attachmentPath = $register->attachment; // الاحتفاظ بالملف القديم كقيمة افتراضية
    if ($request->hasFile('attachment')) {
        // حذف الملف القديم من السيرفر لتوفير المساحة
        if ($register->attachment) {
            Storage::disk('public')->delete($register->attachment);
        }
        // تخزين الملف الجديد
        $attachmentPath = $request->file('attachment')->store('attachments/statistical', 'public');
    }

    // تحديث البيانات في الجدول
    $register->update([
        'company_id'       => $request->company_id,
        'statistical_code' => $request->statistical_code,
        'issue_date'       => $request->issue_date,
        'duration'         => $request->duration,
        'expiry_date'      => $expiryDate->format('Y-m-d'),
        'attachment'       => $attachmentPath,
    ]);

    return redirect()->route('statistical.index')
                     ->with('success', 'تم تحديث بيانات الرمز الإحصائي بنجاح.');
}
/**
 * عرض شاشة تجديد الرمز الإحصائي
 */
public function renew($id)
{
    // جلب السجل الحالي للتجديد بناءً عليه
    $register = StatisticalRegister::with('company')->findOrFail($id);
    
    return view('companies.statistical.renew', compact('register'));
}

/**
 * معالجة طلب التجديد وتحديث التواريخ والمرفقات
 */
public function processRenew(Request $request, $id)
{
    $register = StatisticalRegister::findOrFail($id);

    // التحقق من التواريخ الجديدة والمرفق الجديد للتجديد
    $request->validate([
        'issue_date' => 'required|date',
        'duration'   => 'required|string',
        'attachment' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
    ], [
        'issue_date.required' => 'يرجى تحديد تاريخ الإصدار الجديد للتجديد.',
    ]);

    // حساب تاريخ الانتهاء الجديد بناءً على المدة المحددة
    $issueDate = \Carbon\Carbon::parse($request->issue_date);
    $expiryDate = match ($request->duration) {
        'سنة واحدة' => $issueDate->copy()->addYear(),
        'سنتين'    => $issueDate->copy()->addYears(2),
        '3 سنوات'  => $issueDate->copy()->addYears(3),
        default    => $issueDate->copy()->addYear(),
    };

    // رفع ملف التجديد الجديد إن وجد
    $attachmentPath = $register->attachment;
    if ($request->hasFile('attachment')) {
        // حذف الملف القديم المرتبط بالصلاحية السابقة لمنع تراكم الملفات غير المستخدمة
        if ($register->attachment) {
            Storage::disk('public')->delete($register->attachment);
        }
        $attachmentPath = $request->file('attachment')->store('attachments/statistical', 'public');
    }

    // تحديث السجل ببيانات التجديد الجديدة
    $register->update([
        'issue_date'  => $request->issue_date,
        'duration'    => $request->duration,
        'expiry_date' => $expiryDate->format('Y-m-d'),
        'attachment'  => $attachmentPath,
    ]);

    return redirect()->route('statistical.index')
                     ->with('success', 'تم تجديد الرمز الإحصائي وتمديد صلاحيته بنجاح.');
}

/**
 * حذف السجل الإحصائي نهائياً مع ملفه المرفق
 */
public function destroy($id)
{
    $register = StatisticalRegister::findOrFail($id);

    // حذف الملف المرفق من السيرفر إذا كان موجوداً لتوفير المساحة
    if ($register->attachment) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($register->attachment);
    }

    // حذف السجل من قاعدة البيانات
    $register->delete();

    return redirect()->route('statistical.index')
                     ->with('success', 'تم حذف سجل الرمز الإحصائي والمرفق التابع له بنجاح.');
}
public function show($id)
{
    // جلب الشركة مع كافة سجلاتها بما فيها الرمز الإحصائي الجديد
    $company = Company::with('statisticalRegisters')->findOrFail($id);
    
    return view('companies.show', compact('company'));
}
}