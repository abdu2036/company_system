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
        Schema::create('treasury_transactions', function (Blueprint $table) {
            $table->id();
            
            // نوع الحركة: 'deposit' للإيداع (مثل استلام الإيرادات) أو 'withdrawal' للسحب (مثل استلام المدير للسيولة)
            $table->string('type'); 
            
            // قيمة المبلغ المالي المستلم أو المسحوب
            $table->decimal('amount', 15, 2); 
            
            // الشخص الذي قام بتسليم القيمة إلى الخزينة (اسم الموظف أو الحساب المالي)
            $table->string('delivered_by')->nullable(); 
            
            // الشخص الذي استلم القيمة من الخزينة (مثل اسم مدير الشركة أو الإدارة)
            $table->string('received_by')->nullable(); 
            
            // تفاصيل وبيان الحركة (مثال: "تسليم السيولة اليومية لمدير الشركة تصفية للصندوق")
            $table->text('notes')->nullable(); 
            
            // تاريخ الحركة المالي (يفضل فصله عن created_at لتوثيق تاريخ الحركة الفعلي)
            $table->date('transaction_date'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treasury_transactions');
    }
};