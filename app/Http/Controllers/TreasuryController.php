<?php

namespace App\Http\Controllers;

use App\Models\TreasuryTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TreasuryController extends Controller
{
    /**
     * عرض الصفحة الرئيسية للخزينة (كشف الحركات الماليّة)
     */
    public function index()
    {
        // جلب رصيد الخزينة الحالي باستخدام الدالة التي وضعناها في الموديل
        $currentBalance = TreasuryTransaction::getCurrentBalance();

        // جلب الحركات مرتبة من الأحدث إلى الأقدم
        $transactions = TreasuryTransaction::orderBy('transaction_date', 'desc')
                                            ->orderBy('created_at', 'desc')
                                            ->paginate(15);

        return view('companies.treasury.index', compact('currentBalance', 'transactions'));
    }

    /**
     * معالجة عملية إيداع سيولة في الخزينة (مثل استلام إيراد نقدي أو تحويل)
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'delivered_by' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        TreasuryTransaction::create([
            'type' => 'deposit',
            'amount' => $request->amount,
            'delivered_by' => $request->delivered_by,
            'received_by' => 'الخزينة الرئيسية', // المستلم هنا هو الخزينة نفسها
            'notes' => $request->notes,
            'transaction_date' => Carbon::now()->toDateString(),
        ]);

        return redirect()->back()->with('success', 'تم إيداع المبلغ في الخزينة بنجاح.');
    }

    /**
     * معالجة عملية سحب سيولة من الخزينة لمدير الشركة أو الإدارة
     */
    /**
     * معالجة عملية سحب سيولة من الخزينة للمدير
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'received_by' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // جلب صافي القيمة الحالي (بعد حساب الإيرادات والمصروفات والسحوبات السابقة)
        $currentBalance = TreasuryTransaction::getCurrentBalance();
        
        // منع السحب إذا كان المبلغ أكبر من الصافي المتوفر فعلياً في درج الخزينة
        if ($request->amount > $currentBalance) {
            return redirect()->back()->with('error', 'فشلت العملية! المبلغ المراد سحبه أكبر من صافي السيولة المتوفرة حالياً.');
        }

        TreasuryTransaction::create([
            'type' => 'withdrawal',
            'amount' => $request->amount,
            'delivered_by' => auth()->user()->name ?? 'مسؤول الخزينة',
            'received_by' => $request->received_by,
            'notes' => $request->notes,
            'transaction_date' => now()->toDateString(),
        ]);

        return redirect()->back()->with('success', 'تم سحب المبلغ وتحديث صافي رصيد الخزينة بنجاح.');
    }

     public function printReceipt($id)
    {
        $transaction = TreasuryTransaction::findOrFail($id);
        return view('companies.treasury.print_receipt', compact('transaction'));
    }
}