<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('assets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('company_id')->constrained()->onDelete('cascade'); // ربط الأصل بالشركة
        
        $table->string('asset_code')->unique(); // كود المنظومة التلقائي
        $table->string('serial_number')->nullable(); // سيريال المصنع
        $table->string('name');
        $table->string('category'); // تصنيف (أثاث، إلكترونيات...)
        $table->string('location'); // الموقع الحالي
        
        // الحالة (جديد، مستعمل، تحت الصيانة، تالف)
        $table->enum('status', ['جديد', 'مستعمل', 'تحت الصيانة', 'تالف'])->default('جديد');
        
        $table->date('purchase_date')->nullable(); // تاريخ الشراء
        $table->decimal('purchase_price', 10, 2)->default(0); // سعر الشراء
        
        // بيانات الصيانة (تظهر فقط عند الحاجة)
        $table->text('fault_description')->nullable(); // وصف المشكلة
        $table->decimal('maintenance_cost', 10, 2)->default(0); // تكلفة الصيانة
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
