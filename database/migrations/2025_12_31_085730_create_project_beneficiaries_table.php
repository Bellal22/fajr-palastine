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
        Schema::create('project_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('persons')->cascadeOnDelete(); // تغيير هنا
            $table->string('status')->default('غير مستلم'); // حالة الاستلام
            $table->text('notes')->nullable(); // الملاحظات
            $table->date('delivery_date')->nullable(); // تاريخ التسليم
            $table->timestamps();

            $table->unique(['project_id', 'person_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_beneficiaries');
    }
};
