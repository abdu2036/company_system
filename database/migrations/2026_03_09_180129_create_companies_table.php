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
        Schema::create('companies', function (Blueprint $table) {
            // 1. معرف فريد تلقائي لكل شركة (Primary Key)
            $table->id();

            // 2. اسم الشركة (سلسلة نصية بحد أقصى 255 حرف)
            $table->string('name');

            // 3. نشاط الشركة (نصي لوصف مجال العمل)
            $table->string('activity');

            // 4. عنوان الشركة (استخدام text يسمح بمساحة أكبر من string)
            $table->text('address');

            // 5. حالة الشركة: 1 تعني مفعلة و 0 تعني غير مفعلة (القيمة الافتراضية هي مفعل)
            $table->boolean('is_active')->default(true);
            // أضف هذه الأسطر هنا
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            // 6. تاريخ الإضافة وتاريخ آخر تحديث (يتم إنشاؤهما تلقائياً بواسطة Laravel)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
