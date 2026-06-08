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
      Schema::create('company_expenses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
    $table->foreignId('category_id')->constrained('expense_categories')->onDelete('cascade');
    $table->decimal('amount', 10, 2); // قيمة المصروف
    $table->date('expense_date'); // تاريخ حركة الصرف
    $table->text('notes')->nullable(); // ملاحظات تفصيلية
    $table->string('document_path')->nullable(); // مسار الإيصال المرفق
    $table->foreignId('created_by')->constrained('users'); // الموظف المسؤول لتوثيق الأمان
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_expenses');
    }
};
