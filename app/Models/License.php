<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class License extends Model
{
    protected $fillable = [
        'company_id', 
        'license_number', 
        'tax_number', 
        'issue_date', 
        'expiry_date', 
        'document_path',
        'created_by', 
        'updated_by'
    ];

    public function company(): BelongsTo {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }
    
}