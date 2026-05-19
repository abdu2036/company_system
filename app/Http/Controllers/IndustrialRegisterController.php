<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\IndustrialRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class IndustrialRegisterController extends Controller
{
    /**
     * عرض جدول السجلات الصناعية مع ميزة البحث الفوري
     */
    public function index(Request $request)
    {
        // التقاط قيمة البحث إن وجدت
        $search = $request->input('search');

        // بناء الاستعلام مع العلاقات والفلترة
        $registers = IndustrialRegister::with('company')
            ->when($search, function ($query) use ($search) {
                $query->where('industrial_code', 'LIKE', "%{$search}%")
                      ->orWhereHas('company', function ($q) use ($search) {
                          $q->where('name', 'LIKE', "%{$search}%");
                      });
            })
            ->latest()
            ->get();

        return view('companies.industrial.index', compact('registers'));
    }

    /**
     * عرض شاشة إضافة سجل صناعي جديد
     */
    public function create()
    {
        // جلب جميع الشركات لكي تظهر في القائمة المنسدلة للاختيار منها
        $companies = Company::all();
        
        return view('companies.industrial.create', compact('companies'));
    }

    /**
     * حفظ بيانات السجل الصناعي في قاعدة البيانات
     */
    public function store(Request $request)
    {
        // 1. التحقق من صحة البيانات المدخلة (Validation)
        $request->validate([
            'company_id'      => 'required|exists:companies,id',
            'industrial_code' => 'required|string|max:255',
            'issue_date'      => 'required|date',
            'duration'        => 'required|string',
            'attachment'      => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048', // كحد أقصى 2 ميجابايت
        ], [
            'company_id.required'      => 'يرجى اختيار الشركة.',
            'industrial_code.required' => 'يرجى إدخال رقم السجل الصناعي.',
            'issue_date.required'      => 'يرجى تحديد تاريخ الإصدار.',
            'duration.required'        => 'يرجى اختيار مدة الصلاحية.',
        ]);

        // 2. معالجة وتجهيز تاريخ الانتهاء تلقائياً بناءً على تاريخ الإصدار والمدة
        $issueDate = Carbon::parse($request->issue_date);
        $expiryDate = match ($request->duration) {
            'سنة واحدة' => $issueDate->copy()->addYear(),
            'سنتين'    => $issueDate->copy()->addYears(2),
            '3 سنوات'  => $issueDate->copy()->addYears(3),
            default    => $issueDate->copy()->addYear(),
        };

        // 3. معالجة رفع الملف المرفق داخل مجلد فرعي مخصص منظم
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments/industrial', 'public');
        }

        // 4. إدخال البيانات في قاعدة البيانات عن طريق الموديل
        IndustrialRegister::create([
            'company_id'      => $request->company_id,
            'industrial_code' => $request->industrial_code,
            'issue_date'      => $request->issue_date,
            'duration'        => $request->duration,
            'expiry_date'     => $expiryDate->format('Y-m-d'),
            'attachment'      => $attachmentPath,
        ]);

        // 5. إعادة التوجيه إلى القائمة الرئيسية مع رسالة نجاح
        return redirect()->route('industrial.index')
                         ->with('success', 'تم حفظ بيانات السجل الصناعي بنجاح.');
    }

    /**
     * عرض شاشة تعديل السجل الصناعي الحالي
     */
    public function edit($id)
    {
        $register = IndustrialRegister::findOrFail($id);
        $companies = Company::all();

        return view('companies.industrial.edit', compact('register', 'companies'));
    }

    /**
     * تحديث بيانات السجل الصناعي في قاعدة البيانات وحذف المستندات القديمة
     */
    public function update(Request $request, $id)
    {
        $register = IndustrialRegister::findOrFail($id);

        $request->validate([
            'company_id'      => 'required|exists:companies,id',
            'industrial_code' => 'required|string|max:255',
            'issue_date'      => 'required|date',
            'duration'        => 'required|string',
            'attachment'      => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
        ]);

        // إعادة احتساب تاريخ الانتهاء تلقائياً
        $issueDate = Carbon::parse($request->issue_date);
        $expiryDate = match ($request->duration) {
            'سنة واحدة' => $issueDate->copy()->addYear(),
            'سنتين'    => $issueDate->copy()->addYears(2),
            '3 سنوات'  => $issueDate->copy()->addYears(3),
            default    => $issueDate->copy()->addYear(),
        };

        // معالجة الملف المرفق الجديد مع تنظيف السيرفر من الملف القديم
        $attachmentPath = $register->attachment;
        if ($request->hasFile('attachment')) {
            if ($register->attachment) {
                Storage::disk('public')->delete($register->attachment);
            }
            $attachmentPath = $request->file('attachment')->store('attachments/industrial', 'public');
        }

        $register->update([
            'company_id'      => $request->company_id,
            'industrial_code' => $request->industrial_code,
            'issue_date'      => $request->issue_date,
            'duration'        => $request->duration,
            'expiry_date'     => $expiryDate->format('Y-m-d'),
            'attachment'      => $attachmentPath,
        ]);

        return redirect()->route('industrial.index')
                         ->with('success', 'تم تحديث بيانات السجل الصناعي بنجاح.');
    }

    /**
     * عرض شاشة تجديد الصلاحية للسجل الصناعي
     */
    public function renew($id)
    {
        $register = IndustrialRegister::with('company')->findOrFail($id);
        
        return view('companies.industrial.renew', compact('register'));
    }

    /**
     * معالجة طلب التجديد وحفظ التواريخ والمرفقات الجديدة
     */
    public function processRenew(Request $request, $id)
    {
        $register = IndustrialRegister::findOrFail($id);

        $request->validate([
            'issue_date' => 'required|date',
            'duration'   => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
        ], [
            'issue_date.required' => 'يرجى تحديد تاريخ الإصدار الجديد للتجديد.',
        ]);

        // حساب تاريخ الانتهاء المستقبلي الجديد
        $issueDate = Carbon::parse($request->issue_date);
        $expiryDate = match ($request->duration) {
            'سنة واحدة' => $issueDate->copy()->addYear(),
            'سنتين'    => $issueDate->copy()->addYears(2),
            '3 سنوات'  => $issueDate->copy()->addYears(3),
            default    => $issueDate->copy()->addYear(),
        };

        // رفع مستند التجديد مع مسح النسخة القديمة المنتهية
        $attachmentPath = $register->attachment;
        if ($request->hasFile('attachment')) {
            if ($register->attachment) {
                Storage::disk('public')->delete($register->attachment);
            }
            $attachmentPath = $request->file('attachment')->store('attachments/industrial', 'public');
        }

        $register->update([
            'issue_date'  => $request->issue_date,
            'duration'    => $request->duration,
            'expiry_date' => $expiryDate->format('Y-m-d'),
            'attachment'  => $attachmentPath,
        ]);

        return redirect()->route('industrial.index')
                         ->with('success', 'تم تجديد السجل الصناعي وتمديد صلاحيته بنجاح.');
    }

    /**
     * حذف السجل الصناعي نهائياً ومسح مستنده المرفق من وحدة التخزين
     */
    public function destroy($id)
    {
        $register = IndustrialRegister::findOrFail($id);

        if ($register->attachment) {
            Storage::disk('public')->delete($register->attachment);
        }

        $register->delete();

        return redirect()->route('industrial.index')
                         ->with('success', 'تم حذف سجل السجل الصناعي والمرفق التابع له بنجاح.');
    }

    /**
     * اختياري: عرض بيانات الشركة متضمنة السجلات الصناعية
     */
    public function show($id)
    {
        $company = Company::with('industrialRegisters')->findOrFail($id);
        
        return view('companies.show', compact('company'));
    }
}