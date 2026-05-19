<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('industrial_registers', function (Blueprint $table) {
            $table->id();
            // ربط السجل الصناعي بجدول الشركات (عند حذف الشركة يحذف السجل تلقائياً)
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            
            $table->string('industrial_code'); // رقم السجل الصناعي
            $table->date('issue_date');        // تاريخ الإصدار
            $table->string('duration');        // مدة الصلاحية (سنة، سنتين...)
            $table->date('expiry_date');       // تاريخ الانتهاء المحسوب
            $table->string('attachment')->nullable(); // مسار ملف السجل الصناعي المرفق
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('industrial_registers');
    }
};