<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndustrialRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'industrial_code',
        'issue_date',
        'duration',
        'expiry_date',
        'attachment',
    ];

    // تحويل التواريخ إلى كائنات Carbon لتسهيل المقارنة والتنسيق في Blade
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * علاقة السجل الصناعي بالشركة (كل سجل ينتمي لشركة واحدة)
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}