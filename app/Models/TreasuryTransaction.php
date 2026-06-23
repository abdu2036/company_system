<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// استدعاء الموديلات الصحيحة والمنبثقة من هيكلية مشروعك الفعلي
use App\Models\Revenue; 
use App\Models\CompanyExpense; 

class TreasuryTransaction extends Model
{
    use HasFactory;

    protected $table = 'treasury_transactions';

    protected $fillable = [
        'type', 
        'amount', 
        'delivered_by', 
        'received_by', 
        'notes', 
        'transaction_date'
    ];

    /**
     * الحسبة الذكية لصافي رصيد الخزينة الحالي:
     * (إجمالي الإيرادات التشغيلية + الإيداعات اليدوية) - (إجمالي المصروفات التشغيلية + سحوبات المدير)
     */
    public static function getCurrentBalance()
    {
        // 1. جلب إجمالي الإيرادات المسجلة "نقداً" فقط
        $totalCashRevenues = Revenue::where('payment_method', 'cash')->sum('amount'); 

        // 2. جلب إجمالي المصروفات المدفوعة "نقداً" فقط
        $totalCashExpenses = CompanyExpense::where('payment_method', 'cash')->sum('amount'); 

        // 3. جلب الحركات اليدوية المباشرة من جدول الخزينة
        $manualDeposits = self::where('type', 'deposit')->sum('amount');
        $managerWithdrawals = self::where('type', 'withdrawal')->sum('amount');

        // 4. المعادلة المحاسبية الصافية المتاحة ككاش في الصندوق
        $netCashBalance = ($totalCashRevenues + $manualDeposits) - ($totalCashExpenses + $managerWithdrawals);

        return $netCashBalance;
    }
   
}