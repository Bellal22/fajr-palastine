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
        Schema::table('complaints', function (Blueprint $table) {
            $table->text('response')->nullable()->after('complaint_text');
            $table->timestamp('responded_at')->nullable()->after('response');
            $table->unsignedBigInteger('responded_by')->nullable()->after('responded_at');
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'rejected'])->default('pending')->after('responded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['response', 'responded_at', 'responded_by', 'status']);
        });
    }
};
