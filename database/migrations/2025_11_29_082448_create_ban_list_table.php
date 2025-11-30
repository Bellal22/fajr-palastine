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
        Schema::create('ban_list', function (Blueprint $table) {
            $table->id();
            $table->string('id_num', 9)->unique(); // رقم الهوية
            $table->string('reason')->nullable();  // سبب الحظر (اختياري)
            $table->date('banned_at')->nullable(); // تاريخ الإضافة (اختياري)
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ban_list');
    }
};