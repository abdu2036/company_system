<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ExpenseCategory;
use App\Models\CompanyExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyExpenseController extends Controller
{
    /**
     * 4️⃣ الفهرس العام وسجلات مصروفات الشركات
     */
    public function index()
    {
        // استدعاء الشركات مع حساب مجموع مصروفاتها تلقائياً لدعم الـ Pagination بنفس أسلوب نظامك
        $companies = Company::with(['commercialRegister'])
            ->withSum('expenses as expenses_sum_amount', 'amount')
            ->paginate(10);

        return view('companies.expenses.index', compact('companies'));
    }

    /**
     * 5️⃣ صفحة تسجيل حركة مصروفات جديدة لشركة محددة (يدعم واجهة الأسطر المتعددة)
     */
    public function create(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        // جلب بيانات الشركة وبنود المصاريف المخصصة التابعة لها
        $company = Company::with('expenseCategories')->findOrFail($request->company_id);

        return view('companies.expenses.create', compact('company'));
    }

    /**
     * 6️⃣ صفحة تاريخ حركات المصروفات المخصصة بالشهر والسنة (مع احتساب إجمالي الفلترة)
     */
    public function history(Request $request, $company_id)
    {
        $company = Company::findOrFail($company_id);
        
        // بناء جملة الاستعلام الأساسية
        $query = CompanyExpense::with('category')
            ->where('company_id', $company_id);

        // 1. الفرز عن طريق البحث الشامل
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }

        // 2. الفرز حسب الشهر المختار (إذا لم يُختر شهر، يتم افتراض الشهر الحالي تلقائياً)
        $selectedMonth = $request->input('month', date('m'));
        if ($request->filled('month') || !$request->has('search')) {
            $query->whereMonth('expense_date', $selectedMonth);
        }

        // 3. الفرز حسب السنة المختارة (إذا لم تُختر سنة، يتم افتراض السنة الحالية)
        $selectedYear = $request->input('year', date('Y'));
        if ($request->filled('year') || !$request->has('search')) {
            $query->whereYear('expense_date', $selectedYear);
        }

        // جلب البيانات المصفاة
        $expenses = $query->orderBy('expense_date', 'desc')->get();

        // 🧮 حساب إجمالي المصروفات للشهر والسنة المحددين فقط في الفلترة الحالية
        $monthly_total = $expenses->sum('amount');

        return view('companies.expenses.history', compact('company', 'expenses', 'monthly_total', 'selectedMonth', 'selectedYear'));
    }

    /**
     * 1️⃣ حفظ بند مصروفات جديد خاص بالشركة
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name'       => 'required|string|max:255',
        ]);

        ExpenseCategory::create([
            'company_id' => $request->company_id,
            'name'       => $request->name,
        ]);

        return redirect()->back()->with('success', 'تم إضافة بند المصروفات بنجاح ✅');
    }

    /**
     * 2️⃣ المطور: حفظ مجموعة من الفواتير والمصروفات (الأسطر المتعددة) دفعة واحدة مع المرفق
     */
  /**
     * 2️⃣ المطور: حفظ مجموعة من الفواتير والمصروفات (الأسطر المتعددة) دفعة واحدة مع المرفق
     */
    public function storeMultiple(Request $request)
    {
        // التحقق من صحة المصفوفة والبيانات التكرارية القادمة من الواجهة
        $request->validate([
            'company_id'                  => 'required|exists:companies,id',
            'expenses'                    => 'required|array|min:1',
            'expenses.*.category_id'      => 'required|exists:expense_categories,id',
            'expenses.*.invoice_number'   => 'nullable|string|max:255',
            'expenses.*.expense_date'     => 'required|date',
            'expenses.*.amount'           => 'required|numeric|min:0',
            'expenses.*.notes'            => 'nullable|string',
        ]);

        $documentPath = null;

        // 📁 معالجة ونقل الملف المرفق (إذا تم رفعه عبر حقل الرفع الموحد في الأسفل)
        if ($request->hasFile('expense_document')) {
            $file = $request->file('expense_document');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('assets/admin/uploads/expenses/');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            
            $file->move($destinationPath, $fileName);
            $documentPath = 'assets/admin/uploads/expenses/' . $fileName;
        } 
        // دعم قراءة الملف المؤقت القديم إذا كنت تعتمد على نظام رفع مؤقت بـ Dropzone أو الـ JavaScript الخاص بك
        elseif ($request->filled('temp_file_path')) {
            $tempPath = public_path($request->temp_file_path);
            if (file_exists($tempPath)) {
                $fileName = basename($tempPath);
                $finalPath = 'assets/admin/uploads/expenses/' . $fileName;

                if (!file_exists(public_path('assets/admin/uploads/expenses/'))) {
                    mkdir(public_path('assets/admin/uploads/expenses/'), 0777, true);
                }

                rename($tempPath, public_path($finalPath));
                $documentPath = $finalPath;
            }
        }

        // 🔑 توليد كود فريد ومشترك لعملية الإدخال الحالية بالكامل لربط البنود الثلاثة معاً
        $transactionCode = 'TRX-' . strtoupper(uniqid());

        // تنفيذ الحفظ الآمن داخل سياق Transaction (إما حفظ الكل أو إلغاء العملية كاملة عند حدوث خطأ مالي)
        DB::transaction(function () use ($request, $documentPath, $transactionCode) {
            foreach ($request->expenses as $index => $expenseData) {
                CompanyExpense::create([
                    'company_id'       => $request->company_id,
                    'category_id'      => $expenseData['category_id'],
                    'invoice_number'   => $expenseData['invoice_number'],
                    'transaction_code' => $transactionCode, // 👈 تم إضافة حقن الكود المشترك هنا ليرتبطوا ببعض بقاعدة البيانات
                    'expense_date'     => $expenseData['expense_date'],
                    'amount'           => $expenseData['amount'],
                    'notes'            => $expenseData['notes'],
                    'document_path'    => $documentPath, 
                    'created_by'       => Auth::id(), // توثيق "سجل مراقبة العمليات للموظفين"
                ]);
            }
        });

        // التوجيه الذكي إلى صفحة سجل الحركات والتاريخ للشركة المشغلة مباشرة لتفقد الحركات المضافة
        return redirect()->route('expenses.history', $request->company_id)
                         ->with('success', 'تم تسجيل كافة القيود والمصروفات المالية بنجاح 💸');
    }

    /**
     * 3️⃣ المطور: حذف حركة مصروف مالي (متوافق مع مسمى دالة الـ Blade)
     */
  public function destroy(Request $request, $id)
{
    $expense = CompanyExpense::findOrFail($id);

    // إذا كانت الرغبة حذف مجموعة كاملة بناء على الكود التجميعي المرسل من الـ Blade
    if ($request->filled('delete_group_code')) {
        $groupExpenses = CompanyExpense::where('transaction_code', $request->delete_group_code)->get();
        foreach ($groupExpenses as $item) {
            if ($item->document_path && file_exists(public_path($item->document_path))) {
                @unlink(public_path($item->document_path));
            }
            $item->delete();
        }
        return redirect()->back()->with('success', 'تم حذف الفاتورة المجمعة بكافة بنودها بنجاح 🗑️');
    }

    // الحذف الفردي العادي في حال كان بنداً وحيداً
    if ($expense->document_path && file_exists(public_path($expense->document_path))) {
        @unlink(public_path($expense->document_path));
    }
    $expense->delete();

    return redirect()->back()->with('success', 'تم حذف السجل المالي للمصروف بنجاح 🗑️');
}
}