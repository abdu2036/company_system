<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommercialRegister extends Model
{
    protected $fillable = [
        'company_id', 
        'cr_number', 
        'representative_name', 
        'phone', 
        'issue_date', 
        'expiry_date', 
        'document_path',
        'created_by', 
        'updated_by'
    ];

    // علاقة السجل بالشركة
    public function company(): BelongsTo {
        return $this->belongsTo(Company::class);
    }

    // علاقة السجل بالمستخدم الذي أنشأه
    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }
}