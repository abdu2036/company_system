<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Revenue;
use App\Models\RevenueCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class RevenueController extends Controller
{
    /**
     * عرض الصفحة الرئيسية للإيرادات (قائمة الشركات)
     *//**
 * عرض الصفحة الرئيسية للإيرادات (قائمة الشركات)
 */
public function index()
{
    // جلب الشركات مع حساب مجموع مبالغ الإيرادات لكل شركة مع تفعيل الـ Pagination
    $companies = Company::withSum('revenues', 'amount')->paginate(10);
    
    // استدعاء ملف الـ Blade من المسار الخاص بك
    return view('companies.revenues.index', compact('companies'));
}

    /**
     * عرض صفحة تاريخ حركات الإيرادات المحدثة والبحث المتقدم للشركة
     */
    public function history(Request $request, $company_id)
    {
        // جلب الشركة الحالية أو إظهار خطأ 404 إذا كانت غير موجودة
        $company = Company::findOrFail($company_id);

        // التقاط قيم الفلترة من الرابط (أو تحديد الشهر والسنة الحاليين كقيم افتراضية)
        $selectedMonth = $request->input('month', date('m'));
        $selectedYear = $request->input('year', date('Y'));
        $search = $request->input('search');

        // بناء الاستعلام المالي للإيرادات الخاصة بهذه الشركة
        $query = Revenue::with('category')
            ->where('company_id', $company->id)
            ->whereYear('revenue_date', $selectedYear)
            ->whereMonth('revenue_date', $selectedMonth);

        // تطبيق محرك البحث الشامل إذا قام المستخدم بكتابة نص بحث
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%")
                  ->orWhere('amount', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function ($catQ) use ($search) {
                      $catQ->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // جلب البيانات مرتبة من الأحدث إلى الأقدم
        $revenues = $query->orderBy('revenue_date', 'desc')->get();

        // حساب إجمالي الإيرادات للفترة المحددة ليظهر في الكرت العلوي
        $monthly_total = $revenues->sum('amount');

        // إرسال المتغيرات إلى صفحة الـ Blade
        return view('companies.revenues.history', compact(
            'company',
            'revenues',
            'selectedMonth',
            'selectedYear',
            'monthly_total'
        ));
    }

    /**
     * عرض صفحة إضافة حركة إيراد جديدة (سواء منفردة أو متعددة)
     */
    public function create(Request $request)
    {
        $company_id = $request->input('company_id');
        $company = $company_id ? Company::find($company_id) : null;
        $companies = Company::all();
        $categories = RevenueCategory::all();

        return view('companies.revenues.create', compact('company', 'companies', 'categories'));
    }

    /**
     * حفظ حركة إيراد منفردة عادية
     */
    public function storeRevenue(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'required|exists:revenue_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'revenue_date' => 'required|date',
            'document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:4096',
            'notes' => 'nullable|string',
        ]);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = 'rev_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/revenues'), $filename);
            $documentPath = 'uploads/revenues/' . $filename;
        }

        // توليد كود حركة فريد لكل عملية منفردة لتوحيد النظام
        $transactionCode = 'REV-SINGLE-' . strtoupper(uniqid());

        Revenue::create([
            'company_id' => $request->company_id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'revenue_date' => $request->revenue_date,
            'transaction_code' => $transactionCode,
            'document_path' => $documentPath,
            'notes' => $request->notes,
        ]);

        return redirect()->route('revenues.history', $request->company_id)
            ->with('success', 'تم تسجيل حركة الإيراد بنجاح.');
    }

    /**
     * حفظ حركات إيرادات مجمعة متعددة البنود (نفس فكرة storeMultiple في المصروفات)
     */
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'revenue_date' => 'required|date',
            'document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:4096',
            'categories' => 'required|array',
            'categories.*' => 'required|exists:revenue_categories,id',
            'amounts' => 'required|array',
            'amounts.*' => 'required|numeric|min:0.01',
            'notes' => 'required|array',
        ]);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = 'rev_group_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/revenues'), $filename);
            $documentPath = 'uploads/revenues/' . $filename;
        }

        // توليد كود حركة مجمع مشترك يربط السطور ببعضها
        $groupTransactionCode = 'REV-GRP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        foreach ($request->categories as $index => $categoryId) {
            Revenue::create([
                'company_id' => $request->company_id,
                'category_id' => $categoryId,
                'amount' => $request->amounts[$index],
                'revenue_date' => $request->revenue_date,
                'transaction_code' => $groupTransactionCode,
                'document_path' => $documentPath, // نفس الملف المرفق يربط بالجميع
                'notes' => $request->notes[$index] ?? null,
            ]);
        }

        return redirect()->route('revenues.history', $request->company_id)
            ->with('success', 'تم حفظ إيصال الإيرادات المجمع بكافة بنوده بنجاح.');
    }

    /**
     * إضافة بند/تصنيف إيراد جديد سريعاً عبر الجافاسكريبت أو المودال
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:revenue_categories,name',
        ]);

        $category = RevenueCategory::create([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'category' => $category
        ]);
    }

    /**
     * حذف حركة إيراد (منفردة أو حذف كامل الفاتورة المجمعة بناءً على كود الحركة المشترك)
     */
    public function destroy(Request $request, $id)
    {
        // التحقق مما إذا كان الطلب يحتوي على كود تجميعي لحذف الفاتورة المجمعة بالكامل
        if ($request->has('delete_group_code') && !empty($request->delete_group_code)) {
            
            // جلب كافة السجلات المشمولة تحت هذا الكود لحذف ملفاتهم المرفقة أولاً
            $groupRevenues = Revenue::where('transaction_code', $request->delete_group_code)->get();
            foreach ($groupRevenues as $rev) {
                if ($rev->document_path && file_exists(public_path($rev->document_path))) {
                    @unlink(public_path($rev->document_path));
                }
            }

            // حذف كافة الأسطر من قاعدة البيانات دفعة واحدة
            Revenue::where('transaction_code', $request->delete_group_code)->delete();

            return redirect()->back()->with('success', 'تم حذف إيصال الإيرادات المجمع بكافة بنوده بنجاح.');
        }

        // في حالة الحذف المنفرد (سطر واحد فقط)
        $revenue = Revenue::findOrFail($id);

        // حذف الملف المرفق إذا وجد
        if ($revenue->document_path && file_exists(public_path($revenue->document_path))) {
            @unlink(public_path($revenue->document_path));
        }

        $revenue->delete();

        return redirect()->back()->with('success', 'تم حذف قيد الإيراد المالي بنجاح.');
    }
}