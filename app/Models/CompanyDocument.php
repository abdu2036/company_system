<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDocument extends Model
{
    // الحقول المسموح بتعبئتها
    protected $fillable = [
        'company_id', 
        'document_name', 
        'file_path', 
        'file_extension', 
        'file_size', 
        'document_type', 
        'notes'
    ];

    // علاقة عكسية: المستند ينتمي لشركة واحدة
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}