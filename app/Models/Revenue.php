<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;

    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'revenues';

    // الحقول المسموح بتعبئتها جماعياً
    protected $fillable = [
        'company_id',
        'category_id',
        'amount',
        'revenue_date',
        'transaction_code',
        'document_path',
        'notes',
    ];

    /**
     * علاقة الإيراد بالشركة: كل إيراد ينتمي لشركة واحدة
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * علاقة الإيراد بالتصنيف/البند: كل حركة إيراد تنتمي لبند معين
     * (تأكد من اسم الموديل الخاص بتصنيفات الإيراد لديك، هنا افترضت أن اسمه RevenueCategory)
     */
    public function category()
    {
        return $this->belongsTo(RevenueCategory::class, 'category_id');
    }
}