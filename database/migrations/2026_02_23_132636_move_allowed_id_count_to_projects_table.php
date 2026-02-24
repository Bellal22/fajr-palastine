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
        // 1. Remove from need_requests
        if (Schema::hasColumn('need_requests', 'allowed_id_count')) {
            Schema::table('need_requests', function (Blueprint $table) {
                $table->dropColumn('allowed_id_count');
            });
        }

        // 2. Add to need_request_projects
        Schema::table('need_request_projects', function (Blueprint $table) {
            $table->unsignedInteger('allowed_id_count')->nullable()->after('is_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('need_request_projects', function (Blueprint $table) {
            $table->dropColumn('allowed_id_count');
        });

        Schema::table('need_requests', function (Blueprint $table) {
            $table->unsignedInteger('allowed_id_count')->nullable()->after('reviewed_by');
        });
    }
};
