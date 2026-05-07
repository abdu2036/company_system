<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // تأكد أن هذا الاسم معرف في config/database.php
    protected $connection = 'mysql_hrms'; 

    protected $table = 'employees';

    protected $fillable = [
        'full_name', 
        'employee_code',
        'user_id'
    ];

    // هذا السطر يحل مشكلة الدائرة (يجعل النظام يفهم أن name هو full_name)
    public function getNameAttribute()
    {
        return $this->full_name;
    }

    public function receivedAssets()
    {
        // بما أن الأصول في قاعدة بيانات أخرى، تأكد من كتابة اسم القاعدة قبل الجدول
        return $this->hasMany(Asset::class, 'received_by_emp_id');
    }
}