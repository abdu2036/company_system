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
        Schema::create('statistical_registers', function (Blueprint $table) {
            $table->id();
            
            // ربط السجل الإحصائي بجدول الشركات مع ميزة الحذف التلقائي عند حذف الشركة
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade');
            
            // رقم الرمز الإحصائي
            $table->string('statistical_code'); 
            
            // تاريخ الإصدار
            $table->date('issue_date'); 
            
            // مدة الصلاحية (مثال: سنة واحدة، سنتين، 3 سنوات)
            $table->string('duration'); 
            
            // تاريخ الانتهاء
            $table->date('expiry_date'); 
            
            // ملف الرمز الإحصائي (PDF أو صورة) ويكون اختياري
            $table->string('attachment')->nullable(); 
            
            // طوابع الوقت لإنشاء وتحديث السجل
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistical_registers');
    }
};