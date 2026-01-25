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
        Schema::table('persons', function (Blueprint $table) {
            $table->string('employment_status')->nullable()->change();
            $table->string('social_status')->nullable()->change();
            $table->string('housing_type')->nullable()->change();
            $table->string('housing_damage_status')->nullable()->change();
            $table->string('gender')->nullable()->change();
            $table->string('relationship')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            // Reverting to something generic or leaving as is if original was specific ENUMs
            // usually you'd want to revert to exactly what it was, but without Knowing 
            // the exact ENUM list, VARCHAR(255) is safer.
        });
    }
};
