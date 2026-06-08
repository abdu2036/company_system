<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueCategory extends Model
{
    use HasFactory;

    protected $table = 'revenue_categories';

    protected $fillable = ['name'];

    /**
     * علاقة التصنيف بالإيرادات: التصنيف الواحد يحتوي على عدة حركات إيرادات
     */
    public function revenues()
    {
        return $this->hasMany(Revenue::class, 'category_id');
    }
}