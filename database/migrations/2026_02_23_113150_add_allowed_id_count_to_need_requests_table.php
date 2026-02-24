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
        Schema::table('need_requests', function (Blueprint $table) {
            $table->unsignedInteger('allowed_id_count')->nullable()->after('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('need_requests', function (Blueprint $table) {
            $table->dropColumn('allowed_id_count');
        });
    }
};
