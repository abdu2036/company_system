<?php

namespace App\Models;
use App\Models\InvoiceItem;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    // الحقول التي تظهر في الميجريشن الخاص بك
    protected $fillable = [
        'company_id', 
        'total_amount', 
        'paid_amount', 
        'remaining_amount', 
        'created_by'
    ];

    // علاقة الفاتورة بالبنود (Items)
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // علاقة الفاتورة بالشركة (لجلب اسم الشركة والمفوض)
public function company()
{
    return $this->belongsTo(Company::class);
}
}