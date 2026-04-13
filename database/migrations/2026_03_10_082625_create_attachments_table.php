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
    Schema::create('attachments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('company_id')->constrained()->onDelete('cascade'); // ربط بالشركة
        $table->string('file_type');      // نوع المستند (سجل، رخصة...)
        $table->string('file_path');      // المسار النهائي للملف
        $table->string('original_name');   // اسم الملف الأصلي عند الرفع
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
