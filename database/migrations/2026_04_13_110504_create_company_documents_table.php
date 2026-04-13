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
        Schema::create('company_documents', function (Blueprint $table) {
            $table->id();
            
            // ربط المستند بشركة معينة (عند حذف الشركة تُحذف مستنداتها تلقائياً)
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade');

            // البيانات الأساسية للمستند
            $table->string('document_name');      // اسم المستند (عقد إيجار، تفويض، إلخ)
            $table->string('file_path');          // مسار تخزين الملف في السيرفر
            $table->string('file_extension');     // نوع الملف (pdf, png, jpg)
            $table->string('file_size')->nullable(); // حجم الملف (مثلاً: 2MB)
            
            // تصنيف المستند
            // نستخدم هذا الحقل لنميز هل هو (سجل تجاري، رخصة) أم (مستند إضافي)
            $table->string('document_type')->default('additional'); 

            // الحقل الذي طلبته للملاحظات
            $table->text('notes')->nullable(); 

            // تواريخ الرفع والتحديث
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_documents');
    }
};
