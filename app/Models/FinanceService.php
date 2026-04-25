<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceService extends Model
{
    use HasFactory;

    // تحديد الجدول إذا كان الاسم مختلفاً عن التوقعات الافتراضية
    protected $table = 'finance_services';

    // السماح بحفظ الحقول التالية
    protected $fillable = ['name'];
}