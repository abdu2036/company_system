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
    Schema::table('company_expenses', function (Blueprint $table) {
        // قمنا بإزالة after لضمان حقن الحقل بنجاح في نهاية الجدول بدون أخطاء
        $table->string('transaction_code')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_expenses', function (Blueprint $table) {
            // في حال رغبت في التراجع مستقبلاً يتم حذف الحقل فقط دون لمس الجدول
            $table->dropColumn('transaction_code');
        });
    }
};