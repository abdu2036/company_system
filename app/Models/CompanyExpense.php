<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyExpense extends Model
{
    protected $fillable = [
        'company_id', 
        'category_id', 
        'invoice_number',   // 👈 تم إضافته ليعمل حقل "رقم الفاتورة" بشكل سليم
        'transaction_code', // 👈 تم إضافته لربط وتجميع البنود المتعددة للفاتورة الواحدة
        'amount', 
        'expense_date', 
        'notes', 
        'document_path', 
        'created_by'
    ];

    // علاقة المصروف بالشركة
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // علاقة المصروف بالبند المخصص له
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    // علاقة المصروف بالموظف الذي قام بإدخاله لتوثيق النظام
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}