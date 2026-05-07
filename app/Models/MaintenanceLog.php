<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// تأكد من وجود هذه السطور بدقة لإزالة الخطوط الحمراء
use App\Models\Asset; 
use App\Models\Employee;

class MaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'technician_id', // ضروري جداً لربط الفني المستلم
        'maintenance_type',
        'cost',
        'details',
        'start_date',
        'end_date'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function technician()
    {
        // الربط مع موديل الموظفين في hrms_db
        return $this->belongsTo(Employee::class, 'technician_id');
    }
}