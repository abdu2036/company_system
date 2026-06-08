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
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            // ربط البند بشركة معينة وحذفه تلقائياً في حال حُذفت الشركة
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('name'); // اسم البند (مثال: رسوم غرف تجارية، رسوم بلدية)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
