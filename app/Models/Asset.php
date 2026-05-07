<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'company_id', 
        'asset_code', 
        'name', 
        'category', 
        'location', 
        'status', 
        'notes', 
        'purchase_price',
        'added_by_emp_id',     // أضفنا هذا لربط مدخل الأصل
        'received_by_emp_id',  // أضفنا هذا لربط مدير المخزن
        'received_at'          // أضفنا هذا لتسجيل وقت الاستلام
    ];
    
    // علاقة الشركة
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // علاقة سجلات الصيانة (إبقاء نسخة واحدة فقط وبشكل صحيح)
    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class, 'asset_id');
    }

    // علاقة لجلب بيانات المدير الذي استلم القطعة من نظام HRMS
    public function receiver()
    {
        // تأكد أن موديل Employee موجود ومربوط بقاعدة hrms_db
        return $this->belongsTo(\App\Models\Employee::class, 'received_by_emp_id');
    }
}