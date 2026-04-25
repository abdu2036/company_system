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
    'notes', // تأكد من وجودها هنا
    'purchase_price'
];
    
    // تأكد أيضاً من وجود علاقة الشركة التي تحدثنا عنها
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // علاقة لجلب سجلات الصيانة المرتبطة بهذا الأصل
    public function maintenanceLogs()
{
    return $this->hasMany(MaintenanceLog::class);
}

}