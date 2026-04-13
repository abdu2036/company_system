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
        Schema::create('commercial_registers', function (Blueprint $table) {
            $table->id();

            // 1. الربط بجدول الشركات: هذا الحقل يخزن ID الشركة من الجدول السابق
            // constrained تخبر لارفيل أن هذا الحقل "مفتاح أجنبي" مرتبط بجدول companies
            // onDelete('cascade') تعني إذا حُذفت الشركة، يتم حذف سجلها التجاري تلقائياً
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            // 2. رقم السجل التجاري (فريد بحيث لا يتكرر لنفس السجل)
            $table->string('cr_number')->unique();

            // 3. اسم المفوض (الشخص المسؤول المسجل في السجل)
            $table->string('representative_name');

            // 4. رقم الهاتف الخاص بالسجل
            $table->string('phone');

            // 5. تاريخ إصدار السجل التجاري
            $table->date('issue_date');

            // 6. تاريخ انتهاء السجل التجاري
            $table->date('expiry_date');

            // 7. مسار ملف السجل (المرفق) - نجعله nullable تحسباً لعدم رفعه فوراً
            $table->string('document_path')->nullable();
            // أضف هذه الأسطر هنا
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_registers');
    }
};
