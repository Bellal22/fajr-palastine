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
            $table->integer('id_num')->nullable();
            $table->string('first_name');
            $table->string('father_name');
            $table->string('grandfather_name');
            $table->string('family_name');
            $table->date('dob')->nullable();
            $table->string('social_status')->nullable();
            $table->string('city')->nullable();
            $table->string('current_city')->nullable();
            $table->string('neighborhood')->nullable();
            $table->longText('landmark')->nullable();
            $table->string('housing_type')->nullable();
            $table->string('housing_damage_status')->nullable();
            $table->integer('relatives_count')->nullable();
            $table->boolean('has_condition')->default(false);
            $table->longText('condition_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id_num',
                'first_name',
                'father_name',
                'grandfather_name',
                'family_name',
                'dob',
                'social_status',
                'city',
                'current_city',
                'neighborhood',
                'landmark',
                'housing_type',
                'housing_damage_status',
                'relatives_count',
                'has_condition',
                'condition_description');
        });
    }
};
