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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();

            // 1. ربط السجل بالشركة الأب
            // سيتم تخزين رقم تعريف الشركة هنا لربط الترخيص بها
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            // 2. رقم الترخيص العام
            $table->string('license_number')->nullable();;

            // 3. الرقم الضريبي للشركة
            $table->string('tax_number')->nullable();

            // 4. تاريخ إصدار الترخيص
            $table->date('issue_date')->nullable();;

            // 5. تاريخ انتهاء الترخيص (مهم جداً للتنبيهات مستقبلاً)
            $table->date('expiry_date')->nullable();

            // 6. مرفق مستند الترخيص
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
        Schema::dropIfExists('licenses');
    }
};
