<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\FinanceService;
use App\Models\Invoice;
use Illuminate\Http\Request;


class FinanceController extends Controller
{
    // عرض الشركات لاختيار شركة لفتح حسابها
 // عرض الشركات لاختيار شركة لفتح حسابها
public function index() {
    // جلب الشركات مع حساب مجموع العمود remaining_amount من جدول finances لكل شركة
    $companies = Company::withSum('finances as total_debt', 'remaining_amount')
        ->latest()
        ->paginate(10);
    
    // إرسال البيانات للمجلد الصحيح حسب مسارك
    return view('companies.finance.index', compact('companies'));
}

    // فتح صفحة "الجدول المالي" لشركة معينة
public function create($company_id) {
    $company = Company::findOrFail($company_id);
    $services = FinanceService::all(); 
    
    // تأكد من كتابة المسار كما هو في شجرة الملفات لديك
    return view('companies.finance.create', compact('company', 'services'));
}

    public function store(Request $request)
{
    // 1. حفظ رأس الفاتورة (الإجماليات)
    $invoice = Invoice::create([
        'company_id' => $request->company_id,
        'total_amount' => $request->total_amount,
        'paid_amount' => $request->paid_amount,
        'remaining_amount' => $request->remaining_amount,
        'created_by' => auth()->id(), // تسجيل من قام بالعملية
    ]);

    // 2. حفظ البنود الـ 16 التي أدخلها المستخدم (فقط التي تحتوي على مبالغ)
    foreach ($request->services as $item) {
        if (isset($item['price']) && $item['price'] > 0) {
            $invoice->items()->create([
                'service_name' => $item['name'],
                'action' => $item['action'],
                'quantity' => $item['quantity'] ?? 1,
                'price' => $item['price'],
                'notes' => $item['notes'],
            ]);
        }
    }

    return redirect()->route('finance.index')->with('success', 'تم حفظ السجل المالي بنجاح');
}

// عرض تفاصيل الفواتير لشركة معينة
public function show($company_id)
{
    $company = Company::findOrFail($company_id);
    // جلب كل الفواتير الخاصة بهذه الشركة مع البنود التابعة لها
    $invoices = Invoice::where('company_id', $company_id)->with('items')->latest()->get();

    return view('companies.finance.show', compact('company', 'invoices'));
}

public function updatePayment(Request $request, $id) {
    $invoice = Invoice::findOrFail($id);
    
    // إضافة المبلغ الجديد للواصل القديم
    $invoice->paid_amount += $request->new_payment;
    
    // إعادة حساب الباقي
    $invoice->remaining_amount = $invoice->total_amount - $invoice->paid_amount;
    
    $invoice->save();

    return back()->with('success', 'تم تحديث الدفعة المالية بنجاح');
}
// طباعة الفاتورة
public function printInvoice($id)
{
    // جلب الفاتورة مع بنودها وبيانات الشركة المرتبطة بها
    $invoice = Invoice::with(['items', 'company'])->findOrFail($id);

    return view('companies.finance.print', compact('invoice'));
}
}