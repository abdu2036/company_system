<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatisticalRegister extends Model
{
    use HasFactory;

    /**
     * اسم الجدول في قاعدة البيانات (اختياري إذا كان مطابقاً لجمع اسم الموديل)
     */
    protected $table = 'statistical_registers';

    /**
     * الحقول المسموح بتعبئتها تلقائياً (Mass Assignment)
     */
    protected $fillable = [
        'company_id',
        'statistical_code', //
        'issue_date',       //
        'duration',         //
        'expiry_date',      //
        'attachment',       //
    ];

    /**
     * تحديد أنواع الحقول (Casting) لضمان التعامل مع التواريخ ككائنات Carbon مريحة في التنسيق
     */
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * علاقة ربط الرمز الإحصائي بالشركة التابع لها
     * كل سجل إحصائي ينتمي إلى شركة واحدة فقط
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}