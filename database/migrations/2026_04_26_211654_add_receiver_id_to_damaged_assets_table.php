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
        // تم تغيير اسم الجدول هنا من damaged_assets إلى assets
        Schema::table('assets', function (Blueprint $table) {
            // معرف الموظف (مدير المخزن) الذي استلم القطعة التالفة
            $table->unsignedBigInteger('received_by_emp_id')->nullable();
            $table->timestamp('received_at')->nullable(); // وقت الاستلام الفعلي
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['received_by_emp_id', 'received_at']);
        });
    }
};