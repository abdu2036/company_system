<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Company extends Model
{
    use HasFactory;
// داخل الكلاس Company
protected $fillable = ['name', 'activity', 'address', 'is_active', 'created_by', 'updated_by'];

// علاقة لمعرفة المستخدم الذي أنشأ الشركة
public function creator() {
    return $this->belongsTo(User::class, 'created_by');
}
// علاقة الشركة بالسجل التجاري
public function commercialRegister() {
    return $this->hasOne(CommercialRegister::class);
}

// علاقة الشركة بالترخيص
// ربط الشركة بالترخيص 📜
// في ملف Company.php
public function license()
{
    // أضفنا orderBy لضمان جلب آخر سجل تم إضافته للشركة
    return $this->hasOne(License::class, 'company_id')->latestOfMany();
}
// علاقة الشركة بالغرفة التجارية
public function chamber() {
    return $this->hasOne(Chamber::class);
}

// علاقة الشركة بسجل المستوردين
// ربط الشركة بسجل المستوردين 🚢
public function importer()
{
    // تأكد من اسم المودل الخاص بسجل المستوردين (مثلاً Importer)
    return $this->hasOne(Importer::class, 'company_id')->latestOfMany(); // جلب آخر سجل مستورد مرتبط بالشركة
}
// العلاقة الجديدة: تجلب آخر ترخيص مسجل للشركة

public function documents()
    {
        return $this->hasMany(CompanyDocument::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}



