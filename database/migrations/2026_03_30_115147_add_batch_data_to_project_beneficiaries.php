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
            $table->integer('captured_qty')->nullable()->after('status');
            $table->text('external_notes')->nullable()->after('captured_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_beneficiaries', function (Blueprint $table) {
            $table->dropColumn(['captured_qty', 'external_notes']);
        });
    }
};
