<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = ['company_id', 'name'];

    // علاقة البند بالشركة
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // علاقة البند بالحركات المالية للمصروفات
    public function expenses()
    {
        return $this->hasMany(CompanyExpense::class, 'category_id');
    }
}