<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'maintenance_type',
        'cost',
        'details',
        'start_date',
        'end_date'
    ];

    // علاقة عكسية لجلب بيانات الأصل من سجل الصيانة
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}