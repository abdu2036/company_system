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
    // تعديل جدول الأصول (هذا صحيح غالباً)
    Schema::table('assets', function (Blueprint $table) {
        $table->unsignedBigInteger('added_by_emp_id')->nullable()->after('id');
    });

    // التعديل المهم: استخدام الاسم الصحيح لجدول الصيانة
    Schema::table('maintenance_logs', function (Blueprint $table) {
        $table->unsignedBigInteger('technician_id')->nullable()->after('asset_id');
    });

    // تأكد من اسم جدول التوالف أيضاً من القائمة (ربما يكون assets أو اسم آخر)
    // سأفترض حالياً أنك ستضيفه لجدول الأصول كحالة أو حقل استلام
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            //
        });
    }
};
