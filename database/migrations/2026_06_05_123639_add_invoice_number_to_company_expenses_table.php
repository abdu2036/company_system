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
        // إضافة حقل رقم الفاتورة ويكون nullable لضمان سلامة البيانات القديمة
        $table->string('invoice_number')->nullable();
    });
}

public function down(): void
{
    Schema::table('company_expenses', function (Blueprint $table) {
        $table->dropColumn('invoice_number');
    });
}
};
