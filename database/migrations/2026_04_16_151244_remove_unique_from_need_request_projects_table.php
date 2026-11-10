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
        Schema::table('need_request_projects', function (Blueprint $table) {
            // Drop foreign key first if it exists
            $table->dropForeign(['project_id']);
            $table->dropUnique(['project_id']);
            $table->index('project_id');
            // Re-add foreign key
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('need_request_projects', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->unique('project_id');
        });
    }
};
