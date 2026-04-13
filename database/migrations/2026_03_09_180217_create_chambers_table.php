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
        Schema::create('chambers', function (Blueprint $table) {
            $table->id();

            // ربط الغرفة التجارية بالشركة
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            // رقم عضوية الغرفة التجارية
            $table->string('chamber_number')->nullable();;

            // تاريخ الإصدار
            $table->date('issue_date')->nullable();;

            // تاريخ انتهاء العضوية
            $table->date('expiry_date')->nullable();;

            // مرفق شهادة الغرفة التجارية
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
        Schema::dropIfExists('chambers');
    }
};
