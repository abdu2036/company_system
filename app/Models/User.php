<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; 

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    // 1. القاعدة الأساسية يجب أن تكون mysql (التي تحتوي على الصلاحيات)
    protected $connection = 'mysql'; 
    protected $table = 'users';

    // 2. تحديد الـ Guard ليتوافق مع جداول Spatie
    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * جلب بيانات الموظف من قاعدة بيانات hrms_db
     */
   /**
 * جلب بيانات الموظف من قاعدة بيانات hrms_db
 */
public function employee()
{
    // لارافيل سيستخدم الاتصال المحدد داخل موديل Employee تلقائياً
    return $this->belongsTo(Employee::class, 'employee_id');
}

    // ملاحظة: لا تضع دالة roles() يدوياً، الـ Trait (HasRoles) سيقوم بالواجب تلقائياً
}