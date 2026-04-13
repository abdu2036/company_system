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
        Schema::create('importers', function (Blueprint $table) {
            $table->id();

            // 1. ربط السجل بالشركة الأب
            // نستخدم نفس المنطق لضمان أن كل بيانات المستوردين تابعة لشركة محددة
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            // 2. رقم قيد المستوردين
            $table->string('importer_number')->nullable();

            // 3. تاريخ إصدار سجل المستوردين
            $table->date('issue_date')->nullable();;

            // 4. تاريخ انتهاء صلاحية سجل المستوردين
            $table->date('expiry_date')->nullable();

            // 5. مرفق مستند سجل المستوردين
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
        Schema::dropIfExists('importers');
    }
};
