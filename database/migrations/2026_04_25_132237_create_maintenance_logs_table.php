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
       Schema::create('maintenance_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('asset_id')->constrained()->onDelete('cascade');
    $table->string('maintenance_type'); // دورية، إصلاح، قطع غيار
    $table->decimal('cost', 10, 2)->default(0);
    $table->text('details')->nullable(); // ماذا حدث في الصيانة؟
    $table->date('start_date');
    $table->date('end_date')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
