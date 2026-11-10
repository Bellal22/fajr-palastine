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
        Schema::table('project_beneficiaries', function (Blueprint $table) {
            // Drop foreign keys that might depend on the unique index in some MySQL versions
            $table->dropForeign(['project_id']);
            $table->dropForeign(['person_id']);
            
            // Drop the unique constraint
            $table->dropUnique(['project_id', 'person_id']);
            
            // Re-create the foreign keys
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_beneficiaries', function (Blueprint $table) {
            $table->unique(['project_id', 'person_id']);
        });
    }
};
