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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('employment_status', ['موظف', 'عامل', 'لا يعمل'])->default('لا يعمل')->after('housing_damage_status'); // حالة العمل
            $table->enum('person_status', ['فعال', 'غير فعال'])->default('غير فعال')->after('employment_status'); // حالة الشخص
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['employment_status', 'person_status']);
        });
    }
};
