<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة هذا الشرط يمنع ظهور خطأ "Table already exists"
        if (!Schema::hasTable('revenues')) {
            Schema::create('revenues', function (Blueprint $table) {
                $table->id();
                
                // ربط الحركة بالشركة
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                
                // ربط الحركة ببند أو تصنيف الإيراد
                // تأكد أن جدول revenue_categories موجود مسبقاً في قاعدة البيانات
                $table->foreignId('category_id')->constrained('revenue_categories')->onDelete('cascade');
                
                // القيمة المالية للحركة
                $table->decimal('amount', 15, 2);
                
                // تاريخ حركة القبض
                $table->date('revenue_date');
                
                // كود الحركة المشترك للتجميع
                $table->string('transaction_code')->nullable();
                
                // مسار المرفق أو الإيصال المصور
                $table->string('document_path')->nullable();
                
                // البيان أو ملاحظات الحركة
                $table->text('notes')->nullable();
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
};