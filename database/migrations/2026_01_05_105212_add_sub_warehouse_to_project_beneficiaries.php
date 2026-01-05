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
            $table->foreignId('sub_warehouse_id')
                ->nullable()
                ->after('quantity')
                ->constrained('sub_warehouses')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_beneficiaries', function (Blueprint $table) {
            $table->dropForeign(['sub_warehouse_id']);
            $table->dropColumn('sub_warehouse_id');
        });
    }
};
