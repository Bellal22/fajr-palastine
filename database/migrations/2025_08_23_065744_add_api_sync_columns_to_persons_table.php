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
            Schema::table('persons', function (Blueprint $table) {
                $table->timestamp('api_synced_at')->nullable()->after('updated_at');
                $table->string('api_sync_status')->nullable()->after('api_synced_at'); // success, failed, pending
                $table->text('api_sync_error')->nullable()->after('api_sync_status');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn(['api_synced_at', 'api_sync_status', 'api_sync_error']);
        });
    }
};
